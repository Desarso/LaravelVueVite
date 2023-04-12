<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Warehouse\WarehouseNoteRepository;

class WarehouseNoteController extends Controller
{
    protected $warehouseNoteRepository;

    public function __construct()
    {
        $this->warehouseNoteRepository = new WarehouseNoteRepository;
    }

    public function getNotes(Request $request)
    {
        return $this->warehouseNoteRepository->getNotes($request);
    }

    public function create(Request $request)
    {
        return $this->warehouseNoteRepository->create($request);
    }
}
