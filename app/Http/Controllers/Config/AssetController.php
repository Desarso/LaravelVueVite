<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AssetRepository;
use App\Models\Asset;
use App\Repositories\AssetStatusRepository;
use App\Repositories\AssetCategoryRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\App;

class AssetController extends Controller
{
    protected $assetRepository;
    protected $assetCategoryRepository;
    protected $assetStatusRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->assetRepository         = new AssetRepository;
        $this->assetCategoryRepository = new AssetCategoryRepository;
        $this->assetStatusRepository   = new AssetStatusRepository;
    }

    public function index()
    {        
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Assets"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/assets/index', [            
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'categories'  => $this->assetCategoryRepository->getList(),
            'statuses'    => $this->assetStatusRepository->getList()  
        ]);
    }

    public function getAll()
    {
        return $this->assetRepository->getAll();
    }

    public function getAllEnabled()
    {
        return $this->assetRepository->getAllEnabled();
    }

    public function getList()
    {
        return $this->assetRepository->getList();
    }

    public function getAssetsData()
    {
        return $this->assetRepository->getAssetsData();
    }

    public function getAssetInfo(Request $request)
    {
        return $this->assetRepository->getAssetInfo($request);
    }

    public function saveAssetQRCode(Request $request)
    {
        return $this->assetRepository->saveAssetQRCode($request);
    }

    public function create(Request $request)
    {
        return $this->assetRepository->create($request);
    }

    public function updateOrCreate(Request $request)
    {
        return (is_null($request->id) == true) ? $this->assetRepository->create($request) : $this->assetRepository->update($request);
    }

    public function update(Request $request)
    {
        return $this->assetRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->assetRepository->delete($request);
    }

    public function getQR(Request $request)
    {
        $asset = Asset::select('id','name', 'code', 'photo')->where('id', $request->id)->first();

        return view('/pages.config.assets.asset-qr', ['asset' => $asset]);
    }

    public function downloadQR(Request $request)
    {
        return $this->assetRepository->downloadQR($request);
    }

    public function uploadImage(Request $request)
    {
        dd($request->all());
    }
    
    public function createTicket(Request $request)
    {
        return $this->asset->createTicket($request);
    }

    public function getAssetsSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Assets)->first()->settings;
        return json_decode($settings);
    }


    // APP`s functions

    public function getListAPP(Request $request)
    {
        return $this->assetRepository->getListAPP($request);
    }

    public function getAssetInfoAPP(Request $request)
    {
        return $this->assetRepository->getAssetInfoAPP($request);
    }

    public function searchAssetsAPP(Request $request)
    {
        return $this->assetRepository->searchAssetsAPP($request);
    }

    public function getTicketAssetAPP(Request $request)
    {
        return $this->assetRepository->getTicketAssetAPP($request);
    }
    
    public function getAssetTasks(Request $request)
    {
        return $this->assetRepository->getAssetTasks($request);
    }
}
