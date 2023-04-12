<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\TicketType;

class TicketTypeRepository
{
    private $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        return TicketType::orderBy('name', 'ASC')
                            ->get(['id', 'name', 'description', 'idteam', 'iscleaningtask', 'icon', 'color', 'hassla', 'template', 'showingrid']);
    }

    public function getList()
    {
        return TicketType::orderBy('name', 'ASC')->get(['id as value', 'name as text', 'icon', 'color', 'iscleaningtask', 'template']);
    }

    public function create($request)
    {
        $ticketType = TicketType::create($request->all());

        return response()->json(['success' => true, 'model' => $ticketType]);
    }

    public function update($request)
    {       
        $ticketType = TicketType::find($request->id);

        $ticketType->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $ticketType]);
    }

    public function delete($request)
    {
        $ticketType = TicketType::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($ticketType, ['items']);

        if(!$hasRelations)
        {
            $ticketType->delete();

            return response()->json(['success' => true, 'model' => $ticketType]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $ticketType, 'relations' => $hasRelations]);
        }
    }   
    
    public function createOnFly($request)
    {
        $model = TicketType::create($request->all());

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito', 'model' => $model->refresh()]);
    }

    public function restore($request)
    {
        $ticketType = TicketType::withTrashed()->findOrFail($request->id);

        $ticketType->restore();

        return response()->json(['success' => true, 'model' => $ticketType]);
    }
}