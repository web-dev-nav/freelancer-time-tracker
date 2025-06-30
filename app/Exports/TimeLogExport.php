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
        
        // Header section with improved spacing
        $data[] = ['FREELANCER TIMESHEET REPORT'];
        $data[] = [''];
        $data[] = ['Report Period:', "{$this->startDate} to {$this->endDate}"];
        $data[] = ['Total Hours Worked:', $this->totalHours . ' hours'];
        $data[] = ['Total Work Sessions:', $this->logs->count()];
        $data[] = ['Report Generated:', now('America/Toronto')->format('M j, Y g:i A T')];
        $data[] = [''];
        $data[] = [''];

        // Group logs by date
        $groupedLogs = $this->logs->groupBy(function($log) {
            return $log->clock_in->format('Y-m-d');
        });

        foreach ($groupedLogs as $date => $dailyLogs) {
            $dayTotal = $dailyLogs->sum('total_minutes');
            $dayHours = round($dayTotal / 60, 2);
            
            // Date header with better formatting
            $data[] = [
                strtoupper($dailyLogs->first()->clock_in->format('l, F j, Y')),
                '',
                '',
                "Daily Total: {$dayHours} hrs"
            ];
            
            // Daily entries without project column
            foreach ($dailyLogs as $log) {
                $data[] = [
                    $log->clock_in_time,
                    $log->clock_out_time,
                    $log->formatted_duration,
                    $log->work_description
                ];
            }
            
            $data[] = ['', '', '', '']; // Empty row between days
        }

        // Enhanced summary section
        $data[] = ['', '', '', ''];
        $data[] = ['TIMESHEET SUMMARY', '', '', ''];
        $data[] = ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', '', '', ''];
        $data[] = ['Total Working Days:', $groupedLogs->count() . ' days'];
        $data[] = ['Total Work Sessions:', $this->logs->count() . ' sessions'];
        $data[] = ['Total Hours Worked:', $this->totalHours . ' hours'];
        $data[] = ['Average Hours per Day:', $groupedLogs->count() > 0 ? round($this->totalHours / $groupedLogs->count(), 2) . ' hours' : '0 hours'];
        $data[] = ['Average Session Length:', $this->logs->count() > 0 ? round($this->totalMinutes / $this->logs->count()) . ' minutes' : '0 minutes'];

        return $data;
    }

    public function headings(): array
    {
        return [
            [], [], [], [], [], [], [], [],
            ['Start Time', 'End Time', 'Duration', 'Work Description']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // Start Time
            'B' => 18,  // End Time  
            'C' => 15,  // Duration
            'D' => 60,  // Work Description (expanded)
        ];
    }

    public function title(): string
    {
        return 'Freelancer Timesheet ' . $this->startDate . ' to ' . $this->endDate;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Main header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 20,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '1a73e8']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            
            // Period info styling
            3 => ['font' => ['bold' => true, 'size' => 11]],
            4 => ['font' => ['bold' => true, 'size' => 11]],
            5 => ['font' => ['bold' => true, 'size' => 11]],
            6 => ['font' => ['italic' => true, 'color' => ['rgb' => '666666'], 'size' => 10]],
            
            // Column headers
            9 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '4285f4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            
            // Summary section header
            'A' => ['alignment' => ['wrapText' => true]],
            'D' => ['alignment' => ['wrapText' => true]]
        ];
    }
}