<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
 
use Illuminate\Http\Request;
use App\Repositories\FormsRepository;
use App\Repositories\ChecklistDataRepository;
use App\Models\Checklist;
use App\Models\ChecklistOption;

class FormsController extends Controller
{
    protected $forms;
    protected $checklistData;

    public function __construct(FormsRepository $forms)
    {
        $this->middleware('auth', ['only' => 'index']);        
        $this->forms         = new FormsRepository;
        $this->checklistData = new ChecklistDataRepository;
    }

    public function index()
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"],['name'=>"Forms"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,            
        ];

        return view('/pages/config/forms/index', [
            'forms'       => $this->forms->getList(),
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function editor(Request $request)
    {               
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['link'=>"/config-forms",'name'=>"Forms"],['name'=>"Editor"]
        ];
           
        $pageConfigs = [
            'verticalMenuNavbarType' => 'sticky',
            'pageHeader' => true,            
        ];

        return view('/pages/config/forms/editor', [
            'pageConfigs'   => $pageConfigs,
            'breadcrumbs'   => $breadcrumbs,
            'formSelected'  => Checklist::find($request->id),
            'globalForms'   => $this->forms->getList(),
            'checklistData' => $this->checklistData->getList()
        ]);
    }
 
    public function getAll()
    {
        return $this->forms->getAll();
    }

    public function getList()
    {
        return $this->forms->getList();
    }

    public function getDetails(Request $request)
    {
        return $this->forms->getDetails($request);
    }
 
    public function create(Request $request)
    {
        return $this->forms->create($request);
    }

    public function update(Request $request)
    {
        return $this->forms->update($request);
    }

    public function delete(Request $request)
    {
        return $this->forms->delete($request);
    }

    public function disable(Request $request)
    {
        return $this->forms->disable($request);
    }

    public function addFormItem(Request $request)
    {
        return $this->forms->addFormItem($request);
    }

    public function deleteFormItem(Request $request)
    {
        return $this->forms->deleteFormItem($request);
    }    

    public function updateFormItem(Request $request)
    {
        return $this->forms->updateFormItem($request);
    } 

    public function sortFormItems(Request $request)
    {
        return $this->forms->sortFormItems($request);
    }

    public function getFormProperties(Request $request)
    {
        $checklistOption = ChecklistOption::find($request->id);

        $merged = collect($checklistOption)->merge(json_decode($checklistOption->properties));

        return view('pages.config.forms.form-properties', ["checklistOption" => $merged]);
    }

    public function saveImage(Request $request)
    {
        return $this->forms->saveImage($request);
    }

    public function removeImage(Request $request)
    {
        return $this->forms->removeImage($request);
    }

    public function getFormPreview(Request $request)
    {
        return $this->forms->getPreview($request);
    }

    public function createFormCopy(Request $request)
    {
        return $this->forms->createCopy($request);
    }
}
