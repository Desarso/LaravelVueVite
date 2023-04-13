<?php

namespace App\Http\Controllers\Tickets;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TicketRepository;
use App\Repositories\TicketAppRepository;
use App\Repositories\FilterRepository;
use App\Repositories\SpotRepository;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ticket;

class TicketController extends Controller
{
    protected $ticketRepository;
    protected $ticketAppRepository;
    protected $filterRepository;
    protected $spotRepository;

    public function __construct(TicketRepository $ticketRepository, TicketAppRepository $ticketAppRepository)
    {
        //$this->middleware('auth', ['only' => 'index']);
        $this->middleware('auth')->only(['index']);
        $this->middleware('ajax-session-expired')->except(['deleteFile']);

        $this->ticketRepository    = $ticketRepository;
        $this->ticketAppRepository = $ticketAppRepository;
        $this->filterRepository    = new FilterRepository;
        $this->spotRepository      = new SpotRepository;
    }

    public function index(Request $request)
    {
        $ticketsFilter = '[]';
        $filters = $this->filterRepository->getUserFilters();
        $hierarchySpots = json_encode($this->spotRepository->getHierarchy());    

        $pageConfigs = [
            'verticalMenuNavbarType' =>  'sticky', //'floating',
            'pageHeader' => false,            
        ];

        if($request->has('tickets'))
        {
            $ticketsFilter = $this->getFilterDefault($request->tickets);
        }
        
        return view('task.dashboard-tasks', ['pageConfigs' => $pageConfigs, 'filters' => $filters, 'hierarchySpots' => $hierarchySpots, "ticketsFilter" => $ticketsFilter]);
    }

    public function resumeView(Request $request)
    {
        $ticket = $this->ticketRepository->getTicketResume($request);

        return view("task.resume", [ "ticket" => $ticket ]);
    }

    public function getAll(Request $request)
    {
        return $this->ticketRepository->getAll($request);
    }
    
    public function getStats(Request $request)
    {
        return $this->ticketRepository->getStats($request);
    }

    public function getMyStats(Request $request)
    {
        return $this->ticketRepository->getMyStats($request);
    }

    public function getStatus(Request $request)
    {
        return $this->ticketRepository->getStatus($request);
    }

    public function create(Request $request)
    {
        return $this->ticketRepository->create($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->ticketRepository->changeStatus($request);
    }

    public function get(Request $request)
    {
        return $this->ticketRepository->get($request);
    }

    public function update(Request $request)
    {
        return $this->ticketRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->ticketRepository->delete($request);
    }

    public function verify(Request $request)
    {
        return $this->ticketRepository->verify($request);
    }

    public function uploadFile(Request $request)
    {
        return $this->ticketRepository->uploadFile($request);
    }

    public function deleteFile(Request $request)
    {
        return $this->ticketRepository->deleteFile($request);
    }

    public function setDuration(Request $request)
    {
        return $this->ticketRepository->setDuration($request);
    }

    public function escalate(Request $request)
    {
        return $this->ticketRepository->escalate($request);
    }

    public function getLast()
    {
        return $this->ticketRepository->getLast();
    }

    public function getEssentialProductTickets(Request $request)
    {
        return $this->ticketRepository->getEssentialProductTickets($request);
    }

    // For Grid (Cleaning Dashboard)
    public function getAllEssentialProductTickets(Request $request) {
        return $this->ticketRepository->getAllEssentialProductTickets($request);
    }

    public function export(Request $request) 
    {
        $myFile = Excel::raw(new TicketsExport($request), \Maatwebsite\Excel\Excel::XLSX);

        $response = array(
            'name' => "Tickets", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
         );
         
        return response()->json($response);
    }

    /************************* APP function ***********************/

    
    public function getAllTicketsApp(Request $request)
    {
        return $this->ticketAppRepository->getAllApp($request);
    }
    
    public function getNewTicketsApp(Request $request)
    {
        return $this->ticketAppRepository->getNewTicketsApp($request);
    }
    
    public function deleteTaskApp(Request $request)
    {
        return $this->ticketAppRepository->deleteTaskApp($request);
    }
    
    public function approvedTaskApp(Request $request)
    {
        return $this->ticketAppRepository->approvedTaskApp($request);
    }
    
    public function syncToServer(Request $request)
    {
        return $this->ticketAppRepository->syncToServer($request);
    }
    
    public function updateTaskApp(Request $request)
    {
        return $this->ticketAppRepository->updateTaskApp($request);
    }
    
    public function signTaskAPP(Request $request)
    {
        return $this->ticketAppRepository->signTaskAPP($request);
    }
    
    public function createTaskAPP(Request $request)
    {
        return $this->ticketAppRepository->createTaskAPP($request);
    }
    
    public function getTaskCleaningProduct(Request $request)
    {
        return $this->ticketAppRepository->getTaskCleaningProduct($request);
    }
    
    public function getTaskByIdspot(Request $request)
    {
        return $this->ticketAppRepository->getTaskByIdspot($request);
    }
    
    public function changeStatusApp(Request $request)
    {
        return $this->ticketAppRepository->changeStatusApp($request);
    }
    
    public function escalateTaskApp(Request $request)
    {
        return $this->ticketRepository->escalate($request);
    }
    
    public function searchTicket(Request $request)
    {
        return $this->ticketAppRepository->searchTicket($request);
    }
    
    public function findTicketsApp(Request $request)
    {
        return $this->ticketAppRepository->findTicketsApp($request);
    }
    
    public function getgeneralStats(Request $request)
    {
        return $this->ticketRepository->getgeneralStats($request);
    }
    
    public function getTrendStats(Request $request)
    {
        return $this->ticketRepository->getTrends($request);
    }

    public function syncFromExcel(Request $request)
    {
        return $this->ticketRepository->syncFromExcel($request);
    }

    public function uploadBase64(Request $request)
    {
        return $this->ticketAppRepository->uploadBase64($request);
    }

    public function deleteImage(Request $request)
    {
        return $this->ticketAppRepository->deleteImage($request);
    }

    private function getFilterDefault($tickets)
    {
        $filters = ["logic" => "or", "filters" => []];
        $items = [];

        $tickets = explode(",", $tickets);

        foreach ($tickets as $ticket)
        {
            $item = (object) array("field" => "id", "value" => $ticket, "operator" => "eq");
            array_push($items, $item);
        }
        $filters["filters"] = $items;

        return json_encode($filters);
    }

    public function checkTaskExistsAPP(Request $request)
    {
        return $this->ticketAppRepository->checkTaskExistsAPP($request);
    }

    public function assignTaskApp(Request $request)
    {
        return $this->ticketAppRepository->assignTaskApp($request);
    }

    public function getDataToPowerBI(Request $request)
    {
        return $this->ticketRepository->getDataToPowerBI($request);
    }

    public function showMsFilesApp(Request $request)
    {
        return $this->ticketAppRepository->showMsFilesApp($request);
    }
}
