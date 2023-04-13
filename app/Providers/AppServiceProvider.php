<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Repositories\TicketStatusRepository;
use App\Repositories\ItemRepository;
use App\Repositories\TicketPriorityRepository;
use App\Repositories\SpotRepository;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TicketTypeRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TagRepository;
use App\Repositories\AssetRepository;
use App\Repositories\ChecklistRepository;
use App\Repositories\SpotTypeRepository;
use Illuminate\Support\Facades\Schema;

use App\Observers\TicketObserver;
use App\Observers\TicketNoteObserver;
use App\Observers\SpotObserver;
use App\Observers\ItemObserver;
use App\Observers\UserObserver;
use App\Observers\TagObserver;
use App\Observers\ChecklistObserver;
use App\Observers\ChecklistOptionObserver;
use App\Observers\TeamObserver;
use App\Observers\AssetObserver;
use App\Observers\ProjectObserver;
use App\Observers\SpotTypeObserver;
use App\Observers\TicketTypeObserver;
use App\Observers\UserTeamObserver;
use App\Observers\RoleObserver;
use App\Observers\CleaningPlanObserver;
use App\Observers\WarehouseObserver;
use App\Observers\TicketPriorityObserver;

use App\Models\Ticket;
use App\Models\TicketNote;
use App\Models\Spot;
use App\Models\Item;
use App\Models\User;
use App\Models\Tag;
use App\Models\Checklist;
use App\Models\ChecklistOption;
use App\Models\Team;
use App\Models\Asset;
use App\Models\Project;
use App\Models\SpotType;
use App\Models\TicketType;
use App\Models\UserTeam;
use App\Models\Role;
use App\Models\Cleaning\CleaningPlan;
use App\Models\Warehouse\Warehouse;
use App\Models\TicketPriority;

use App\Helpers\Helper;
use Session;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        
        if(Schema::hasTable('wh_ticket_status'))
        {

            view()->composer('*', function($view)
            {
                $organization = DB::table('wh_organization')->first();

                if(Auth::check())
                {
                    $ticketStatus   = new TicketStatusRepository;
                    $ticketPriority = new TicketPriorityRepository;
                    $item           = new ItemRepository();
                    $spot           = new SpotRepository();
                    $spotType       = new SpotTypeRepository;
                    $user           = new UserRepository;
                    $team           = new TeamRepository;
                    $ticketType     = new TicketTypeRepository;
                    $role           = new RoleRepository;
                    $tag            = new TagRepository;
                    $asset          = new AssetRepository;
                    $checklist      = new ChecklistRepository;
                    $role           = new RoleRepository;

                    View::share('global_statuses',     $ticketStatus->getList());
                    View::share('global_priorities',   $ticketPriority->getList());
                    View::share('global_items',        $item->getAll());
                    View::share('global_spots',        $spot->getList());
                    View::share('global_spot_types',   $spotType->getList());
                    View::share('global_users',        $user->getList());
                    View::share('global_teams',        $team->getList());
                    View::share('global_ticket_types', $ticketType->getList());
                    View::share('global_permissions',  $role->getPermissions());
                    View::share('global_tags',         $tag->getList());
                    View::share('global_assets',       $asset->getList());
                    View::share('global_checklist',    $checklist->getList());
                    View::share('global_roles',        $role->getList());

                    dd('view composer executed');
                }

                View::share('organization', json_encode($organization));
            });
        }
        
        //Register observers
        Ticket::observe(TicketObserver::class);
        TicketNote::observe(TicketNoteObserver::class);
        Spot::observe(SpotObserver::class);
        Item::observe(ItemObserver::class);
        User::observe(UserObserver::class);
        Checklist::observe(ChecklistObserver::class);
        ChecklistOption::observe(ChecklistOptionObserver::class);
        UserTeam::observe(UserTeamObserver::class);
        Role::observe(RoleObserver::class);
        Tag::observe(TagObserver::class);
        CleaningPlan::observe(CleaningPlanObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        TicketPriority::observe(TicketPriorityObserver::class);
        // Team::observe(TeamObserver::class);
        // Asset::observe(AssetObserver::class);
        // Project::observe(ProjectObserver::class);
        // SpotType::observe(SpotTypeObserver::class);
        // TicketType::observe(TicketTypeObserver::class);
    }
}
