<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\CleaningSchedule;

class HouseKeepingRepository
{

      	public function getAll()
        {
               return DB::table('wh_cleaning_schedule as s')
              ->join('wh_user as st', 's.idresource', '=', 'st.id')
              ->join('wh_spot as tt', 's.idspot', '=', 'tt.id')

             ->select('s.id','s.sequence', 's.dow','st.id as idresource','st.firstname as Encargado','tt.id as idspot', 'tt.name as Habitacion')
              ->get();
       }

       public function addMultiCleaningSchedule($request)
       {
            $spots = (array)$request->idspot;
            for ($i = 0; $i < count($spots) ; ++$i) 
            { //cambiar a foreach
              
             $row['idresource'] = $request->idresource[0];
             $row['idspot'] = $spots[$i];
             $row['dow'] = json_encode($request->dow);

             $HouseKeeping = CleaningSchedule::create($row);
            }
      }

      public function updateCleaningSchedule($request)
      {
            $validatedData = $request->validate([
              'idresource' => 'required'
            ]);

            $HouseKeeping = CleaningSchedule::findOrFail($request->id);

            
            $HouseKeeping->idresource     = $request->idresource;
            $HouseKeeping->idspot         = $request->idspot;
            $HouseKeeping->dow            = $request->dow;
            $HouseKeeping->save();
      }

      public function deleteCleaningSchedule($request)
      {
            $HouseKeeping = CleaningSchedule::find($request->id);
            $HouseKeeping->delete();
      }


}