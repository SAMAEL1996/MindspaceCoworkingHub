<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ExportModule implements FromArray, WithHeadings
{
    protected $headings;
    protected $records;

    public function __construct(array $headings, array $records)
    {
        $this->headings = $headings;
        $this->records = $records;
    }

    public function array(): array
    {
        return $this->records;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
