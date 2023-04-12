<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\SettingUpdate;

use App\Repositories\SpotRepository;
use App\Repositories\UserRepository;
use App\Repositories\TagRepository;
use App\Repositories\ItemRepository;
use App\Repositories\UserTeamRepository;
use App\Repositories\ChecklistOptionRepository;
use App\Repositories\TicketPriorityRepository;

class SettingUpdateRepository
{
    protected $spotRepository;
    protected $userRepository;
    protected $checklistRepository;
    protected $tagRepository;
    protected $itemRepository;
    protected $userTeamRepository;
    protected $ticketPriorityRepository;

    public function __construct(SpotRepository $spotRepository, UserRepository $userRepository, TagRepository $tagRepository, ItemRepository $itemRepository,ChecklistOptionRepository $checklistRepository,UserTeamRepository $userTeamRepository, TicketPriorityRepository $ticketPriorityRepository)
    {
        $this->spotRepository = $spotRepository;
        $this->userRepository = $userRepository;
        $this->checklistRepository = $checklistRepository;
        $this->tagRepository  = $tagRepository;
        $this->itemRepository = $itemRepository;
        $this->userTeamRepository = $userTeamRepository;
        $this->ticketPriorityRepository = $ticketPriorityRepository;
    }

    public function register($model)
    {
        $settingUpdate = SettingUpdate::firstOrCreate(['name' => $model->getTable()]);
        $settingUpdate->touch();
    }  

    public function getListApp()
    {
        return DB::table('wh_setting_update')->get(['id', 'name', 'updated_at']);
    }

    public function get($request)
    {
        $iduser = $request->iduser;
        $settings_app = collect(json_decode($request->data));

        $settings = DB::table('wh_setting_update')->select('id', 'name', 'updated_at')->get();

        $filter = $settings->filter(function($setting, $key) use($settings_app, $iduser, $request){

            $settingApp = $settings_app->firstWhere('id', $setting->id);

            if ($settingApp) {
            
                $last_updated_at = $settingApp->updated_at;

                if($setting->updated_at != $last_updated_at && !is_null($last_updated_at))
                {
                    $setting->data = $this->getData($setting->name, $last_updated_at, $iduser, $request);
                    return true;
                }
            }
        });
        
        return $filter->values();
    }  

    private function getData($table, $last_updated_at, $iduser, $request)
    {
        switch($table)
        {
            case 'wh_spot':

                return $this->spotRepository->getListApp($iduser, $last_updated_at, $request);
                break;
                
            case 'wh_item':

                return $this->itemRepository->getListApp($last_updated_at);
                break;

            case 'wh_user':

                return $this->userRepository->getListApp($last_updated_at, $request);
                break;    

            case 'wh_checklist':
                
                return $this->checklistRepository->getListApp($last_updated_at, $request);
                break; 

            case 'wh_checklist_data':
                
                return $this->checklistRepository->getChecklistDataApp($last_updated_at);
                break; 

            case 'wh_user_team':

                return $this->userTeamRepository->getUserPermissionsAPP($iduser);
                break;   

            case 'wh_tag':
                return $this->tagRepository->getListApp($last_updated_at);
                break;

            case 'wh_ticket_priority':
                return $this->ticketPriorityRepository->getListApp($last_updated_at);
                break;

            case 'wh_project':
                # code...
                break;
        }

    }
}