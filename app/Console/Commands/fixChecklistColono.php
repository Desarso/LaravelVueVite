<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use App\Models\ChecklistOption;
use App\Models\TicketNote;


class fixChecklistColono extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fix_checklist_colono';

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
        $checklist = TicketChecklist::whereIn('idticket',[544,566,568,572,577,592,598,602,650])->get();


        for ($i=0; $i < count($checklist); $i++) { 

            $options = collect(json_decode($checklist[$i]->options));



            for ($x=0; $x < count($options); $x++) { 

                $line = ChecklistOption::where('name', $options[$x]->name)
                                        ->where('idchecklist', 6)
                                        ->first();

                
                $note = TicketNote::where('idchecklistoption', $options[$x]->idchecklistoption)
                                    ->where('idticket', $checklist[$i]->idticket)
                                    ->update(['idchecklistoption' => $line->id]);

                $options[$x]->idchecklistoption = $line->id;
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
