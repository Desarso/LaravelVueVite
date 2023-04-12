<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MainWorkPlanExport implements WithMultipleSheets 
{
    protected $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        return [
            new WorkPlanExport($this->request),
            new WorkPlanTasksExport($this->request),
        ];
    }
}
