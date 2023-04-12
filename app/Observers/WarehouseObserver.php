<?php

namespace App\Observers;

use App\Models\Warehouse\Warehouse;
use App\Repositories\Warehouse\WarehouseLogRepository;
use App\Enums\WarehouseLogAction;

class WarehouseObserver
{
    protected $warehouselogRepository;

    public function __construct()
    {
        $this->warehouselogRepository  = new WarehouselogRepository;
    }

    public function created(Warehouse $warehouse)
    {
        $this->warehouselogRepository->register(WarehouseLogAction::Create, $warehouse);
    }

    public function updated(Warehouse $warehouse)
    {
        $changes = array_keys($warehouse->getChanges());

        if (in_array("idstatus", $changes))
        {
            $this->warehouselogRepository->register(WarehouseLogAction::ChangeStatus, $warehouse);
        }
        else
        {
            $this->warehouselogRepository->register(WarehouseLogAction::Edit, $warehouse);
        }
    }

    public function deleted(Warehouse $warehouse)
    {
        $this->warehouselogRepository->register(WarehouseLogAction::Delete, $warehouse);
    }

    public function restored(Warehouse $warehouse)
    {
        //
    }

    public function forceDeleted(Warehouse $warehouse)
    {
        //
    }
}
