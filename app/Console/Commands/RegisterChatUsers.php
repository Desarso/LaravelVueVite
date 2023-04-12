<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class RegisterChatUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:registes_chat_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to add users inside the chat server';

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
     * @return int
     */
    public function handle()
    {
        $organization = $this->getOrganization();
        
        if (is_null($organization->chat_uid)) {

            $organizationChatUID = uniqid();

            $response = Http::post(
                "http://chat.whagons.com:3001/api/users/create-organization", [
                'organization' => $organization,
            ]);

            if($response->status() == 200)
            {
                $data = json_decode($response->body());
                $organizationChatUID = $data->organization->_id;

                DB::table('wh_organization')
                    ->where('id', "=", $organization->id)
                    ->update(['chat_uid' => $organizationChatUID]);
            }

            $organization = $this->getOrganization();
        }

        $users = DB::table('wh_user')
                    ->select('id AS wh_id', 'urlpicture', DB::raw( "'".$organization->chat_uid . "' AS id_organization") , DB::raw('CONCAT(firstname," ",ifNUll(lastname,"")) as name'))
                    ->whereNull('chat_uid')
                    ->whereNull('deleted_at')
                    ->get();

        // dd(json_encode($organization), json_encode($users));

        $response = Http::post(
            "http://chat.whagons.com:3001/api/users/create-many", [
            'organization' => $organization,
            'users' => $users
        ]);

        if($response->status() == 200)
        {
            $data = json_decode($response->body());

            foreach ($data->users as $user)
            {
                DB::table('wh_user')
                    ->where('id', "=", $user->wh_id)
                    ->update(['chat_uid' => $user->uid]);
            }
        }

        return 0;
    }

    private function getOrganization()
    {
        return DB::table('wh_organization')
                    ->select('id','name', 'chat_uid',  DB::raw( "'".env('APP_URL') . "' AS url") )
                    ->first();
    }
}
