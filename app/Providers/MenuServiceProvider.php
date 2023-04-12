<?php

namespace App\Providers;

use App\Repositories\OrganizationRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(Schema::hasTable('wh_organization'))
        {
            view()->composer('*', function($view)
            {
                if(Auth::check())
                {
                    $organizationRepository = new OrganizationRepository;
                    $menuRepository = new MenuRepository;
                    // get all data from menu.json file
                    //$verticalMenuJson = file_get_contents(base_path('resources/json/verticalMenu.json'));
                    $verticalMenuData = $organizationRepository->menuSettings();
                    $verticalMenuData2 = $menuRepository->getMenu();

                    //dd($verticalMenuData, $verticalMenuData2->toArray());

                    $horizontalMenuJson = file_get_contents(base_path('resources/json/horizontalMenu.json'));
                    $horizontalMenuData = json_decode($horizontalMenuJson);
                    // Share all menuData to all the views
                    \View::share('menuData', [$verticalMenuData, $horizontalMenuData, $verticalMenuData2]);

                    $dataSearchJson = file_get_contents(base_path('public/data/laravel-search-list.json'));
                    $dataSearch = json_decode($dataSearchJson);

                    $dataSearch = collect($dataSearch);

                    $shortcuts = $dataSearch->whereIn("url", json_decode(Auth::user()->shortcuts));

                    \View::share('dataSearch', $dataSearch);
                    \View::share('shortcuts', $shortcuts);
                }
                else
                {
                    \View::share('dataSearch', []);
                }
            });
        }
    }
}
