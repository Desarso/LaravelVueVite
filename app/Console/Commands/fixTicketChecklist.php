<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use App\Models\ChecklistOption;
use App\Enums\ChecklistOptions;


class FixTicketChecklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fix_ticket_checklist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'commando para arreglar el problema de los group del checklist';

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
        $checklist = TicketChecklist::whereDate('created_at', '>', '2021-01-01')
                                    ->where('idchecklist',2)
                                    ->get();

        for ($i=0; $i < count($checklist); $i++) { 

            $options_copy = collect(json_decode($checklist[$i]->options));
            $options = $options_copy->sortBy('position')->values();

            for ($x=0; $x < count($options); $x++) { 

                $checklistOption = ChecklistOption::find($options[$x]->idchecklistoption);
                
                if($checklistOption) {
                    $options[$x]->group = $checklistOption->group;
                } else {
                }
            }

            $checklist[$i]->options = $options->toJson();

            try {
                $checklist[$i]->save();
            } catch (\Throwable $th) {
                echo "Error -> idticket: ".$checklist[$i]->idticket; 
            }
        }
    }
}
