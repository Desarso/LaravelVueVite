<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TagRepository;

class TagController extends Controller
{
    protected $spot;

    public function __construct(TagRepository $tag)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->tag = $tag;
    }

    public function createOnFly(Request $request)
    {
        return $this->tag->createOnFly($request);
    }

    public function getAllTagAPP(Request $request)
    {
        return $this->tag->getListApp();
    }
}
