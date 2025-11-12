<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeLog;
use Carbon\Carbon;

class TimeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read the timesheet data from JSON file
        $jsonPath = base_path('timesheet_data.json');
        $timezone = config('app.timezone', 'UTC');
        
        if (!file_exists($jsonPath)) {
            $this->command->info('timesheet_data.json file not found. Skipping TimeLogSeeder.');
            return;
        }
        
        $timeLogsData = json_decode(file_get_contents($jsonPath), true);
        
        if (!$timeLogsData) {
            $this->command->info('No valid data found in timesheet_data.json. Skipping TimeLogSeeder.');
            return;
        }
        
        $this->command->info('Seeding ' . count($timeLogsData) . ' time log entries...');
        
        foreach ($timeLogsData as $logData) {
            try {
                // Create clock-in datetime in application timezone, then convert to UTC
                $clockIn = Carbon::createFromFormat('Y-m-d H:i', $logData['date'] . ' ' . $logData['start_time'], $timezone)
                                ->setTimezone('UTC');
                
                // Create clock-out datetime in application timezone, then convert to UTC
                $clockOut = Carbon::createFromFormat('Y-m-d H:i', $logData['date'] . ' ' . $logData['end_time'], $timezone)
                                 ->setTimezone('UTC');
                
                // Handle overnight sessions
                if ($clockOut->lt($clockIn)) {
                    $clockOut->addDay();
                }
                
                // Calculate total minutes
                $totalMinutes = $clockIn->diffInMinutes($clockOut);
                
                // Generate a unique session ID
                $sessionId = 'session_' . $clockIn->timestamp . '_' . uniqid();
                
                // Create the time log entry
                TimeLog::create([
                    'session_id' => $sessionId,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'total_minutes' => $totalMinutes,
                    'work_description' => $logData['work_description'],
                    'project_name' => $logData['project_name'],
                    'status' => 'completed',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'TimeLogSeeder',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
            } catch (\Exception $e) {
                $this->command->error('Error creating time log for date ' . $logData['date'] . ': ' . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info('TimeLogSeeder completed successfully!');
    }
}
