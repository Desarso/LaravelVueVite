<?php

namespace App\Repositories\Warehouse;

use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseLog;
use Illuminate\Support\Facades\Auth;
use App\Enums\WarehouseLogAction;
use App\Enums\WarehouseStatus;
use Session;
use Carbon\Carbon;

class WarehouseLogRepository
{
    public function register($action, $model)
    {
        $data = collect($model)->except(['created_at', 'updated_at', 'deleted_at']);

        switch($action)
        {
            case WarehouseLogAction::Create:

                $log  = ["action" => $action, "data" => json_encode($data), "idwarehouse" => $model->id, "idstatus" => WarehouseStatus::Pending, "iduser" => Auth::id()];
                break;

            case WarehouseLogAction::Edit:

                $data = collect($model->getChanges())->except('updated_at');
                $log  = ["action" => $action, "data" => json_encode($data), "idwarehouse" => $model->id, "idstatus" => $model->idstatus, "iduser" => Auth::id()];
                break;

            case WarehouseLogAction::Delete:

                $log  = ["action" => $action, "data" => json_encode($data), "idwarehouse" => $model->id, "idstatus" => $model->idstatus, "iduser" => Auth::id()];
                break;

            case WarehouseLogAction::ChangeStatus:

                $data = collect($model->getChanges())->except('updated_at');
                $log  = ["action" => $action, "data" => json_encode($data), "idwarehouse" => $model->id, "idstatus" => $model->idstatus, "iduser" => Auth::id()];
                break;
        }

        WarehouseLog::create($log);
    }
}