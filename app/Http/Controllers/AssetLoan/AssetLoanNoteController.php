<?php

namespace App\Http\Controllers\AssetLoan;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AssetLoanNoteRepository;

class AssetLoanNoteController extends Controller
{
    protected $assetLoanNoteRepository;

    public function __construct()
    {
        $this->assetLoanNoteRepository = new AssetLoanNoteRepository;
    }

    public function getNotes(Request $request)
    {
        return $this->assetLoanNoteRepository->getNotes($request);
    }

    public function create(Request $request)
    {
        return $this->assetLoanNoteRepository->create($request);
    }

    public function delete(Request $request)
    {
        return $this->assetLoanNoteRepository->delete($request);
    }
}
