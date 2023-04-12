<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class OrganizationRepository
{
    public function planSettings()
    {
        $apps = DB::table('wh_app')->get();

        $plansettings = DB::table('wh_organization')->first()->plansettings;

        $plansettings = json_decode($plansettings);
        $settings = $this->removeDisabled($plansettings);

        $settings = collect($settings)->sortBy('position');

        foreach($settings as $setting)
        {
            
            if ($setting->table == 'wh_user') {
                $setting->value  = DB::table($setting->table)->where('issuperadmin', 0)->whereNull('deleted_at')->count();
            } else {
                $setting->value      = DB::table($setting->table)->whereNull('deleted_at')->count();
            }

            $setting->usage      = ($setting->value / $setting->maxvalue) * 100;
            $setting->usagecolor = $this->usageColor($setting->usage);
        }

        return $settings;
    }

    public function menuSettings()
    {
        $organization = DB::table('wh_organization')->first();
        if(!is_null($organization))
        {
            $menusettings = json_decode($organization->menusettings);
            $menusettings->menu = $this->removeDisabled($menusettings->menu);
            return $menusettings;
        }

        return [];
    }

    private function removeDisabled($data)
    {
        $apps = DB::table('wh_app')->get();

        foreach($data as $key => $item)
        {
            if(property_exists($item, 'idapp') == true)
            {
                $app = $apps->firstWhere('id', $item->idapp);

                if(!is_null($app))
                {
                    if(!$app->enabled) unset($data[$key]);
                }
            }
            else if(property_exists($item, 'submenu') == true)
            {
                foreach($item->submenu as $key => $submenu)
                {
                    if(property_exists($submenu, 'idapp') == true)
                    {
                        $app = $apps->firstWhere('id', $submenu->idapp);
        
                        if(!is_null($app))
                        {
                            if(!$app->enabled) unset($item->submenu[$key]);
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function usageColor($usage) 
    { 
        if ($usage >= 90) return 'danger';
        if ($usage >= 60) return 'warning';
        if ($usage >= 20) return 'success';
        return 'primary';
    }
}
