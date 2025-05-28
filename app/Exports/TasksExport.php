<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class TasksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tasks;

    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Return the collection of tasks to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->tasks;
    }

    /**
     * Define the headings for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tiêu đề',
            'Bộ môn',
            'Người được giao',
            'Thời hạn',
            'Trạng thái',
        ];
    }

    /**
     * Map each task to an array of values for the Excel row.
     *
     * @param mixed $task
     * @return array
     */
    public function map($task): array
    {
        return [
            $task->title,
            $task->department->name ?? 'Không xác định',
            $task->assignedUsers->pluck('name')->implode(', '),
            $task->due_date->format('d/m/Y'),
            $task->status,
        ];
    }
}