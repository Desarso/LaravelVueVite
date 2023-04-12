<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TaskFavoriteRepository;

class TaskFavoriteController extends Controller
{
    protected $taskFavRepository;

    public function __construct()
    {
        $this->taskFavRepository = new TaskFavoriteRepository;
    }

    public function getFavoritesByIduser(Request $request)
    {
        return $this->taskFavRepository->getFavoritesByIduser($request);
    }

    public function saveFavorite(Request $request)
    {
        return $this->taskFavRepository->saveFavorite($request);
    }

    public function deleteFavoriteAPP(Request $request)
    {
        return $this->taskFavRepository->deleteFavorite($request);
    }

}