<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['author', 'client'])->default('client');
            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
        });

        User::query()->firstOrCreate(
            ['email' => env('AUTHOR_EMAIL', 'owner@timesheet.local')],
            [
                'name' => env('AUTHOR_NAME', 'Owner'),
                'password' => Hash::make(env('AUTHOR_PASSWORD', 'password123')),
                'role' => 'author',
                'email_verified_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
