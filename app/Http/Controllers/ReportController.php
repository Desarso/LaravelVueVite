<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Exports\ClockinMapExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\Cleaning\CleaningStatusRepository;
use App\Repositories\ItemRepository;
use App\Repositories\Reports\ReportAttendanceOvertimeRepository;
use App\Repositories\Reports\ReportAttendanceRepository;
use App\Repositories\Reports\ReportBranchRepository;
use App\Repositories\Reports\ReportChecklistNoteRepository;
use App\Repositories\Reports\ReportChecklistRepository;
use App\Repositories\Reports\ReportCleaningRepository;
use App\Repositories\Reports\ReportDurationRepository;
use App\Repositories\Reports\ReportItemRepository;
use App\Repositories\Reports\ReportLocationRepository;
use App\Repositories\Reports\ReportOrganizationRepository;
use App\Repositories\Reports\ReportPriorityRepository;
use App\Repositories\Reports\ReportProductivityRepository;
use App\Repositories\Reports\ReportTaskAverageRepository;
use App\Repositories\Reports\ReportTaskRepository;
use App\Repositories\Reports\ReportTeamRepository;
use App\Repositories\Reports\ReportUserRepository;
use App\Repositories\Reports\ReportClockinRepository;
use App\Repositories\Reports\ReportClockinDeviceRepository;
use App\Repositories\Reports\ReportClockinMapRepository;
use App\Repositories\Reports\ReportClockinTimeRepository;
use App\Repositories\Reports\ReportGeneralRepository;
use App\Repositories\Reports\ReportChecklistInvoiceRepository;
use App\Repositories\Reports\ReportChecklistDurationRepository;
use App\Repositories\Reports\ReportTasksSummaryRepository;
use App\Repositories\Reports\ReportTeamsSummaryRepository;
use App\Repositories\Reports\ReportChecklistReviewRepository;
use App\Repositories\Reports\ReportChecklistReviewRepository2;
use App\Repositories\Reports\ReportChecklistSummaryRepository;
use App\Repositories\Reports\ReportManagementBranchRepository;
use App\Repositories\Reports\ReportChecklistReview3Repository;
use App\Repositories\Reports\ReportDueTasksRepository;
//use App\Repositories\AttendanceLogRepository;


class ReportController extends Controller
{
    protected $reportPriorityRepository;
    protected $reportCleaningRepository;
    protected $reportChecklistRepository;
    protected $reportOrganizationRepository;
    protected $reportTeamRepository;
    protected $reportUserRepository;
    protected $reportItemRepository;
    protected $reportTaskAverageRepository;
    protected $reportAttendanceRepository;
    //protected $attendanceLogRepository;
    protected $cleaningStatusRepository;
    protected $productivityRepository;
    protected $itemRepository;
    protected $reportTaskRepository;
    protected $reportDurationRepository;
    protected $reportAttendanceOvertimeRepository;
    protected $reportLocationRepository;
    protected $reportBranchRepository;
    protected $reportChecklistNoteRepository;
    protected $reportClockinRepository;
    protected $reportClockinDeviceRepository;
    protected $reportClockinMapRepository;
    protected $reportClockinTimeRepository;
    protected $reportGeneralRepository;
    protected $reportChecklistInvoiceRepository;
    protected $reportChecklistDurationRepository;
    protected $reportTasksSummaryRepository;
    protected $reportTeamsSummaryRepository;
    protected $reportChecklistReviewRepository;
    protected $reportChecklistReviewRepository2;
    protected $reportChecklistSummaryRepository;
    protected $reportManagementBranchRepository;
    protected $reportChecklistReview3Repository;
    protected $reportDueTasksRepository;

