<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FilterRepository;

class FilterController extends Controller
{
    protected $filterRepository;

    public function __construct()
    {
        $this->filterRepository = new FilterRepository;
    }

    public function getList()
    {
        return $this->filterRepository->getList();
    }

    public function create(Request $request)
    {
        return $this->filterRepository->create($request);
    }

    public function delete(Request $request)
    {
        return $this->filterRepository->delete($request);
    }
    public function update(Request $request)
    {
        return $this->filterRepository->update($request);
    }
}
