<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\SchedulerLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class InvoiceSchedulingTest extends TestCase
{
    use RefreshDatabase;

    private function invoice(array $attributes = []): Invoice
    {
        $organization = Project::create(['name' => 'Organization', 'status' => 'active']);

        return Invoice::create($attributes + [
            'invoice_number' => 'TEST-'.uniqid(), 'project_id' => $organization->id,
            'client_name' => 'Client', 'client_email' => 'client@example.test',
            'invoice_date' => now()->toDateString(), 'due_date' => now()->addDays(14)->toDateString(),
        ]);
    }

    private function fakePdf(): void
    {
        Pdf::shouldReceive('loadView')->andReturn(Mockery::mock(\Barryvdh\DomPDF\PDF::class)->shouldReceive('output')->andReturn('PDF')->getMock());
    }

    public function test_offset_schedule_is_saved_in_the_application_timezone(): void
    {
        config(['app.timezone' => 'America/Toronto']);
        $this->travelTo(now('America/Toronto')->setDate(2026, 11, 5)->setTime(8, 0));
        $invoice = $this->invoice();
        $this->actingAs(User::where('role', 'author')->first())->postJson("/api/invoices/{$invoice->id}/send-email", [
            'email' => 'client@example.test', 'scheduled_send_at' => '2026-11-05T15:37:00Z',
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertSame('2026-11-05 10:37:00', $invoice->fresh()->getRawOriginal('scheduled_send_at'));
        $this->assertNull($invoice->fresh()->sent_at);
    }

    public function test_failed_delivery_stays_unsent_and_is_retried_after_backoff(): void
    {
        $this->travelTo(now()->startOfMinute());
        $invoice = $this->invoice(['scheduled_send_at' => now()->subMinute()]);
        $scheduled = $invoice->scheduled_send_at->copy();
        $this->fakePdf();
        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $this->artisan('invoices:send-scheduled')->assertExitCode(1);
        $invoice->refresh();
        $this->assertSame('draft', $invoice->status);
        $this->assertNull($invoice->sent_at);
        $this->assertNotNull($invoice->scheduled_send_at);
        $this->assertSame(1, $invoice->scheduled_send_attempts);
        $this->assertSame('SMTP unavailable', $invoice->last_send_error);
        $this->artisan('invoices:send-scheduled')->assertExitCode(0); // No early retry.
        $this->travel(6)->minutes();
        Mail::shouldReceive('send')->once()->andReturnNull();
        $this->artisan('invoices:send-scheduled')->assertExitCode(0);
        $invoice->refresh();
        $this->assertSame('sent', $invoice->status);
        $this->assertNotNull($invoice->sent_at);
        $this->assertNull($invoice->scheduled_send_at);
        $this->assertNull($invoice->last_send_error);
        $this->assertTrue(SchedulerLog::where('status', 'sent')->first()->scheduled_at->equalTo($scheduled));
    }

    public function test_previously_sent_invoice_can_be_scheduled_again_and_paid_invoices_are_excluded(): void
    {
        $invoice = $this->invoice(['status' => 'sent', 'sent_at' => now()->subDay(), 'scheduled_send_at' => now()->subMinute()]);
        $paid = $this->invoice(['status' => 'paid', 'scheduled_send_at' => now()->subMinute()]);
        $this->fakePdf();
        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andReturnNull();
        $this->artisan('invoices:send-scheduled')->assertExitCode(0);
        $this->assertNull($invoice->fresh()->scheduled_send_at);
        $this->assertNotNull($paid->fresh()->scheduled_send_at);
    }

    public function test_reminder_must_follow_the_invoice_and_clients_cannot_schedule(): void
    {
        $invoice = $this->invoice();
        $this->actingAs(User::where('role', 'author')->first())->postJson("/api/invoices/{$invoice->id}/send-email", [
            'email' => 'client@example.test', 'scheduled_send_at' => now()->addDays(2)->toIso8601String(),
            'reminder_send_at' => now()->addDay()->toIso8601String(),
        ])->assertUnprocessable();
        $client = User::create(['name' => 'Client', 'email' => 'client2@example.test', 'password' => 'test', 'role' => 'client']);
        $this->actingAs($client)->postJson("/api/invoices/{$invoice->id}/send-email", ['email' => 'client@example.test'])->assertForbidden();
    }

    public function test_future_invoices_and_exhausted_retries_are_not_sent(): void
    {
        $this->invoice(['scheduled_send_at' => now()->addHour()]);
        $failed = $this->invoice(['scheduled_send_at' => now()->subHour()]);
        $failed->scheduled_send_attempts = 5;
        $failed->save();
        Mail::shouldReceive('mailer')->never();
        $this->artisan('invoices:send-scheduled')->assertExitCode(0);
    }

    public function test_a_delivery_lock_prevents_a_second_worker_and_manual_send(): void
    {
        $invoice = $this->invoice(['scheduled_send_at' => now()->subMinute()]);
        $lock = Cache::lock('invoice-delivery-'.$invoice->id, 600);
        $lock->get();
        try {
            Mail::shouldReceive('mailer')->never();
            $this->artisan('invoices:send-scheduled')->assertExitCode(0);
            $this->actingAs(User::where('role', 'author')->first())->postJson("/api/invoices/{$invoice->id}/send-email", ['email' => 'client@example.test'])->assertStatus(409);
            $this->assertNull($invoice->fresh()->sent_at);
        } finally {
            $lock->release();
        }
    }

    public function test_cancelling_a_failed_schedule_clears_retry_state_and_unbilled_route_is_reachable(): void
    {
        $invoice = $this->invoice(['scheduled_send_at' => now()->subMinute()]);
        $invoice->last_send_error = 'SMTP unavailable';
        $invoice->scheduled_send_attempts = 5;
        $invoice->save();
        $this->actingAs(User::where('role', 'author')->first());
        $this->postJson("/api/invoices/{$invoice->id}/cancel-schedule", ['kind' => 'send'])->assertOk();
        $this->assertNull($invoice->fresh()->last_send_error);
        $this->assertNull($invoice->fresh()->scheduled_send_at);
        $this->getJson('/api/invoices/unbilled-logs')->assertOk();
    }
    public function test_an_immediate_delivery_failure_does_not_mark_the_invoice_sent(): void
    {
        $invoice = $this->invoice();
        $this->fakePdf();
        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $this->actingAs(User::where('role', 'author')->first())->postJson("/api/invoices/{$invoice->id}/send-email", ['email' => 'client@example.test'])->assertStatus(500);
        $this->assertSame('draft', $invoice->fresh()->status);
        $this->assertNull($invoice->fresh()->sent_at);
    }

}
