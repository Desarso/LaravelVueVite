<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Repositories\LogRepository;
use App\Enums\LogAction;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketAssigned;
use App\Events\TicketCreated;
use App\Events\TicketFinished;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class TicketObserver
{
    protected $logRepository;
    protected $userRepository;

    public function __construct()
    {
        $this->logRepository  = new LogRepository;
        $this->userRepository = new UserRepository;
    }

    public function created(Ticket $ticket)
    {
        $dispatcher = Ticket::getEventDispatcher();

        Ticket::unsetEventDispatcher();

        if(is_null($ticket->code))
        {
            $ticket->code = $ticket->id;
            $ticket->save();
        }

        Ticket::setEventDispatcher($dispatcher);

        $iduser = Auth::check() ? Auth::id() : session('iduser');
        $this->logRepository->register(LogAction::CreateTicket, $ticket, $iduser);
    }

    public function updated(Ticket $ticket)
    {
        $changes = $ticket->getChanges();
        
        unset($changes['updated_at']);

        if(array_key_exists('updated_by', $changes)) unset($changes['updated_by']);

        if(count($changes) == 0) return; //Evitamos meter el log al hacer un touch al ticket
    
        $this->logRepository->register(LogAction::EditTicket, $ticket, $ticket->updated_by, $changes);

        $settings = json_decode(DB::table('wh_organization')->first()->settings);

        if(property_exists($settings, 'notify_finished_task') == true)
        {
            if(($settings->notify_finished_task == true) && (property_exists($changes, 'idstatus') == true) && ($changes['idstatus'] == 4))
            {
                event(new TicketFinished($ticket));
            }
        }
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param  \App\Ticket  $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        $this->logRepository->register(LogAction::DeleteTicket, $ticket, $ticket->updated_by);
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param  \App\Ticket  $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param  \App\Ticket  $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }

    public function belongsToManyAttached($relation, $parent, $ids) 
    {
        if(count($ids) == 0 && $relation == "users")
        {
            $users = $this->userRepository->getUserToNotify($parent);

            event(new TicketAssigned($parent, $users, "withoutUsers"));
        }
        else if($relation == "users" && count($ids) > 0)
        {
            event(new TicketAssigned($parent, $ids, $relation));
        }
        else if($relation == "usersCopy" && count($ids) > 0)
        {
            event(new TicketAssigned($parent, $ids, $relation));
        }

        foreach ($ids as $id)
        {
            $this->createLogPivot("attached", $relation, $parent, $id);
        }
    }

    public function belongsToManyDetached($relation, $parent, $ids) 
    {
        if(count($ids) == 0) return;

        foreach ($ids as $id)
        {
            $this->createLogPivot("detached", $relation, $parent, $id);
        }
    }

    private function createLogPivot($action, $relation, $parent, $id)
    {
        $data = new \stdClass;
        $data->action = $action;
        $data->id     = $id;

        $logType = null;

        switch ($relation)
        {
            case 'users':
                $logType = LogAction::User;
                break;

            case 'usersCopy':
                $logType = LogAction::Copy;
                break;

            case 'tags':
                $logType = LogAction::Tag;
                break;

            case 'approvers':
                $logType = LogAction::Approver;
                break;
        }

        $this->logRepository->register($logType, $parent, $parent->updated_by, $data);
    }
}