    public function __construct()
    {
        $this->reportPriorityRepository = new ReportPriorityRepository;
        $this->reportCleaningRepository = new ReportCleaningRepository;
        $this->reportOrganizationRepository = new ReportOrganizationRepository;
        $this->reportChecklistRepository    = new ReportChecklistRepository;
        $this->reportTeamRepository         = new ReportTeamRepository;
        $this->reportUserRepository         = new ReportUserRepository;
        $this->reportTaskRepository         = new ReportTaskRepository;
        $this->reportItemRepository         = new ReportItemRepository;
        $this->reportTaskAverageRepository  = new ReportTaskAverageRepository;
        $this->reportAttendanceRepository   = new ReportAttendanceRepository;
        $this->cleaningStatusRepository     = new CleaningStatusRepository;
        $this->productivityRepository       = new ReportProductivityRepository;
        $this->itemRepository               = new ItemRepository;
        $this->reportDurationRepository     = new ReportDurationRepository;
        $this->reportAttendanceOvertimeRepository = new ReportAttendanceOvertimeRepository;
        $this->reportLocationRepository           = new ReportLocationRepository;
        $this->reportBranchRepository             = new ReportBranchRepository;
        $this->reportChecklistNoteRepository      = new ReportChecklistNoteRepository;
        $this->reportClockinRepository            = new ReportClockinRepository;
        $this->reportClockinDeviceRepository      = new ReportClockinDeviceRepository;
        $this->reportClockinMapRepository         = new ReportClockinMapRepository;
        $this->reportClockinTimeRepository        = new ReportClockinTimeRepository;
        $this->reportGeneralRepository            = new ReportGeneralRepository;
        $this->reportChecklistInvoiceRepository   = new ReportChecklistInvoiceRepository;
        $this->reportChecklistDurationRepository  = new ReportChecklistDurationRepository;
        $this->reportTasksSummaryRepository       = new ReportTasksSummaryRepository;
        $this->reportTeamsSummaryRepository       = new ReportTeamsSummaryRepository;
        $this->reportChecklistReviewRepository    = new ReportChecklistReviewRepository;
        $this->reportChecklistReviewRepository2   = new ReportChecklistReviewRepository2;
        $this->reportChecklistSummaryRepository   = new ReportChecklistSummaryRepository;
        $this->reportManagementBranchRepository   = new ReportManagementBranchRepository;
        $this->reportChecklistReview3Repository   = new ReportChecklistReview3Repository;
        $this->reportDueTasksRepository           = new ReportDueTasksRepository;
    }

