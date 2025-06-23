<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class TimesheetExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $entries;
    protected $startDate;
    protected $endDate;
    protected $totalHours;

    public function __construct($entries, $startDate, $endDate)
    {
        $this->entries = $entries;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalHours = $entries->sum('duration');
    }

    public function array(): array
    {
        $data = [];
        
        // Header information
        $data[] = ['Professional Timesheet'];
        $data[] = ['Period:', "{$this->startDate} - {$this->endDate}"];
        $data[] = ['Total Hours:', $this->totalHours];
        $data[] = [''];

        // Data rows
        foreach ($this->entries as $entry) {
            $data[] = [
                $entry->work_date->format('D, M j, Y'),
                $entry->start_time->format('H:i'),
                $entry->end_time->format('H:i'),
                $entry->task_description,
                $entry->duration
            ];
        }

        // Total row
        $data[] = ['', '', '', 'TOTAL:', $this->totalHours];

        return $data;
    }

    public function headings(): array
    {
        return [
            [], [], [], [],
            ['Date', 'Start Time', 'End Time', 'Task / Activity', 'Duration (hrs)']
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 15,
            'D' => 60,
            'E' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E9ECEF']]],
        ];
    }
}