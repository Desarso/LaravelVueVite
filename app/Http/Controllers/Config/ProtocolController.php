<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Repositories\ProtocolRepository;
use App\Repositories\ProtocolTypeRepository;
use Illuminate\Http\Request;
use App\Models\Protocol;

class ProtocolController extends Controller
{
    protected $protocol;
    protected $protocoltype;

    public function __construct(ProtocolRepository $protocol, ProtocolTypeRepository $protocoltype)
    {
        $this->protocol = $protocol;
        $this->protocoltype = $protocoltype;
    }

    public function index()
    {
        
        $breadcrumbs = [
            ['link' => "/", 'name' => 'Home'], ['link' => "/config-dashboard", 'name' => "Configuration"], ['name' => "Protocols"],
        ];

        $pageConfigs = [
            'pageHeader' => true,
        ];

        return view('/pages/config/protocols/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'categories'  => $this->protocoltype->getList()
        ]);
    }

    public function getAll()
    {
        return $this->protocol->getAll();
    }

    public function getList()
    {
        return $this->protocol->getList();
    }

    public function create(Request $request)
    {
        return $this->protocol->create($request);
    }

    public function update(Request $request)
    {
        return $this->protocol->update($request);
    }

    public function delete(Request $request)
    {
        return $this->protocol->delete($request);
    }

    public function saveHtml(Request $request)
    {
        return $this->protocol->saveHtml($request);
    }

    public function show(Request $request)
    {
        return $this->protocol->show($request);
    }

    public function showProtocolApp(Request $request)
    {
        return $this->protocol->showProtocolApp($request);
    }

    public function getListApp(Request $request)
    {
        return $this->protocol->getListApp($request);
    }
}
