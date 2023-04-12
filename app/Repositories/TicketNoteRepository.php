<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\TicketNote;
use Illuminate\Support\Facades\Auth;
use App\Enums\LogAction;
use App\Events\LogActivity;
use App\Events\NoteCreated;
use Session;
use Carbon\Carbon;

class TicketNoteRepository
{
    public function getNotes($request)
    {
        $notes = DB::table('wh_ticket_note as tn')
                   ->join('wh_user as r', 'r.id', '=', 'tn.created_by')
                   ->where('tn.idticket', $request->idticket)
                   ->whereNull('tn.idchecklistoption')
                   ->select('tn.id', 'tn.note', 'tn.type', 'tn.created_at', 'tn.created_by', DB::raw('CONCAT(r.firstname, " ", r.lastname) AS fullname'), 'r.urlpicture')
                   ->orderBy('tn.created_at')
                   ->get();

        $notes->each(function ($note, $key) {
            $note->created_at = Carbon::parse($note->created_at)->setTimezone(Session::get('local_timezone'));
        });

        return view('task.notes', ["notes" => $notes]);
    }

    public function create($request)
    {
        $request['created_by'] = Auth::id();
        $request['uuid'] = uniqid();
        $request['type'] = 1;

        $note = TicketNote::create($request->all());
        $note->ticket->touch();

        event(new NoteCreated($note));

        return response()->json(['success' => true, 'data' => $note]);
    }

    public function delete($request)
    {
        $note = TicketNote::find($request->id);
        $note->ticket->touch();
        $note->delete();
    }

    public function getNotesApp($request)
    {
        return DB::table('wh_ticket_note as tn')
                   ->join('wh_user as r', 'r.id', '=', 'tn.created_by')
                   ->where('tn.idticket', $request->idtask)
                   ->when($request->has('idchecklistoption'), function ($query) use($request){
                        return $query->where('tn.idchecklistoption', $request->idchecklistoption);
                    }, function ($query) {
                        return $query->whereNull('tn.idchecklistoption');
                    })
                   ->select('tn.uuid', 'tn.idticket AS idtask', 'tn.note', 'tn.type', 'tn.created_at', DB::raw('CONCAT(r.firstname, " ", r.lastname) AS createdName'), 'r.urlpicture', 'tn.created_by', 'tn.idchecklistoption')
                   ->get();
    }

    public function deleteNoteAPP($request)
    {
        Session::put('iduser', $request->iduser);

        $note = TicketNote::where('idticket' , '=', $request->idtask)
                            ->where('uuid' , '=', $request->uuid)
                            ->first();
        
        if ($note) {
            $note->ticket->touch();
            $note->delete();
        }
        
        
        return response()->json(['success' => true]);
    }
}