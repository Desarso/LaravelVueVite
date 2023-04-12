<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use App\Models\Cleaning\CleaningSchedule;
use App\Models\Spot;
use Carbon\Carbon;
use Helper;
use PhpParser\Node\Expr\Print_;
use App\Enums\TicketStatus;
use App\Enums\CleaningStatus;
use App\Enums\App;
use App\Repositories\UserRepository;

class CleaningScheduleRepository
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository        = $userRepository;
    }

    public function getAll()
    {
        $schedules = CleaningSchedule::get(['id', 'idspot', 'iduser', 'dow', 'time','sequence','iditem','enabled']);

        $schedules->map(function ($schedule){
            $schedule->dow = array_map(array($this, 'formatDow'), json_decode($schedule->dow));
            return $schedule;
        });
        
        return $schedules;
    }

    public function getList()
    {
        return DB::table('wh_cleaning_schedule')->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        $dows = $this->pluckDows($request->dow);
        $request->merge(['dow' => json_encode($dows)]);
        $model = CleaningSchedule::create($request->all());
        $model->dow = array_map(array($this, 'formatDow'), $dows);
        return $model;

       // return CleaningSchedule::create($request->all());
    }

    private function pluckDows($dows)
    {
        $dows = collect($dows)->pluck('value')->toArray();
        $dows = array_map('intval', $dows);
        return $dows;
    }

    private function formatDow($iddow)
    {
        $dow = new \stdClass;
        $dow->value = $iddow;
        return $dow;
    }


    public function update($request)
    {       
      $model = CleaningSchedule::find($request->id);
      $dows = $this->pluckDows($request->dow);
      $request->merge(['dow' => json_encode($dows)]);
      $model->fill($request->all())->save();
      $model->dow = array_map(array($this, 'formatDow'), $dows);
      return $model;

    }

    public function delete($request)
    {               
        $model = CleaningSchedule::findOrFail($request->id);
        $model->delete();
    }    

    private function getRoomsDataAPP($idUser)
    {               
        $spots = $this->userRepository->getUserSpots($idUser);

        return Spot::select('id', 'idcleaningstatus', 'name', 'idparent', 'order')
                    ->with('parent:id,name')
                    ->with('cleaningStatus:id,name,background,icon')
                    ->with(["cleaningPlans" => function($q) {                       
                        $q->select('id', 'iduser', 'idspot', 'idcleaningstatus')
                            ->with('user:id,firstname,lastname')
                            ->where('idcleaningstatus', '!=', CleaningStatus::Clean)
                            ->where('date', Carbon::today());         
                    }])
                    ->withCount(["cleaningPlans" => function($q)  {
                        $q->where('idcleaningstatus', '!=', CleaningStatus::Clean)
                          ->where('date', Carbon::today());
                    }])
                    // ->join('cleaningPlans', 'cleaningPlans.idspot', '=', 'wh_spot.id') 
                    ->where('enabled', true)
                    ->where('cleanable', true)
                    ->whereIn('id', $spots);
    }

    public function getRoomsAPP($request)
    {               
        $idUser = $request->iduser;
        $data = $this->getRoomsDataAPP($idUser);
        $rooms = $data->get();

        $rooms->map(function ($item) use ($request) {
            $iconOriginal = $item->cleaningStatus->getOriginal('icon');
            $item->cleaningStatus->icon = helper::formatIcon($iconOriginal);

            $hasPlans = $item->cleaningPlans->whereIN('iduser',$request->iduser);
            $item->me =  count($hasPlans) > 0;

            $plan =  $item->cleaningPlans->first();
           
            if ($plan) {
                if ($plan->user) {
                    $item->assigned = $plan->user->fullname;
                } else {
                    $item->assigned = null;
                }
            } else {
                $item->assigned = null;
            }

            return $item;
        });

        // return $rooms;


        $meRooms = $rooms->where('me', true)->all();
        $otherRooms = $rooms->where('me', false)->sortBy('order')->values()->all();
        $rooms = array_merge($meRooms, $otherRooms);;

        return $rooms;
    }    

    public function getRoomByIdAPP($request)
    {               
        $spot = Spot::select('id', 'idcleaningstatus', DB::raw("(CASE WHEN shortname IS NOT NULL THEN shortname ELSE name END) AS name"))
                        ->with('parent:id,name')
                        ->with('cleaningStatus:id,name,background,icon')
                        ->withCount(["cleaningPlans" => function($q)  {
                            $q->where('idcleaningstatus', '!=', CleaningStatus::Clean)
                            ->where('date', Carbon::today());
                        }])
                        ->where('enabled', true)
                        ->where('cleanable', true)
                        ->where('id', $request->id)
                        ->get()
                        ->first();

        $iconOriginal = $spot->cleaningStatus->getOriginal('icon');
        $spot->cleaningStatus->icon = helper::formatIcon($iconOriginal);

        return $spot;
    }

    public function searchRoomsAPP($request)
    {               
        $idUser = $request->iduser;
        $data = $this->getRoomsDataAPP($idUser);

        $items = $data
                    ->when(isset($request->spot), function ($query) use($request) {
                        $query->Where('name', 'LIKE', "%$request->spot%");
                    })
                    ->when(isset($request->status), function ($query) use($request) {
                        $query->whereHas('cleaningStatus', function ($query) use($request) {
                            return $query->Where('name', 'LIKE', "%$request->status%");
                        });
                    })
                    ->get();

        $items->map(function ($item) {
            $iconOriginal = $item->cleaningStatus->getOriginal('icon');
            $item->cleaningStatus->icon = helper::formatIcon($iconOriginal);
            return $item;
        });

        return $items;
    }    

    public function getCleaningProducts() 
    {
        $cleaning_products = $this->getCleaningSettings()->cleaning_products;
        
        return DB::table('wh_item')  
                 ->whereIn('idtype', $cleaning_products)
                 ->pluck('id')
                 ->toArray();
    }

    public function getCleaningSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Cleaning)->first()->settings;
        return json_decode($settings);
    }
}