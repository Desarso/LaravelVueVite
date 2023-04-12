<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Checklist;
use App\Models\Item;
use App\Models\TicketType;
use App\Models\ChecklistOption;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Http\File;

class FormsRepository
{
    public function getAll()
    {
        $data = Checklist::orderBy('name', 'ASC')->get();    
        
        $data->map(function ($checklist){
            $checklist->idtype = $checklist->items->count() > 0 ? $checklist->items->first()->idtype : 0;
            return $checklist;
        });

        return $data;
    }

    public function getList()
    {        
        return Checklist::get(['id as value', 'name as text']);
    }

    public function getDetails($request)
    {        
        return ChecklistOption::with('children')->where('idchecklist', $request->idchecklist)->whereNull('idparent')->orderBy('position', 'asc')->get();
    }

    public function create($request)
    {
        $request["type"] = 1;
        $form = Checklist::create($request->all());

        $ticketType = TicketType::find($request->idtype);

        $dataItem = ["name" => $request->name, "idtype" => $request->idtype, "idteam" => $ticketType->idteam, "idchecklist" => $form->id];

        Item::create($dataItem);

        return response()->json(['success' => true, 'model' => $form]);
    }

    public function update($request)
    {
        $model = Checklist::find($request->id);

        $model->fill($request->all())->save();

        $item = Item::find($model->items->first()->id);

        $ticketType = TicketType::find($request->idtype);

        $dataItem = ["name" => $request->name, "idtype" => $request->idtype, "idteam" => $ticketType->idteam];

        $item->fill($dataItem)->save();

        return response()->json(['success' => true, 'model' => $model]);
    }

    public function delete($request)
    {
        $model = Checklist::findOrFail($request->id);
        $model->delete();

        return response()->json(['success' => true, 'model' => $model]);
    }

