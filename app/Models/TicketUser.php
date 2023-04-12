<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketUser extends Pivot
{
    protected $table = "wh_ticket_user";

    public static function boot()
    {
        parent::boot();

        $events = [
            'retrieved', 'creating', 'created', 'updating', 'updated',
            'saving', 'saved', // 'restoring', 'restored',
            'deleting', 'deleted', // 'forceDeleted',
        ];

        foreach($events as $event) {
            static::$event(function ($item) use ($event) {
                //echo "<b>{$event}</b> has been fired!<br>";
            });
        }
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idticket', 'id');
    }

}
