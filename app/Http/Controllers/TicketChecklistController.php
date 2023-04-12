<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TicketChecklistRepository;

class TicketChecklistController extends Controller
{
    protected $ticketChecklistRepository;

    public function __construct(TicketChecklistRepository $ticketChecklistRepository)
    {
        $this->ticketChecklistRepository = $ticketChecklistRepository;
    }

    public function get(Request $request)
    {
        return $this->ticketChecklistRepository->get($request);
    }

    public function save(Request $request)
    {
        return $this->ticketChecklistRepository->save($request);
    }

    public function generatePdf(Request $request)
    {
        return $this->ticketChecklistRepository->generatePdf($request);
    }

    public function viewPdf(Request $request)
    {
        return $this->ticketChecklistRepository->viewPdf($request);
    }

    public function assignEvaluator(Request $request)
    {
        return $this->ticketChecklistRepository->assignEvaluator($request);
    }

    public function getChecklistApp(Request $request)
    {
        return $this->ticketChecklistRepository->getChecklistApp($request);
    }

    public function getEvalutionUserChecklistAPP(Request $request)
    {
        return $this->ticketChecklistRepository->getEvalutionUserChecklistAPP($request);
    }

    public function addChecklistEvaluatorApp(Request $request)
    {
        return $this->ticketChecklistRepository->addChecklistEvaluatorApp($request);
    }

    public function synctTaskChecklistAPP(Request $request)
    {
        return $this->ticketChecklistRepository->synctTaskChecklistAPP($request);
    }

    public function sendPdfEmailAPP(Request $request)
    {
        return $this->ticketChecklistRepository->sendPdfEmail($request);
    }

}