    public function disable($request)
    {
        $model = Checklist::findOrFail($request->id);
        $model->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $model]);
    }

    public function addFormItem($request)
    {
        if($request->position == 0)
        {
            $max = ChecklistOption::where('idchecklist', $request->idchecklist)->max('position');
            $request['position'] = ($max + 1);
        }

        if($request->optiontype == 2 || $request->optiontype == 5)
        {
            $request['iddata'] = $this->getDefaultChecklistData();
        }

        $request['properties'] = $this->getDefaultProperties($request);

        $checklistOption = ChecklistOption::create($request->all());

        $this->sortFormItems($request);

        return response()->json(['success' => true, 'model' => $checklistOption]);
    }

    public function deleteFormItem($request)
    {
        $model = ChecklistOption::findOrFail($request->id);
        $model->delete();

        $this->sortFormItems($request);

        return response()->json(['success' => true]);
    }

    public function updateFormItem($request)
    {
        $data = $request->except(['id', 'name', 'iddata', 'showinreport']);

        $model = ChecklistOption::find($request->id);
        $model->name = $request->name;

        if(count($data) > 0)
        {
            $model->properties = $this->getFormatProperties($model, $data);
        }

        if($request->has('iddata'))
        {
            $model->iddata = $request->iddata;
        }

        if($request->has('showinreport'))
        {
            $model->showinreport = $request->showinreport;
        }

        $model->save();
    }

    public function sortFormItems($request)
    {
        $group = null;

        if($request->has('data'))
        {
            $data = json_decode($request->data);
        }
        else
        {
            $data = ChecklistOption::when(is_null($request->idparent), function ($query) use ($request) {
                                        $query->whereNull('idparent')->where('idchecklist', $request->idchecklist);
                                    }, function ($query) use ($request) {
                                        $query->where('idparent', $request->idparent);
                                    })
                                    ->orderBy('position', 'asc')
                                    ->get();
        }

        foreach($data as $key => $item)
        {
            $model = ChecklistOption::find($item->id);
            $model->position = $key = $key + 1;
            if ($model->isgroup) $group = ($group == null ? 1 : $group+ 1);
            $model->group = $group;
            $model->save();
        }
    }

    public function saveImage($request)
    {
        $file = $request->file('files');

        $client = env('DO_SPACES_HOTEL', 'whagons');
        $path   = env('DO_SPACES_ROUTE', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/');

        $file_name = $client . "/" . uniqid() . "." . $file->getClientOriginalExtension();

        $image_resize = Image::make($file)->resize(500, 500, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');
        
        Storage::disk('spaces')->put($file_name, (string) $image_resize, 'public');
        
        $full_url = $path . $file_name;

        $model = ChecklistOption::find($request->id);
        $model->properties = json_encode(["value" => $full_url, "size" => $image_resize->filesize(), "extension" => "jpg"]);
        $model->save();

        return response()->json(["success" => true, "url" => $full_url, "size" => $image_resize->filesize(), "extension" => "jpg"]);
    }

    public function removeImage($request)
    {
        $full_url = explode('/', $request['fileNames']);

        $deleted = Storage::disk('spaces')->delete($full_url[3] . '/' . $full_url[4]);

        $model = ChecklistOption::find($request->id);
        $model->properties = json_encode(["value" => "", "size" => "", "extension" => ""]);
        $model->save();

        return response()->json(["success" => true]);
    }

    public function getDefaultProperties($request)
    {
        $properties = null;

        switch($request->optiontype)
        {
            case 2:
                $properties = json_encode(["value" => "null", "required" => false]);
                break;

            case 3:
                $properties = json_encode(["value" => "", "required" => false]);
                break;

            case 4:
                $properties = json_encode(["value" => "0", "required" => false]);
                break;

            case 5:
                $properties = json_encode(["value" => "null", "required" => false]);
                break;

            case 7:
                $properties = json_encode(["value" => null, "required" => false, "type" => "Texto"]);
                break;

            case 11:
                $properties = json_encode(["value" => null, "size" => "", "extension" => "", "required" => false]);
                break;

            case 14:
                $properties = json_encode(["duration" => 0, "startdate" => null, "resumedate" => null, "finishdate" => null]);
                break;
                
            default:
                $properties = json_encode(["value" => null, "required" => false]);
                break;
        }

        return $properties;
    }

    private function getDefaultChecklistData()
    {
        $data = DB::table('wh_checklist_data')->first();

        return is_null($data) ? null : $data->id;
    }

    public function getFormatProperties($model, $data)
    {
        $properties = null;

        switch($model->optiontype)
        {
            case 1:
                $properties = null;
                break;
                
            default:
                $properties = json_encode($data);
                break;
        }
     
        return $properties;
    }

    public function getPreview($request)
    {
        $data = DB::table('wh_checklist_data')->get();

        $options = ChecklistOption::with('children')->where('idchecklist', $request->idchecklist)->whereNull('idparent')->get();

        $options->map(function ($option) use ($data) {
            $option->data  = (is_null($option->iddata) ? null : json_decode($data->firstWhere('id', $option->iddata)->data));
            $option->value = $this->getValue($option);
            return $option;
        });

        return view('pages.config.forms.preview', ["options" => $options->sortBy('position')]);
    }

    public function getValue($option)
    {
        $value = "";

        switch ($option->optiontype)
        {
            case 11:
                $properties = json_decode($option->properties);
                $value = $properties->value;
                break;
            
            default:
                $value = "";
                break;
        }

        return $value;
    }

    public function createCopy($request)
    {
        $request["type"] = 1;

        $form = Checklist::create($request->all());

        $ticketType = TicketType::find($request->idtype);

        $dataItem = ["name" => $request->name, "idtype" => $request->idtype, "idteam" => $ticketType->idteam, "idchecklist" => $form->id];

        Item::create($dataItem);

        $form['idtype'] = $request->idtype;

        $this->replicateFormOptions($form->id, $request->idchecklist);

        return response()->json(["success" => true, "model" => $form]);
    }

    public function replicateFormOptions($idnewchecklist, $idcopychecklist)
    {
        $options = ChecklistOption::with('children')->where('idchecklist', $idcopychecklist)->whereNull('idparent')->get();

        foreach($options as $option)
        {
            $newoption = $option->replicate()->fill(['idchecklist' => $idnewchecklist]);

            $newoption->save();

            foreach ($option->children as $child)
            {
                $child->idparent = $newoption->id;

                $newoption2 = $child->replicate()->fill(['idchecklist' => $idnewchecklist]);

                $newoption2->save();
            }
        }
    }

}