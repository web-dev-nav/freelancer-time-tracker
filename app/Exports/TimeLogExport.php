<?php

// app/Exports/TimeLogExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TimeLogExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $logs;
    protected $startDate;
    protected $endDate;
    protected $totalHours;
    protected $totalMinutes;

    public function __construct($logs, $startDate, $endDate)
    {
        $this->logs = $logs;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalMinutes = $logs->sum('total_minutes');
        $this->totalHours = round($this->totalMinutes / 60, 2);
    }

    public function array(): array
    {
        $data = [];
        
        // Header section
        $data[] = ['PROFESSIONAL TIMESHEET'];
        $data[] = [''];
        $data[] = ['Period:', "{$this->startDate} to {$this->endDate}"];
        $data[] = ['Total Hours:', $this->totalHours];
        $data[] = ['Total Sessions:', $this->logs->count()];
        $data[] = ['Generated:', now()->format('M j, Y g:i A')];
        $data[] = [''];

        // Group logs by date
        $groupedLogs = $this->logs->groupBy(function($log) {
            return $log->clock_in->format('Y-m-d');
        });

        foreach ($groupedLogs as $date => $dailyLogs) {
            $dayTotal = $dailyLogs->sum('total_minutes');
            $dayHours = round($dayTotal / 60, 2);
            
            // Date header
            $data[] = [
                strtoupper($dailyLogs->first()->clock_in->format('l, F j, Y')),
                '',
                '',
                '',
                "Day Total: {$dayHours} hrs"
            ];
            
            // Daily entries
            foreach ($dailyLogs as $log) {
                $data[] = [
                    $log->clock_in_time,
                    $log->clock_out_time,
                    $log->formatted_duration,
                    $log->work_description,
                    $log->project_name ?: 'General'
                ];
            }
            
            $data[] = ['', '', '', '', '']; // Empty row between days
        }

        // Summary section
        $data[] = ['', '', '', '', ''];
        $data[] = ['SUMMARY'];
        $data[] = ['Total Working Days:', $groupedLogs->count()];
        $data[] = ['Total Sessions:', $this->logs->count()];
        $data[] = ['Total Hours:', $this->totalHours];
        $data[] = ['Average Hours/Day:', $groupedLogs->count() > 0 ? round($this->totalHours / $groupedLogs->count(), 2) : 0];

        return $data;
    }

    public function headings(): array
    {
        return [
            [], [], [], [], [], [], [],
            ['Start Time', 'End Time', 'Duration', 'Work Description', 'Project']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Start Time
            'B' => 15,  // End Time  
            'C' => 12,  // Duration
            'D' => 50,  // Work Description
            'E' => 20,  // Project
        ];
    }

    public function title(): string
    {
        return 'Timesheet ' . $this->startDate . ' to ' . $this->endDate;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Main header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 18,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '2c3e50']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            
            // Period info
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['italic' => true, 'color' => ['rgb' => '666666']]],
            
            // Column headers
            8 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '34495e']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }
}