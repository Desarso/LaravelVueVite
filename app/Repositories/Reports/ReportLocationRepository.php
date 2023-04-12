<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportLocationRepository
{
      public function getData($request)
      {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $data = Log::select("id", "iduser", "idstatus", "location", "idticket", "locationname", "created_at")
                       ->whereHas('ticket', function ($query) {
                              $query->whereNull('deleted_at');
                       })
                       ->when(!is_null($request->iduser), function ($query) use ($request) {
                              return $query->where('iduser', $request->iduser);
                       })
                       ->when(!is_null($request->idteam), function ($query) use($request){
                              return $query->whereHas('user.teams', function ($q) use ($request) {
                                    $q->where('idteam', $request->idteam);
                              });
                       })
                       ->when(!is_null($request->idstatus), function ($query) use ($request) {
                              return $query->where('idstatus', $request->idstatus);
                       })
                       ->whereBetween('created_at', [$start, $end])
                       ->whereNotNull('idstatus')
                       ->whereNotNull('location')
                       ->whereNotNull('idticket');

            $total = $data->count('id');

            $data = $data->skip($request->skip)->take($request->take)->get();

            $collection = collect([]);

            foreach($data as $item)
            {
                  $location = "";

                  if(is_null($item->locationname))
                  {
                        $location = $this->getLocation($item->location);
                        $item->locationname = $location;
                        $item->save();
                  }
                  else
                  {
                        $location = $item->locationname;
                  }

                  $option = ['iduser' => $item->id, 'code' => $item->ticket->code, 'task' => $item->ticket->name, 'spot' => $item->ticket->spot->name, 'iduser' => $item->user->fullname, 'idstatus' => $item->idstatus, 'location' => $location, 'coordinates' => json_decode($item->location), 'created_at' => $item->created_at];
    
                  $collection->push($option);
            }

            return array("total" => $total, "data" => $collection);
      }

      private function getLocation($json)
      {
            if(is_null($json) || $json == "") return null;

            $data = json_decode($json);

            $params = $data->lat ."," . $data->long;

            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAZoNhuZaqtozVGDBuRC206IvCyqyeI2MU&latlng={$params}");

            if($response->status() == 200)
            {
                  $data = json_decode($response->body());
            }

            return property_exists($data, 'results') ? $data->results[0]->formatted_address : "";
      }
}