    //Inicio reporte de prioridades
    public function viewReportPriority()
    {
        return view('/reports/report-priority', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getTicketPriority(Request $request)
    {
        return $this->reportPriorityRepository->getTicketPriority($request);
    }

    public function getDataPriority(Request $request)
    {
        return $this->reportPriorityRepository->getDataPriority($request);
    }

    public function getUserPriority(Request $request)
    {
        return $this->reportPriorityRepository->getUserPriority($request);
    }

    public function getEfficiencyPriority(Request $request)
    {
        return $this->reportPriorityRepository->getEfficiencyPriority($request);
    }
    //Fin reporte de prioridades

    //Inicio reporte de solicitudes de limpieza
    public function viewReportCleaningRequest()
    {
        return view('/reports/report-cleaning-request', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataCleaningRequest(Request $request)
    {
        return $this->reportCleaningRepository->getDataCleaningRequest($request);
    }

    public function getDataCleaningTicketType(Request $request)
    {
        return $this->reportCleaningRepository->getDataCleaningTicketType($request);
    }

    public function getCleaningRequestItems(Request $request)
    {
        return $this->reportCleaningRepository->getCleaningRequestItems($request);
    }

    public function getCleaningRequestTicketTypes(Request $request)
    {
        return $this->reportCleaningRepository->getCleaningRequestTicketTypes($request);
    }
    //Fin reporte de solicitudes de limpieza

    //Inicio reporte de checklist
    public function viewReportChecklist()
    {
        return view('/reports/report-checklist', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataChecklist(Request $request)
    {
        return $this->reportChecklistRepository->getData($request);
    }

    //Inicio reporte de organizacion
    public function viewReportOrganization()
    {
        return view('/reports/report-organization', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getChecklistGroupBySpot(Request $request)
    {
        return $this->reportOrganizationRepository->getChecklistGroupBySpot($request);
    }

    public function getChecklistBranchReportBySection(Request $request)
    {
        return $this->reportOrganizationRepository->getChecklistBranchReportBySection($request);
    }

    public function getChecklistBranchReport(Request $request)
    {
        return $this->reportOrganizationRepository->getChecklistBranchReport($request);
    }
    //Fin reporte de checklist

    //Inicio reporte de limpieza
    public function viewReportCleaning()
    {
        return view('reports.report-cleaning', [
            'pageConfigs' => ['pageHeader' => true],
            'cleaningStatuses' => $this->cleaningStatusRepository->getList(),
            'cleaningItems' => $this->itemRepository->getCleaningItems(),
        ]);
    }

    public function getDataCleaning(Request $request)
    {
        return $this->reportCleaningRepository->getDataCleaning($request);
    }
    //Fin reporte de limpieza

    //Inicio reporte de equipos
    public function viewReportTeam()
    {
        return view('reports.report-team', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    public function getDataTeam(Request $request)
    {
        return $this->reportTeamRepository->getData($request);
    }

    public function getDataByTeam(Request $request)
    {
        return $this->reportTeamRepository->getDataByTeam($request);
    }
    //Fin reporte de equipos

    //Inicio reporte de usuarios
    public function viewReportUser()
    {
        return view('reports.report-user', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    public function getDataUserReports(Request $request)
    {
        return $this->reportUserRepository->getDataUserReports($request);
    }

    public function getDataUserTickets(Request $request)
    {
        return $this->reportUserRepository->getDataUserTickets($request);
    }

    public function getUserTicketsDetails(Request $request)
    {
        return $this->reportUserRepository->getUserTicketsDetails($request);
    }
    //Fin reporte de usuarios

    //Inicio reporte de Items
    public function viewReportItem()
    {
        return view('reports.report-item', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    //Inicio reporte de tareas
    public function viewReportTask()
    {
        return view('reports.report-task', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    public function getFrequenteItemsReport(Request $request)
    {
        return $this->reportItemRepository->getFrequenteItemsReport($request);
    }

    public function getDataTaskBySporReport(Request $request)
    {
        return $this->reportItemRepository->getTaskBySporReport($request);
    }
    //Fin reporte de Items

    public function getDataSpotTickets(Request $request)
    {
        return $this->reportTaskRepository->getDataSpotTickets($request);
    }

    public function getSpotTickets(Request $request)
    {
        return $this->reportTaskRepository->getSpotTickets($request);
    }

    public function getDataItemTickets(Request $request)
    {
        return $this->reportTaskRepository->getDataItemTickets($request);
    }

    public function getItemTickets(Request $request)
    {
        return $this->reportTaskRepository->getItemTickets($request);
    }
    //Fin reporte de tareas

    //Inicio reporte de checklist de auditoría
    public function viewReportChecklistAudit()
    {
        return view('reports.report-checklist-audit', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataChecklistAudit(Request $request)
    {
        return $this->reportChecklistRepository->getDataChecklistAudit($request);
    }

    public function getChecklistDetail(Request $request)
    {
        return $this->reportChecklistRepository->getChecklistDetail($request);
    }

    public function sendEmailAudit(Request $request)
    {
        return $this->reportChecklistRepository->sendEmailAudit($request->id);
    }

    //Inicio reporte de promedio de tareas por sede

    public function viewReportAverageBranch()
    {
        // dd("Hola!");
        return view('reports.report-average-branch', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    public function getDataAverageReport(Request $request)
    {
        return $this->reportTaskAverageRepository->getAverageReport($request);
    }
    //Fin reporte de promedio de tareas por sede

    //Inicio reporte de productividad

    public function viewReportProductivity()
    {
        return view('reports.report-productivity', [
            'pageConfigs' => ['pageHeader' => true],
        ]);
    }

    public function getProductivityByTeam(Request $request)
    {
        return $this->productivityRepository->getProductivityByTeam($request);
    }

    public function getProductivityByUser(Request $request)
    {
        return $this->productivityRepository->getProductivityByUser($request);
    }

    public function getProductivityGeneral(Request $request)
    {
        return $this->productivityRepository->getProductivityGeneral($request);
    }
    //Fin reporte de productividad
    //Fin reporte de checklist de auditoría

    //Inicio reporte de asistencia
    public function viewReportAttendance()
    {
        return view('reports.report-attendance', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataAttendance(Request $request)
    {
        return $this->reportAttendanceRepository->getData($request);
    }

    public function viewReportAttendanceLog(Request $request)
    {
        return view('reports.report-attendance-log', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataAttendanceLog(Request $request)
    {
        //return $this->attendanceLogRepository->getData($request);
    }

    //Inicio reporte de duraciones
    public function viewReportDuration()
    {
        return view('reports.report-duration', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataUserDuration(Request $request)
    {
        return $this->reportDurationRepository->getDataUserDuration($request);
    }

    public function getDataSpotDuration(Request $request)
    {
        return $this->reportDurationRepository->getDataSpotDuration($request);
    }

    //Inicio reporte de tiempos extra
    public function viewReportOvertime()
    {
        return view('reports.report-overtime', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataOvertime(Request $request)
    {
        return $this->reportAttendanceOvertimeRepository->getDataResume($request);
    }

    public function getDataOvertimeAPP(Request $request)
    {
        return $this->reportAttendanceOvertimeRepository->getDataResumeAPP($request);
    }

    public function getOvertimeDetails(Request $request)
    {
        return $this->reportAttendanceOvertimeRepository->getOvertimeDetails($request);
    }

    //Inicio reporte de localizaciones
    public function viewReportLocation()
    {
        return view('reports.report-location', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataLocation(Request $request)
    {
        return $this->reportLocationRepository->getData($request);
    }

    //Inicio reporte de sucursales
    public function viewReportBranch()
    {
        return view('reports.report-branch', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataBranchBySpot(Request $request)
    {
        return $this->reportBranchRepository->getDataBranchBySpot($request);
    }

    public function getDataBranchBySection(Request $request)
    {
        return $this->reportBranchRepository->getDataBranchBySection($request);
    }

    public function getDataBranch(Request $request)
    {
        return $this->reportBranchRepository->getDataBranch($request);
    }
    //Fin reporte de sucursales

    //Inicio reporte de notas del checklist
    public function viewReportChecklistNote()
    {
        return view('reports.report-checklist-note', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getDataChecklistNote(Request $request)
    {
        return $this->reportChecklistNoteRepository->getData($request);
    }

    public function generatePdfChecklistNote(Request $request)
    {
        return $this->reportChecklistNoteRepository->generatePdf($request);
    }

    // Inicio reporte Teams Report
    public function viewTeamsReport()
    {
       
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/reports-dashboard",'name'=>"Reports"], ['name'=>"Teams Report"]
        ];
           
        return view('reports.report-teams', [
            'pageConfigs' => ['pageHeader' => true, 'verticalMenuNavbarType' => 'sticky'],
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    //Fin reporte Teams Report


    // Inicio reporte Users Report
    public function viewUsersReport()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/reports-dashboard",'name'=>"Reports"], ['name'=>"Users Report"]
        ];
        return view('reports.report-users', [
            'pageConfigs' => ['pageHeader' => true, 'verticalMenuNavbarType' => 'sticky'],
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    //Inicio reporte de clockin
    public function viewReportClockin()
    {
        return view('reports.report-clockin', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataClockin(Request $request)
    {
        return $this->reportClockinRepository->getData($request);
    }

    public function getClockinDetails(Request $request)
    {
        return $this->reportClockinRepository->getClockinDetails($request);
    }

    //Inicio reporte de clockin device
    public function viewReportClockinDevice()
    {
        return view('reports.report-clockin-device', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataClockinDevice(Request $request)
    {
        return $this->reportClockinDeviceRepository->getData($request);
    }

    // report clockin-map
    public function viewReportclockinMap()
    {
        return view('reports.report-clockin-map', ['pageConfigs' => ['pageHeader' => true]]);
    }

    public function getClockinDataByUser(Request $request)
    {
        return $this->reportClockinMapRepository->getClockinDataByUser($request);
    }

    public function getClockinData(Request $request)
    {
        return $this->reportClockinMapRepository->getClockinData($request);
    }

    public function getLastClockinChange()
    {
        return $this->reportClockinMapRepository->getLastClockinChange();
    }

    public function exportClockinMap(Request $request) 
    {
        $myFile = Excel::raw(new ClockinMapExport($request), \Maatwebsite\Excel\Excel::XLSX);

        $response = array(
            'name' => "Marcas de personal", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
         );
         
        return response()->json($response);
    }

    //Inicio reporte General
    public function viewReportGeneral()
    {
        return view('reports.report-general', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getActivityByDates(Request $request)
    {
        return $this->reportGeneralRepository->getDataActivity($request);
    }

    public function getDataActivityBySpot(Request $request)
    {
        return $this->reportGeneralRepository->getDataActivityBySpot($request);
    }

    public function getDataActivityByItem(Request $request)
    {
        return $this->reportGeneralRepository->getDataActivityByItem($request);
    }

    public function getDataEfficacy(Request $request)
    {
        return $this->reportGeneralRepository->getDataEfficacy($request);
    }

    //Inicio reporte Checklist Invoice
    public function viewReportChecklistInvoice()
    {
        return view('reports.report-checklist-invoice', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistInvoice(Request $request)
    {
        return $this->reportChecklistInvoiceRepository->getData($request);
    }

    //Inicio reporte Checklist Duration
    public function viewReportChecklistDuration()
    {
        return view('reports.report-checklist-duration', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistDuration(Request $request)
    {
        return $this->reportChecklistDurationRepository->getData($request);
    }

    public function getDataChecklistDurationDetail(Request $request)
    {
        return $this->reportChecklistDurationRepository->getDataDetail($request);
    }

    //Inicio reporte Resumen Tareas
    public function viewReportTasksSummary()
    {
        return view('reports.report-tasks-summary', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataTasksSummary(Request $request)
    {
        return $this->reportTasksSummaryRepository->getDataTicketTypeSummary($request);
    }

    public function getDataItemSummary(Request $request)
    {
        return $this->reportTasksSummaryRepository->getDataItemSummary($request);
    }

    public function getDataTicketTypeSummary(Request $request)
    {
        return $this->reportTasksSummaryRepository->getDataTicketTypeSummary($request);
    }

    public function getDataSpotSummary(Request $request)
    {
        return $this->reportTasksSummaryRepository->getDataSpotSummary($request);
    }

    public function getTicketSummaryByMonth(Request $request)
    {
        return $this->reportTasksSummaryRepository->getTicketSummaryByMonth($request);
    }

    public function getTicketSummary(Request $request)
    {
        return $this->reportTasksSummaryRepository->getTicketSummary($request);
    }

    //Inicio reporte Resumen Equipos
    public function viewReportTeamsSummary()
    {
        return view('reports.report-teams-summary', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getTeamSummary(Request $request)
    {
        return $this->reportTeamsSummaryRepository->getTeamSummary($request);
    }

    public function getTeamSummaryByStatus(Request $request)
    {
        return $this->reportTeamsSummaryRepository->getTeamSummaryByStatus($request);
    }

    public function getDataTeamsSummary(Request $request)
    {
        return $this->reportTeamsSummaryRepository->getDataTeamsSummary($request);
    }

    public function getDataTeamUserSummary(Request $request)
    {
        return $this->reportTeamsSummaryRepository->getDataTeamUserSummary($request);
    }



    //Inicio reporte Checklist revisión
    public function viewReportChecklistReview()
    {
        return view('reports.report-checklist-review', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistReview(Request $request)
    {
        return $this->reportChecklistReviewRepository->getDataChecklistReview($request);
    }

    public function getDataChecklistReviewBySection(Request $request)
    {
        return $this->reportChecklistReviewRepository->getDataChecklistReviewBySection($request);
    }

    public function getDataChecklistReviewByOption(Request $request)
    {
        return $this->reportChecklistReviewRepository->getDataChecklistReviewByOption($request);
    }

    //Inicio reporte Checklist revisión 2
    public function viewReportChecklistReview2()
    {
        return view('reports.report-checklist-review2', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistReview2(Request $request)
    {
        return $this->reportChecklistReviewRepository2->getData($request);
    }

    public function getDataChecklistReviewByOption2(Request $request)
    {
        return $this->reportChecklistReviewRepository2->getDataOption($request);
    }

    public function getDataChecklistReviewBySpot2(Request $request)
    {
        return $this->reportChecklistReviewRepository2->getDataSpot($request);
    }

    //Inicio reporte Resumen Spots
    public function viewReportSpotsSummary()
    {
        return view('reports.report-spots-summary', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    //Inicio reporte checklist Resumen 
    public function viewReportChecklistSummary()
    {
        return view('reports.report-checklist-summary', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistSummary(Request $request)
    {
        return $this->reportChecklistSummaryRepository->getDataChecklistSummary($request);
    }

    //Inicio reporte de clockin time
    public function viewReportClockinTime()
    {
        return view('reports.report-clockin-time', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataClockinTime(Request $request)
    {
        return $this->reportClockinTimeRepository->getData($request);
    }

    public function getClockinTimeDetails(Request $request)
    {
        return $this->reportClockinTimeRepository->getClockinDetails($request);
    }


    //Inicio Report Management Branch
    public function viewReportManagementBranch()
    {
        return view('reports.report-management-branch', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistManagement(Request $request)
    {
        return $this->reportManagementBranchRepository->getData($request);
    }

    public function getDataChecklistManagementGroup(Request $request)
    {
        return $this->reportManagementBranchRepository->getDataGroup($request);
    }    

    public function getDataChecklistManagementOption(Request $request)
    {
        return $this->reportManagementBranchRepository->getDataOption($request);
    }  

    //Inicio reporte Checklist revisión 3
    public function viewReportChecklistReview3()
    {
        return view('reports.report-checklist-review3', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataChecklistReview3(Request $request)
    {
        return $this->reportChecklistReview3Repository->getDataChecklistReview($request);
    }  

    public function getDataChecklistReview3Detail(Request $request)
    {
        return $this->reportChecklistReview3Repository->getDataChecklistReviewDetail($request);
    }  

    public function getDataChecklistReview3Notes(Request $request)
    {
        return $this->reportChecklistReview3Repository->getDataChecklistReviewNotes($request);
    }  

    //Inicio Report Due Tasks
    public function viewReportDueTasks()
    {
        return view('reports.report-due-tasks', [ 'pageConfigs' => ['pageHeader' => true] ]);
    }

    public function getDataDueTasksBySpot(Request $request)
    {
        return $this->reportDueTasksRepository->getDataBySpot($request);
    }  

    public function getDataDueTasksByTeam(Request $request)
    {
        return $this->reportDueTasksRepository->getDataByTeam($request);
    }  

    public function getDataDueTasksByItem(Request $request)
    {
        return $this->reportDueTasksRepository->getDataByItem($request);
    }  

    public function getDataDueTasks(Request $request)
    {
        return $this->reportDueTasksRepository->getData($request);
    }  

}


 