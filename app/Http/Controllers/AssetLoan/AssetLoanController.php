<?php

namespace App\Http\Controllers\AssetLoan;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AssetLoanRepository;

class AssetLoanController extends Controller
{
    protected $assetLoanRepository;

    public function __construct()
    {
        $this->assetLoanRepository = new AssetLoanRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-assets", 'name' => "Assets"], ['name' => "Asset Loan"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('pages.assetLoan.index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getListLoanAPP(Request $request)
    {
        return $this->assetLoanRepository->getListLoanAPP($request);
    }

    public function createAssetLoanAPP(Request $request)
    {
        return $this->assetLoanRepository->createAssetLoanAPP($request);
    }

    public function getAssetLoanDetailsAPP(Request $request)
    {
        return $this->assetLoanRepository->getAssetLoanDetailsAPP($request);
    }

    public function getData(Request $request) 
    {
        return $this->assetLoanRepository->getData($request);
    }

    public function create(Request $request) 
    {
        return $this->assetLoanRepository->create($request);
    }

    public function update(Request $request) 
    {
        return $this->assetLoanRepository->update($request);
    }

    public function delete(Request $request) 
    {
        return $this->assetLoanRepository->delete($request);
    }

    public function changeStatus(Request $request) 
    {
        return $this->assetLoanRepository->changeStatus($request);
    }

    public function closeAssetLoanAPP(Request $request) 
    {
        return $this->assetLoanRepository->closeAssetLoanAPP($request);
    }

    public function getAssetLoanNotesAPP(Request $request) 
    {
        return $this->assetLoanRepository->getAssetLoanNotesAPP($request);
    }

    public function createAssetLoanNotesAPP(Request $request) 
    {
        return $this->assetLoanRepository->createAssetLoanNotesAPP($request);
    }

    public function deleteAssetLoanNotesAPP(Request $request) 
    {
        return $this->assetLoanRepository->deleteAssetLoanNotesAPP($request);
    }

    public function deleteAssetLoanAPP(Request $request) 
    {
        return $this->assetLoanRepository->deleteAssetLoanAPP($request);
    }
    
    public function getDetail(Request $request) 
    {
        $assetLoan = $this->assetLoanRepository->get($request);

        return view('pages.assetLoan.asset-loand-details', ['assetLoan' => $assetLoan]);
    }

    public function getLastChange(Request $request) 
    {
        return $this->assetLoanRepository->getLastChange($request);
    }
}
