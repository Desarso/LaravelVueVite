<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use App\Enums\ChecklistOptions;


class MigrateTicketChecklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:migrate_ticket_checklist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'commando para migrar los checklist de la version 2 a 3';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $checklist = DB::table('dd_ticket_checklist as tc')
                        ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                        ->skip(5000)
                        ->take(50000)
                        ->get();


        for ($i=0; $i < count($checklist); $i++) { 

            $options = collect(json_decode($checklist[$i]->options));

            $options->each(function ($item) {
                
                unset($item->type);
                unset($item->timestamp);
                unset($item->idevaluator);

                $isgroup = 0;
                $optiontype = $item->optiontype;
                $item->iddata = null;

                switch ($item->optiontype) {
                    
                    case 6:
                        $isgroup = 1;
                        break;

                    case 5:
                        $item->iddata = 2;
                        $optiontype = 2;
                        break;

                    case 2:
                        $item->iddata = 2;
                        break;
                }

                $item->optiontype  = $optiontype;
                $item->isgroup = $isgroup;
                $item->idasset = null;
            });

            $checklist[$i]->options = $options->toJson();

            if($checklist[$i]->idevaluator == 0) {
                $checklist[$i]->idevaluator = null;
            }

            try {
                // dd("checklist[$i]", $checklist[$i]);
                $newChecklist = TicketChecklist::Create((array)$checklist[$i]);
                echo "\Insert -> idticket: ".$newChecklist->idticket; 
                // dd("TicketChecklist[$i]", $newChecklist->id);
            } catch (\Throwable $th) {
                //throw $th;
                echo "Error -> idticket: ".$checklist[$i]->idticket; 
            }
            
        }
    }
}
