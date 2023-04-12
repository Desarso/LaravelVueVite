<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'App\Repositories\SpotRepository'
        );
        $this->app->bind(
            'App\Repositories\SpotTypeRepository'
        );
        $this->app->bind(
            'App\Repositories\TeamRepository'
        );
        $this->app->bind(
            'App\Repositories\ItemRepository'
        );
        $this->app->bind(
            'App\Repositories\TicketPriorityRepository'
        );
        $this->app->bind(
            'App\Repositories\TicketStatusRepository'
        );
        $this->app->bind(
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Repositories\UserTypeRepository'
        );
        $this->app->bind(
            'App\Repositories\RoleRepository'
        );
        $this->app->bind(
            'App\Repositories\UserTeamRepository'
        );
        $this->app->bind(
            'App\Repositories\TicketRepository'
        );
        $this->app->bind(
            'App\Repositories\TicketNoteRepository'
        );
        $this->app->bind(
            'App\Repositories\CleaningStatusRepository'
        );
        $this->app->bind(
            'App\Repositories\CleaningPlanRepository'
        );
        $this->app->bind(
            'App\Repositories\TicketAppRepository'
        );
        $this->app->bind(
            'App\Repositories\LogRepository'
        );
    }

    public function boot()
    {

    }
}
