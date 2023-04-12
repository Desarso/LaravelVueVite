<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ItemRepository;
use App\Repositories\TicketTypeRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Repositories\SpotRepository;
use App\Repositories\ProtocolRepository;
use App\Repositories\TicketPriorityRepository;

class ItemController extends Controller
{
    protected $item;
    protected $protocolRepository;
    protected $spotRepository;

    public function __construct(ItemRepository $item)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->item = $item;
        $this->protocolRepository = new ProtocolRepository;
        $this->spotRepository = new SpotRepository;
    }

    public function index(Request $request)
    {
        $spots = json_encode($this->spotRepository->getHierarchy());  

        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"],['name'=>"Items"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true  
        ];

        return view('/pages/config/items/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,           
            'spots'       => $spots,
            'protocols'   => $this->protocolRepository->getList(),
            'open'        => ($request->has('open') ? 'true' : 'false')
        ]);
    }

    public function getAll()
    {
        return $this->item->getAll();
    }

    public function getList()
    {
        return $this->item->getList();
    }

    public function getCleaningItems()
    {
        return $this->item->getCleaningItems();

    }

    public function create(Request $request)
    {
        return $this->item->create($request);
    }

    public function createOnFly(Request $request)
    {
        return $this->item->createOnFly($request);
    }

    public function update(Request $request)
    {
        return $this->item->update($request);
    }

    public function delete(Request $request)
    {
        return $this->item->delete($request);
    }

    public function restore(Request $request)
    {
        return $this->item->restore($request);
    }

    public function getListApp(Request $request)
    {
        return $this->item->getListApp(null, $request);
    }

    public function saveSpots(Request $request) 
    {
        return $this->item->saveSpots($request);
    }

    public function getCleaningTypesAPP(Request $request) 
    {
        return $this->item->getCleaningTypesAPP($request);
    }

    public function getCleaningProductsAPP(Request $request) 
    {
        return $this->item->getCleaningProductsAPP($request);
    }

    public function searchCleaningProductsAPP(Request $request) 
    {
        return $this->item->searchCleaningProductsAPP($request);
    }
}
