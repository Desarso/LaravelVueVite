<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Protocol;

class ProtocolRepository
{
    public function getAll()
    {
        return Protocol::whereNull('deleted_at')->get(["id","name", "version", "idtype", "smallimage","image", "code", "html", "isemergency", "activated", "reference", "qrcode", "lan", "enabled"]);
    }

    public function getList()
    {
        return DB::table('wh_protocol')->where('enabled', true)->get(['id as value', 'name as text']);
    }
  
    public function create($request)
    {
        return Protocol::create($request->all());
    }

    public function update($request)
    {       
      $model = Protocol::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = Protocol::findOrFail($request->id);
        $model->delete();
    }

    public function saveHtml($request)
    {
        Protocol::where('id', $request->id)->update(['html' => $request->html]);
        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function show($request)
    {
        $protocol = DB::table('wh_item as i')
                      ->join('wh_protocol as p', 'i.idprotocol', '=', 'p.id') 
                      ->select('p.html')
                      ->where('i.id', $request->iditem)
                      ->first();

        $html = is_null($protocol) ? "" : $protocol->html;

        return view('/pages/config/protocols/template', ['html' => $html ]);
    }

    public function getListApp($request)
    {
        $protocols = Protocol::select('id', 'idtype', 'name', 'version', 'smallimage', 'image')
                      ->get();

        return $protocols;
    }

    
    public function showProtocolApp($request)
    {
        $protocol = DB::table('wh_protocol')
                      ->select('html')
                      ->where('id', $request->id)
                      ->first();

        $html = is_null($protocol) ? "" : $protocol->html;

        return view('/pages/config/protocols/template', ['html' => $html ]);
    }
}