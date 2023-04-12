<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use App\Models\Cleaning\CleaningNote;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log as LavavelLog;

class CleaningNoteRepository
{
    public function getCleaningNotesAPP($request)
    {
        return CleaningNote::select("idplaner", "note", "type", "created_by", "created_at")
                ->with('user:id,firstname,lastname,urlpicture')  
                ->where('idplaner',$request->idplaner)    
                ->get();
    }

    function CreateCleaningNotesAPP($note)
    {   
        if ($note->type == "IMAGE") {
            $url = helper::UploadImageApp([$note->note]);
            $note->merge(["note" => $url]);
        }

        $ticketNote = CleaningNote::create($note->all());

        return response()->json([
            "sucess" => true,
            "ticketNote" => $ticketNote 
        ]);
    }
}