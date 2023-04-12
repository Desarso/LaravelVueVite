<?php

namespace App\Repositories\Reports;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketNote;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;
use App\Models\Spot;

class ReportChecklistSummaryRepository
{
    protected $spotRepository;
    protected $spot;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
        $this->spot =  new Spot;
    }

    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);

        $data = DB::table('wh_ticket as t')
                   ->join('wh_ticket_checklist as tc', 't.id', '=', 'tc.idticket')
                   ->join('wh_spot as s', 's.id', '=', 't.idspot')
                   ->when(!is_null($request->idspot), function ($query) use ($spots){
                        return $query->whereIn('t.idspot', $spots);
                   })
                   ->where('tc.idchecklist', $request->idchecklist)
                   ->whereBetween('t.created_at', [$start, $end])
                   ->whereNull('t.deleted_at')
                   ->select('tc.id', 't.id as idticket', 's.name as spot', 't.created_by', 't.created_at', 'tc.options')
                   ->get();

        return $data;
    }

    public function getDataChecklistSummary($request)
    {
        $data = $this->getData($request);

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $result = collect();

        $columns = [];

        switch ($request->idchecklist)
        {
            case 18:

                $columns = ["SKU", "Descripcion", "Precio_Etiqueta", "Precio_ERP", "Diferencia"];

                foreach ($data as $item)
                {
                    $collect = collect(json_decode($item->options));

                    $collect = $collect->whereIn('optiontype', [3, 4, 9, 10]);

                    $precioEtiqueta = $collect->firstWhere('position', 4)->value;
                    $precioErp      = $collect->firstWhere('position', 5)->value;

                    $precioEtiqueta = is_null($precioEtiqueta) ? 0 : $precioEtiqueta;
                    $precioErp      = is_null($precioErp) ? 0 : $precioErp;

                $object = ['Spot'=> $item->spot, 'SKU' => $collect[0]->value, 'Descripcion' => $collect[1]->value, 'Precio_Etiqueta' => $precioEtiqueta, 'Precio_ERP' => $precioErp, 'Diferencia' => ($precioEtiqueta - $precioErp)];
                
                    $result->push($object);
                }

                break;

            default:

                foreach ($data as $item)
                {
                    $collect = collect(json_decode($item->options));

                    $collect = $collect->whereIn('optiontype', [1, 2, 3, 4, 5, 9, 10]);

                    $columns = $collect->pluck('name')->toArray();

                    $row = [];

                    foreach ($columns as $column)
                    {
                        $row[$this->cleanString($column)] = $this->getValueFromCollection($column, $collect, $checklistData);
                    }

                    $row['Spot'] = $item->spot;

                    array_unshift($columns, "Spot");

                    $result->push($row);
                }

                break;
        }

        array_walk($columns, function(&$v){
            $v = $this->cleanString($v);
        });

        return ["columns" => $columns, "data" => $result];
    }

    private function getValueFromCollection($key, $collection, $checklistData)
    {
        $option = $collection->where("name", $key)->first();

        $value = "";

        switch ($option->optiontype)
        {
            case 1:
                $value = (int)$option->value == 1 ? "Si" : "No";
                break;

            case 2:
                $value = $this->getTextFromJson($option->value, $option->iddata, $checklistData);
                break;

            case 5:
                $value = $this->getTextFromJson($option->value, $option->iddata, $checklistData);
                break;
            
            default:
                $value = $option->value;
                break;
        }

        return $value;
    }

    private function getTextFromJson($value, $iddata, $checklistData)
    {
        if(is_null($value) || $value == "null" || is_null($iddata)) return "";

        $result = json_decode($checklistData->firstWhere('id', $iddata)->data);

        $data = collect($result);

        return $data->firstWhere('value', $value)->text;
    }

    private function cleanString($cadena)
    {
        $cadena = $this->deleteAccent($cadena);

        $cadena = str_replace(' ', '_', $cadena); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-_]/', '', $cadena); // Removes special chars.
    }

    private function deleteAccent($cadena)
    {
        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        //$cadena = utf8_encode($cadena);
    
        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
    
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
    
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
    
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
    
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
    
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );
    
        return $cadena;
    }
}