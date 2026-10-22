<?php

namespace Tests\Feature;

use App\Exports\TimeLogExport;
use App\Models\Project;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ReportDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_and_excel_show_description_text_and_never_interpret_it_as_a_formula(): void
    {
        $project = Project::create(['name' => 'Example organization', 'status' => 'active']);
        $log = TimeLog::create([
            'session_id' => Str::uuid()->toString(), 'project_id' => $project->id, 'status' => 'completed',
            'clock_in' => now()->subDay()->setTime(9, 0), 'clock_out' => now()->subDay()->setTime(10, 0), 'total_minutes' => 60,
            'work_description' => '<p>=2+2 &amp; design&nbsp;review</p><ul><li>Test &lt;header&gt;</li></ul>',
        ]);
        $date = now()->subDay()->toDateString();
        $expected = "=2+2 & design review\n• Test <header>";
        $this->actingAs(User::where('role', 'author')->first())->getJson("/api/timesheet/report?start_date={$date}&end_date={$date}&project_id={$project->id}")
            ->assertOk()->assertJsonPath('data.logs.0.work_description_text', $expected);
        $bytes = Excel::raw(new TimeLogExport(collect([$log]), $date, $date), \Maatwebsite\Excel\Excel::XLSX);
        $path = tempnam(sys_get_temp_dir(), 'report-text-');
        file_put_contents($path, $bytes);
        try {
            $sheet = IOFactory::load($path)->getActiveSheet();
            $matches = [];
            foreach ($sheet->getRowIterator() as $row) {
                $cell = $sheet->getCell('D'.$row->getRowIndex());
                if ($cell->getValue() === $expected) {
                    $matches[] = $cell;
                }
            }
            $this->assertCount(1, $matches);
            $this->assertSame('s', $matches[0]->getDataType());
        } finally {
            unlink($path);
        }
    }
}
