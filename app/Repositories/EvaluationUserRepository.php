<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Enums\App;
use App\Models\Ticket;

class EvaluationUserRepository
{

    protected $checklistRepository;
    protected $checklistOptionRepository;

    public function __construct(TicketChecklistRepository $checklistRepository, ChecklistOptionRepository $checklistOptionRepository)
    {
        $this->checklistRepository = $checklistRepository;
        $this->checklistOptionRepository = $checklistOptionRepository;
    }


    public function getDataEvalution($request)
    {

        $appSettings = DB::table('wh_app')->where('id', App::UsersEvalution)->first()->settings;
        $settings = json_decode($appSettings);

        $weekStartDate = Carbon::now()->startOfWeek();
        $weekEndDate = Carbon::now()->endOfWeek();

        DB::statement('SET SESSION group_concat_max_len = 18446744073709551615;');

        $data = DB::table('wh_ticket_checklist as tc')
            ->select(DB::raw("COUNT(tc.idchecklist) as count_checklist"), 'tc.idchecklist', DB::raw('concat("[", GROUP_CONCAT(REPLACE(REPLACE(tc.options, "[", ""), "]", "")) ,"]") as options'))
            ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
            ->join('wh_ticket_user as tr', 'tr.idticket', '=', 't.id')
            ->where('t.iditem', $settings->default_item)
            ->where('tr.iduser', $request->iduser)
            ->whereBetween('t.created_at', [$weekStartDate, $weekEndDate])
            ->whereNull('t.deleted_at')
            ->groupBy('tc.idchecklist')
            ->get();

        if ($data->count() == 0) return ["data" => collect(), "count_checklist" => 0, "count_branches" => 0];

        $count_checklist = $data[0]->count_checklist;
        $data = collect(json_decode($data[0]->options));

        return ["data" => $data, "count_checklist" => $count_checklist];
    }

    public function getEvaluationGroupbyUser($request)
    {
        $options = collect();

        $data = $this->getDataEvalution($request);
        $result = $data["data"];

        $headers = $result->where('optiontype', 6)->unique('idchecklistoption');

        $result = $result->where('optiontype', '!=', 6);
        $grouped = $result->groupBy('group');

        foreach ($grouped as $group)
        {
            $results = $this->countOptions($group);
            $header = $headers->firstWhere('group', $group[0]->group);

            $results['group'] = $group[0]->group; 
            $results['name'] = $header->name; 
            $options->push($results);
        }

        return $options;
    }

    private function countOptions($collection)
    {
        $checked  = $collection->groupBy('value');
        $keys1 = $checked->keys()->toArray();
        
        $counts = array(
            "si"    =>  in_array(1, $keys1) ? $checked[1]->count()  : 0,
            "no"    =>  in_array(2, $keys1) ? $checked[2]->count()  : 0,
            "na"    =>  in_array(3, $keys1) ? $checked[3]->count()  : 0,
            "total" => $collection->count(),
        );

        return array_merge($counts, $this->getaverage((object)$counts));
    }

    private function getaverage($counts){
        $total = $counts->total - $counts->na;
        $average = 0;

        if($total > 0) $average = ($counts->si/$total) * 100;

        return array( "average" =>round($average, 2) ); 
    }


    public function createEvaluteUserAPP($request) {

        session(['iduser' => $request->authuser]);

        $appSettings = DB::table('wh_app')->where('id', App::UsersEvalution)->first()->settings;
        $settings = json_decode($appSettings);
        $item = DB::table('wh_item')->where('id', $settings->default_item)->first(['name', 'idteam', 'idchecklist']);

        Ticket::unsetEventDispatcher();
        $task = Ticket::create([
            "uuid" => uniqid(),
            "name" => $item->name,
            "iditem" => $settings->default_item,
            "idspot" => $settings->default_spot,
            "idteam" => $item->idteam,
            "created_by" => $request->authuser
        ]);

        $task->users()->attach($request->iduser);

        if(!is_null($item->idchecklist)) {

            $checklist_copy = $this->checklistOptionRepository->getChecklistCopy($item->idchecklist, $task->id);
            $task->checklists()->create($checklist_copy);
        }

        return response()->json(['success' => true, 'idticket' => $task->id]);
    }

    public function getUsersEvaluationAPP()
    {
        $users = DB::table('wh_user')
                ->select('id', 'id as iduser', 'urlpicture', DB::raw('CONCAT(firstname," ",ifNUll(lastname,"")) as fullname'))
                ->whereNull('deleted_at')
                ->get();

        foreach ($users as $user) {
            $data = $this->getDataEvalution($user);
            $lines = $data["data"]->where('optiontype', '!=', 6);
            $results = $this->countOptions($lines);

            $user->average = $results['average'];
            $user->total = $data['count_checklist'];
        }

        return $users;
    }

}