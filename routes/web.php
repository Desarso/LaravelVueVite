<?php

// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
// use App\Http\Controllers\Config\AppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Login
Auth::routes();
//Login


Route::get('/thingy', function () {
    return inertia('welcome');
});

Route::namespace('App\Http\Controllers\Config')->group(function () {


    // TicketTypes
    Route::get('/config-tasktypes', 'TicketTypeController@index')->middleware('check_if_admin'); // compatibilidad con json para dashbord, por eso es tasktypes y no tickettypes
    Route::get('getTicketTypes', 'TicketTypeController@getAll'); // para la grilla
    Route::get('getListTicketTypes', 'TicketTypeController@getList'); // retorna name, value para llenar listas
    Route::post('createTicketTypeOnFly', 'TicketTypeController@createOnFly')->middleware('permission');
    Route::post('createTicketType', 'TicketTypeController@create');
    Route::post('updateTicketType', 'TicketTypeController@update');
    Route::post('deleteTicketType', 'TicketTypeController@delete');
    Route::post('restoreTicketType', 'TicketTypeController@restore');

    //Items
    Route::get('/config-items', 'ItemController@index')->middleware('check_if_admin');
    Route::get('getItems', 'ItemController@getAll');   // para la grilla
    Route::get('getListItems', 'ItemController@getList'); // retorna name, value para llenar listas
    Route::get('getCleaningItems', 'ItemController@getCleaningItems');
    Route::post('saveItemSpots', 'ItemController@saveSpots');
    Route::post('createItemOnFly', 'ItemController@createOnFly')->middleware('permission');
    Route::post('createItem', 'ItemController@create');
    Route::post('updateItem', 'ItemController@update');
    Route::post('deleteItem', 'ItemController@delete');
    Route::post('restoreItem', 'ItemController@restore');


    //SpotTypes
    Route::get('/config-spottypes', 'SpotTypeController@index')->middleware('check_if_admin');
    Route::get('getSpotTypes', 'SpotTypeController@getAll');   // para la grilla
    Route::get('getListSpotTypes', 'SpotTypeController@getList'); // retorna name, value para llenar listas
    Route::post('createSpotType', 'SpotTypeController@create');
    Route::post('updateSpotType', 'SpotTypeController@update');
    Route::post('deleteSpotType', 'SpotTypeController@delete');
    Route::post('restoreSpotType', 'SpotTypeController@restore');

    //Spots
    Route::get('/test', 'SpotController@test');


    Route::get('/config-spots', 'SpotController@index')->middleware('check_if_admin');
    Route::get('getSpots', 'SpotController@getAll');
    Route::post('createSpot', 'SpotController@create');
    Route::post('updateSpot', 'SpotController@update');
    Route::post('deleteSpot', 'SpotController@delete');
    Route::post('restoreSpot', 'SpotController@restore');
    Route::get('getRequireCleaningSpots', 'SpotController@getRequireCleaningSpots');
    Route::get('getListSpots', 'SpotController@getList'); // retorna name, value para llenar listas
    Route::get('getHierarchySpots', 'SpotController@getHierarchy');
    //Route::get('getAllSpotsTreeList', 'SpotController@getAllSpotsTreeList');
    Route::post('createSpotOnFly', 'SpotController@createOnFly')->middleware('permission');
    Route::get('getChildrenSpot', 'SpotController@getChildren');


    //Dashboard
    Route::get('/config-dashboard', 'DashboardController@index');
    Route::get('/config-stats', 'DashboardController@stats');
    Route::get('not-authorized', 'DashboardController@notAuthorized');


    //Planner
    Route::get('/config-planner', 'PlannerController@index')->middleware('check_if_admin');
    Route::get('getPlanner', 'PlannerController@getAll');
    Route::get('getAllScheduler', 'PlannerController@getAllScheduler');
    Route::get('getListPlanner', 'PlannerController@getList'); // retorna name, value para llenar listas
    Route::post('createPlanner', 'PlannerController@create');
    Route::post('updatePlanner', 'PlannerController@update');
    Route::post('deletePlanner', 'PlannerController@delete');
    Route::post('enabledPlanner', 'PlannerController@enabledPlanner');
    Route::post('generateRecurringTickets', 'PlannerController@generateRecurringTickets');
    Route::post('createPlannerTask', 'PlannerController@createPlannerTask');
    Route::post('updatePlannerTask', 'PlannerController@updatePlannerTask');


    //Teams
    Route::get('/config-teams', 'TeamController@index')->middleware('check_if_admin');
    Route::get('getTeams', 'TeamController@getAll');
    Route::get('getListTeams', 'TeamController@getList'); // retorna name, value para llenar listas
    Route::post('createTeam', 'TeamController@create');
    Route::post('updateTeam', 'TeamController@update');
    Route::post('deleteTeam', 'TeamController@delete');
    Route::post('restoreTeam', 'TeamController@restore');


    //Users
    Route::get('/config-users', 'UserController@index')->middleware('check_if_admin');
    Route::get('getUsers', 'UserController@getAll');
    Route::post('createUser', 'UserController@create');
    Route::post('updateUser', 'UserController@update');
    Route::post('deleteUser', 'UserController@delete');
    Route::get('getListUsers', 'UserController@getList');  // retorna name, value para llenar listas
    Route::post('saveUserSpots', 'UserController@saveSpots');
    Route::get('profile', 'UserController@profile');
    Route::post('saveProfile', 'UserController@saveProfile');
    Route::post('changeDarkMode', 'UserController@changeDarkMode');
    Route::post('resetPassword', 'UserController@resetPassword');
    Route::post('changePassword', 'UserController@changePassword');
    Route::post('changePhoto', 'UserController@changePhoto');
    Route::post('saveShortcut', 'UserController@saveShortcut');
    Route::post('savePreferences', 'UserController@savePreferences');
    Route::post('disableUser', 'UserController@disable');
    Route::post('restoreUser', 'UserController@restore');

    //User Teams
    Route::get('getUserTeams', 'UserTeamController@getUserTeams');


    //Assets
    Route::get('/config-assets', 'AssetController@index')->middleware('check_if_admin');
    Route::get('getAssets', 'AssetController@getAll');
    Route::get('getListAssets', 'AssetController@getList');
    Route::post('createAsset', 'AssetController@create');
    Route::post('updateOrCreateAsset', 'AssetController@updateOrCreate');
    Route::post('updateAsset', 'AssetController@update');
    Route::post('deleteAsset', 'AssetController@delete');
    Route::post('getAssetQR', 'AssetController@getQR');
    Route::post('downloadAssetQR', 'AssetController@downloadQR');
    Route::post('uploadAssetImage', 'AssetController@uploadImage');

    Route::get('getEnabledAssets', 'AssetController@getAllEnabled');   // para la grilla
    Route::get('getListAssets', 'AssetController@getList'); // retorna name, value para llenar listas
    Route::post('getAssetsData', 'AssetController@getAssetsData');
    Route::post('saveAssetQRCode', 'AssetController@saveAssetQRCode');
    Route::get('/viewAsset', 'AssetController@viewAsset');
    Route::post('getAssetInfo', 'AssetController@getAssetInfo');
    Route::post('createAssetTicket', 'AssetController@createTicket');
    Route::get('/dashboard-assets', 'AssetController@dashboard');
    Route::get('getAssetTasks', 'AssetController@getAssetTasks');


    //Checklists
    Route::get('/config-checklists', 'ChecklistController@index')->middleware('check_if_admin');
    Route::get('getChecklists', 'ChecklistController@getAll');   // para la grilla
    Route::get('getListChecklists', 'ChecklistController@getList'); // retorna name, value para llenar listas



    //Metrics
    Route::get('/config-metrics', 'MetricController@index')->middleware('check_if_admin');
    Route::get('getMetrics', 'MetricController@getAll');   // para la grilla
    Route::get('getListMetrics', 'MetricController@getList'); // retorna name, value para llenar listas

    //Checklists Options
    Route::get('/config-checklistoptions', 'ChecklistController@options')->middleware('check_if_admin');
    Route::get('getChecklistOptions', 'ChecklistController@getAllOptions');   // para la grilla
    Route::get('getListChecklistOptions', 'ChecklistController@getListOptions'); // retorna name, value para llenar listas

    //Checklists Data
    Route::get('/config-checklistdata', 'ChecklistController@data')->middleware('check_if_admin');
    Route::get('getChecklistData', 'ChecklistController@getAllData');   // para la grilla
    Route::get('getListChecklistData', 'ChecklistController@getListData'); // retorna name, value para llenar listas
    Route::post('reorderOptions', 'ChecklistController@reorderOptions');

    //Dynamic Fields
    Route::get('/config-dynamicfields', 'DynamicFieldController@index');
    Route::get('getDynamicFields', 'DynamicFieldController@getAll');
    Route::get('getListDynamicFields', 'DynamicFieldController@getList'); // retorna name, value para llenar listas

    //Organization
    Route::get('/config-plansettings', 'OrganizationController@planSettings');

    //Tag
    Route::post('createTagOnFly', 'TagController@createOnFly')->middleware('permission');

    //Role
    Route::get('/config-roles', 'RoleController@index')->middleware('check_if_admin');
    Route::get('getRoles', 'RoleController@getAll');
    Route::post('createRole', 'RoleController@create');
    Route::post('updateRole', 'RoleController@update');
    Route::post('deleteRole', 'RoleController@delete');
    Route::post('restoreRole', 'RoleController@restore');
    Route::get('getAllRoles', 'RoleController@getAll');
    Route::get('getListRole', 'RoleController@getList');
    Route::post('changePermission', 'RoleController@change');

    //Protocol
    Route::get('/config-protocols', 'ProtocolController@index')->middleware('check_if_admin');
    Route::get('getProtocols', 'ProtocolController@getAll');
    Route::get('getListProtocols', 'ProtocolController@getList'); // retorna name, value para llenar listas
    Route::post('saveHtml', 'ProtocolController@saveHtml');
    Route::post('showProtocol', 'ProtocolController@show');

    //Holiday
    Route::get('config-holidays', 'HolidayController@index')->middleware('check_if_admin');
    Route::get('getHolidays', 'HolidayController@getAll');
    Route::post('createHoliday', 'HolidayController@create');
    Route::post('updateHoliday', 'HolidayController@update');
    Route::post('deleteHoliday', 'HolidayController@delete');

    // APPS Route
    Route::get('/config-apps', 'AppController@index');


    //THIS CONTROLLER DOES NOT EXITS??
    // //Schedule 
    // Route::get('/config-schedule', 'ScheduleController@index');
    // Route::get('getSchedules', 'ScheduleController@getAll');
    // Route::post('createSchedule', 'ScheduleController@create');
    // Route::post('updateSchedule', 'ScheduleController@update');
    // Route::post('deleteSchedule', 'ScheduleController@delete');
    // Route::get('getScheduleDetails', 'ScheduleController@getScheduleDetails');
    // Route::post('updateScheduleDetails', 'ScheduleController@updateScheduleDetails');


    //Forms    
    Route::get('/config-forms', 'FormsController@index');
    Route::post('createForm', 'FormsController@create');
    Route::post('updateForm', 'FormsController@update');
    Route::post('deleteForm', 'FormsController@delete');
    Route::post('disableForm', 'FormsController@disable');
    Route::get('/form-editor', 'FormsController@editor');
    Route::get('getForms', 'FormsController@getAll');   // para la grilla
    Route::get('getFormDetails', 'FormsController@getDetails');   // para la grilla
    Route::get('getListForms', 'FormsController@getList'); // retorna name, value para llenar listas
    Route::post('addFormItem', 'FormsController@addFormItem');
    Route::post('deleteFormItem', 'FormsController@deleteFormItem');
    Route::post('updateFormItem', 'FormsController@updateFormItem');
    Route::post('sortFormItems', 'FormsController@sortFormItems');
    Route::get('getFormProperties', 'FormsController@getFormProperties');
    Route::post('getFormPreview', 'FormsController@getFormPreview');
    Route::post('createFormCopy', 'FormsController@createFormCopy');

    // QR
    Route::get('/config-qr', 'TaskQRController@index')->middleware('check_if_admin');
    Route::get('getDataQR', 'TaskQRController@getAll');

    // User Shift
    Route::get('/config-shift', 'ShiftController@index');
    Route::get('getShifts', 'ShiftController@getAll');
    Route::post('createShift', 'ShiftController@create');
    Route::post('updateShift', 'ShiftController@update');
    Route::post('deleteShift', 'ShiftController@delete');
    Route::get('getListShifts', 'ShiftController@getList');


    //Menu
    Route::get('/config-menu', 'MenuController@index');
    Route::get('getAllMenu', 'MenuController@getAll');
    Route::post('enableMenu', 'MenuController@enable');

    //Reminder
    Route::get('/config-reminder', 'AppReminderController@index');
    Route::get('getReminders', 'AppReminderController@getAll');
    Route::post('createReminder', 'AppReminderController@create');
    Route::post('updateReminder', 'AppReminderController@update');
    Route::post('deleteReminder', 'AppReminderController@delete');
    Route::post('changeReminder', 'AppReminderController@change');

    //Ticket Priorities
    Route::get('/config-priorities', 'TicketPriorityController@index');
    Route::get('getTicketPriorities', 'TicketPriorityController@getAll');
    Route::post('updateTicketPriority', 'TicketPriorityController@update');
});

// Reports Namespace
Route::namespace('App\Http\Controllers\Reports')->group(function () {
    Route::get('/reports-general', 'ReportController@general');

    //Report Priority
    Route::get('report-priority', 'ReportController@viewReportPriority')->middleware('check_access_report');
    Route::get('getDataPriority', 'ReportController@getDataPriority');
    Route::get('getDataTicketPriority', 'ReportController@getTicketPriority');
    Route::get('getUserPriority', 'ReportController@getUserPriority');
    Route::post('getEfficiencyPriority', 'ReportController@getEfficiencyPriority');

    //Report Cleaning
    Route::get('report-cleaning-request', 'ReportController@viewReportCleaningRequest')->middleware('check_access_report');
    Route::get('getDataCleaningRequest', 'ReportController@getDataCleaningRequest');
    Route::post('getDataCleaningTicketType', 'ReportController@getDataCleaningTicketType');
    Route::get('getCleaningRequestItems', 'ReportController@getCleaningRequestItems');
    Route::get('getCleaningRequestTicketTypes', 'ReportController@getCleaningRequestTicketTypes');

    //Report Checklist
    Route::get('report-checklist', 'ReportController@viewReportChecklist')->middleware('check_access_report');
    Route::get('getDataChecklist', 'ReportController@getDataChecklist');


    //Report Organization
    Route::get('organization', 'ReportController@viewReportOrganization')->middleware('check_access_report');
    Route::get('getChecklistGroupBySpot', 'ReportController@getChecklistGroupBySpot');
    Route::get('getChecklistBranchReportBySection', 'ReportController@getChecklistBranchReportBySection');
    Route::get('getChecklistBranchReport', 'ReportController@getChecklistBranchReport');

    //Report Cleaning
    Route::get('report-cleaning', 'ReportController@viewReportCleaning')->middleware('check_access_report');
    Route::get('getDataCleaning', 'ReportController@getDataCleaning');

    //Report Team

    Route::get('report-teams', 'ReportController@viewTeamsReport'); // New Teams Report (Matthias)
    Route::get('report-team', 'ReportController@viewReportTeam')->middleware('check_access_report');
    Route::get('getDataTeam', 'ReportController@getDataTeam');
    Route::post('getDataByTeam', 'ReportController@getDataByTeam');

    //Report User

    Route::get('report-users', 'ReportController@viewUsersReport'); // New Teams Report (Matthias)
    Route::get('report-user', 'ReportController@viewReportUser')->middleware('check_access_report');
    Route::get('getDataUserReports', 'ReportController@getDataUserReports');
    Route::get('getDataUserTickets', 'ReportController@getDataUserTickets');
    Route::get('getUserTicketsDetails', 'ReportController@getUserTicketsDetails');

    //Report Task
    Route::get('report-task', 'ReportController@viewReportTask')->middleware('check_access_report');
    Route::get('getDataSpotTickets', 'ReportController@getDataSpotTickets');
    Route::get('getSpotTickets', 'ReportController@getSpotTickets');
    Route::get('getDataItemTickets', 'ReportController@getDataItemTickets');
    Route::get('getItemTickets', 'ReportController@getItemTickets');





    //Report Checklist Audit
    Route::get('report-checklist-audit', 'ReportController@viewReportChecklistAudit')->middleware('check_access_report');
    Route::get('getDataChecklistAudit', 'ReportController@getDataChecklistAudit');
    Route::post('sendEmailAudit', 'ReportController@sendEmailAudit');
    Route::get('getChecklistDetail', 'ReportController@getChecklistDetail');

    //Report Item
    Route::get('report-item', 'ReportController@viewReportItem')->middleware('check_access_report');
    Route::get('getFrequenteItemsReport', 'ReportController@getFrequenteItemsReport');
    Route::get('getDataTaskBySporReport', 'ReportController@getTicketSummaryByMonth');

    //Report Item
    Route::get('report-average-branch', 'ReportController@viewReportAverageBranch')->middleware('check_access_report');
    Route::get('getDataAverageReport', 'ReportController@getDataAverageReport');

    //Report Productivity
    Route::get('report-productivity', 'ReportController@viewReportProductivity')->middleware('check_access_report');
    Route::get('getProductivityByTeam', 'ReportController@getProductivityByTeam');
    Route::get('getProductivityByUser', 'ReportController@getProductivityByUser');
    Route::get('getProductivityGeneral', 'ReportController@getProductivityGeneral');

    //Report Attendance
    Route::get('report-attendance', 'ReportController@viewReportAttendance')->middleware('check_access_report');
    Route::get('getDataAttendance', 'ReportController@getDataAttendance');
    Route::get('report-attendance-log', 'ReportController@viewReportAttendanceLog')->middleware('check_access_report');
    Route::get('getDataAttendanceLog', 'ReportController@getDataAttendanceLog');

    //Report Duration
    Route::get('report-duration', 'ReportController@viewReportDuration')->middleware('check_access_report');
    Route::get('getDataUserDuration', 'ReportController@getDataUserDuration');
    Route::get('getDataSpotDuration', 'ReportController@getDataSpotDuration');

    //Report Overtime
    Route::get('report-overtime', 'ReportController@viewReportOvertime');
    Route::get('getDataAttendanceOvertime', 'ReportController@getDataOvertime');
    Route::get('getOvertimeDetails', 'ReportController@getOvertimeDetails');

    //Report Checklist Invoice
    Route::get('report-checklist-invoice', 'ReportController@viewReportChecklistInvoice');
    Route::get('getDataChecklistInvoice', 'ReportController@getDataChecklistInvoice');

    //Report Checklist Duration
    Route::get('report-checklist-duration', 'ReportController@viewReportChecklistDuration');
    Route::get('getDataChecklistDuration', 'ReportController@getDataChecklistDuration');
    Route::get('getDataChecklistDurationDetail', 'ReportController@getDataChecklistDurationDetail');

    //Report Tasks Summary
    Route::get('report-tasks-summary', 'ReportController@viewReportTasksSummary');
    Route::get('getDataTasksSummary', 'ReportController@getDataTasksSummary');
    Route::get('getDataItemSummary', 'ReportController@getDataItemSummary');
    Route::get('getDataTicketTypeSummary', 'ReportController@getDataTicketTypeSummary');
    Route::get('getDataSpotSummary', 'ReportController@getDataSpotSummary');
    Route::get('getTicketSummaryByMonth', 'ReportController@getTicketSummaryByMonth');
    Route::get('getTicketSummary', 'ReportController@getTicketSummary');


    //Report Teams Summary
    Route::get('report-teams-summary', 'ReportController@viewReportTeamsSummary');
    Route::get('getTeamSummary', 'ReportController@getTeamSummary');
    Route::get('getTeamSummaryByStatus', 'ReportController@getTeamSummaryByStatus');
    Route::get('getDataTeamsSummary', 'ReportController@getDataTeamsSummary');
    Route::get('getDataTeamUserSummary', 'ReportController@getDataTeamUserSummary');

    //Report Checklist Review
    Route::get('report-checklist-review', 'ReportController@viewReportChecklistReview');
    Route::get('getDataChecklistReview', 'ReportController@getDataChecklistReview');
    Route::get('getDataChecklistReviewBySection', 'ReportController@getDataChecklistReviewBySection');
    Route::get('getDataChecklistReviewByOption', 'ReportController@getDataChecklistReviewByOption');

    //Report Checklist Review 2
    Route::get('report-checklist-review2', 'ReportController@viewReportChecklistReview2');
    Route::get('getDataChecklistReview2', 'ReportController@getDataChecklistReview2');
    Route::get('getDataChecklistReviewByOption2', 'ReportController@getDataChecklistReviewByOption2');
    Route::get('getDataChecklistReviewBySpot2', 'ReportController@getDataChecklistReviewBySpot2');

    //Report Checklist Summary
    Route::get('report-checklist-summary', 'ReportController@viewReportChecklistSummary');
    Route::get('getDataChecklistSummary', 'ReportController@getDataChecklistSummary');


    //Report Spots Summary
    Route::get('report-spots-summary', 'ReportController@viewReportSpotsSummary');


    //Report Management Branch
    Route::get('report-management-branch', 'ReportController@viewReportManagementBranch');
    Route::get('getDataChecklistManagement', 'ReportController@getDataChecklistManagement');
    Route::get('getDataChecklistManagementGroup', 'ReportController@getDataChecklistManagementGroup');
    Route::get('getDataChecklistManagementOption', 'ReportController@getDataChecklistManagementOption');


    //Report Due Tasks
    Route::get('report-due-tasks', 'ReportController@viewReportDueTasks');
    Route::get('getDueTasksBySpot', 'ReportController@getDataDueTasksBySpot');
    Route::get('getDueTasksByTeam', 'ReportController@getDataDueTasksByTeam');
    Route::get('getDueTasksByItem', 'ReportController@getDataDueTasksByItem');
    Route::get('getDueTasks', 'ReportController@getDataDueTasks');

    //Report Location
    Route::get('report-location', 'ReportController@viewReportLocation');
    Route::get('getDataLocation', 'ReportController@getDataLocation');

    //Report Branch
    Route::get('report-branch', 'ReportController@viewReportBranch');
    Route::get('getDataBranchBySpot', 'ReportController@getDataBranchBySpot');
    Route::get('getDataBranchBySection', 'ReportController@getDataBranchBySection');
    Route::get('getDataBranch', 'ReportController@getDataBranch');

    //Report Checklist Note
    Route::get('report-checklist-note', 'ReportController@viewReportChecklistNote');
    Route::get('getDataChecklistNote', 'ReportController@getDataChecklistNote');
    Route::post('generatePdfChecklistNote', 'ReportController@generatePdfChecklistNote');

    //Report Clockin
    Route::get('report-clockin', 'ReportController@viewReportClockin');
    Route::get('getDataClockin', 'ReportController@getDataClockin');
    Route::get('getClockinDetails', 'ReportController@getClockinDetails');

    //Report Clockin Map
    Route::get('report-clockin-map', 'ReportController@viewReportclockinMap');
    Route::get('getClockinDataByUser', 'ReportController@getClockinDataByUser');
    Route::get('getClockinData', 'ReportController@getClockinData');
    Route::post('getLastClockinChange', 'ReportController@getLastClockinChange');
    Route::get('exportClockinMap', 'ReportController@exportClockinMap');

    //Report Clockin Device
    Route::get('report-clockin-device', 'ReportController@viewReportClockinDevice');
    Route::get('getDataClockinDevice', 'ReportController@getDataClockinDevice');

    //Report Clockin Time
    Route::get('report-clockin-time', 'ReportController@viewReportClockinTime');
    Route::get('getDataClockinTime', 'ReportController@getDataClockinTime');
    Route::get('getClockinTimeDetails', 'ReportController@getClockinTimeDetails');

    //Report General
    Route::get('report-general', 'ReportController@viewReportGeneral');
    Route::post('getActivityByDates', 'ReportController@getActivityByDates');
    Route::post('getDataActivityBySpot', 'ReportController@getDataActivityBySpot');
    Route::post('getDataActivityByItem', 'ReportController@getDataActivityByItem');
    Route::post('getDataEfficacy', 'ReportController@getDataEfficacy');

    //Report Checklist Review
    Route::get('report-checklist-review3', 'ReportController@viewReportChecklistReview3');
    Route::get('getDataChecklistReview3', 'ReportController@getDataChecklistReview3');
    Route::get('getDataChecklistReview3Detail', 'ReportController@getDataChecklistReview3Detail');
    Route::get('getDataChecklistReview3Notes', 'ReportController@getDataChecklistReview3Notes');
    // Route::get('getDataChecklistReviewByOption', 'ReportController@getDataChecklistReviewByOption');
});


// Production Namespace
Route::namespace('App\Http\Controllers\Production')->group(function () {
    // Equipment
    Route::get('/config-machines', 'EquipmentController@index');
    Route::get('getEquipments', 'EquipmentController@getAll'); // para la grilla
    Route::get('getListEquipments', 'EquipmentController@getList'); // retorna name, value para llenar listas

    // Products
    Route::get('/config-products', 'ProductController@index');
    Route::get('getProducts', 'ProductController@getAll'); // para la grilla
    Route::get('getListProducts', 'ProductController@getList'); // retorna name, value para llenar listas

    // Production
    Route::get('/config-production', 'ProductionController@index');
    Route::get('getProductions', 'ProductionController@getProductions');   // para la grilla
    Route::get('getCurrentProduction', 'ProductionController@getCurrentProduction');   // parael Dashboard      
    Route::get('/dashboard-production', 'ProductionController@dashboard');
    Route::post('getLastProduction', 'ProductionController@getLast');
    Route::post('initializeProduction', 'ProductionController@initializeProduction');

    // Se pasaron acá porque ocupamos enviar la sesión con el timezone...

    Route::post('createProduction', 'ProductionController@create');
    Route::post('updateProduction', 'ProductionController@update');

    // Production Detail    
    Route::get('getProductionDetails', 'ProductionController@getProductionDetails');   // para la grilla    


    // Production Stops
    Route::get('/config-productionstops', 'ProductionStopController@index');
    Route::get('getProductionStops', 'ProductionStopController@getAll'); // para la grilla
    Route::get('getListProductionStops', 'ProductionStopController@getList');


    // Production Inputs
    Route::get('/config-productioninputs', 'ProductionInputController@index');
    Route::get('getProductionInputs', 'ProductionInputController@getAll'); // para la grilla
    Route::get('getListProductionInputs', 'ProductionInputController@getList');

    // Production Formulas
    Route::get('/config-productionformulas', 'ProductionFormulaController@index');
    Route::get('getProductionFormulas', 'ProductionFormulaController@getAll'); // para la grilla
    Route::get('getListProductionFormulas', 'ProductionFormulaController@getList');

    // Production Formulas
    Route::get('/config-presentations', 'PresentationController@index');
    Route::get('getPresentations', 'PresentationController@getAll'); // para la grilla
    Route::get('getListPresentations', 'PresentationController@getList');


    // Production Log
    Route::get('getProductionLog', 'ProductionLogController@getProductionLog');

    // Production Schedule
    Route::get('/config-productionschedules', 'ProductionScheduleController@index');
    Route::get('getProductionSchedules', 'ProductionScheduleController@getAll'); // para la grilla
    Route::get('getListSchedules', 'ProductionScheduleController@getList'); // retorna name, value para llenar listas


    // Production Breaks
    Route::get('/config-productionbreaks', 'ProductionBreakController@index');
    Route::get('getProductionBreaks', 'ProductionBreakController@getAll'); // para la grilla   

});


//Warehouse
Route::namespace('App\Http\Controllers\Warehouse')->group(function () {
    //Warehouse
    Route::get('warehouse', 'WarehouseController@index');
    Route::post('updateWarehouse', 'WarehouseController@update');
    Route::post('deleteWarehouse', 'WarehouseController@delete');
    Route::get('getWarehouses', 'WarehouseController@getWarehouses'); // para la grilla
    Route::get('getListWarehouse', 'WarehouseController@getList');
    Route::post('getLastWarehouse', 'WarehouseController@getLast');
    Route::get('nextWarehouseStatus', 'WarehouseController@nextStatus');
    Route::post('changeStatusWarehouse', 'WarehouseController@changeStatus');

    //Warehouse Item
    Route::get('config-warehouse-items', 'WarehouseItemController@index');
    Route::get('getWarehouseItems', 'WarehouseItemController@getWarehouseItems'); // para la grilla
    Route::get('getListWarehouseItem', 'WarehouseItemController@getList');
    Route::get('getValueMapper', 'WarehouseItemController@getValueMapper');
    Route::get('getAllWarehouseItems', 'WarehouseItemController@getAllWarehouseItems');

    //Warehouse Category
    Route::get('config-warehouse-categories', 'WarehouseCategoryController@index');
    Route::get('getWarehouseCategories', 'WarehouseCategoryController@getAll');
    Route::post('createWarehouseCategory', 'WarehouseCategoryController@create');
    Route::post('updateWarehouseCategory', 'WarehouseCategoryController@update');
    Route::post('deleteWarehouseCategory', 'WarehouseCategoryController@delete');

    //reporte
    Route::get('/warehouse-report', 'WarehouseReportController@index');
    Route::get('getWarehouseReport', 'WarehouseReportController@getWarehouseReport'); // para la grilla
    Route::post('getGeneralAverage', 'WarehouseReportController@getGeneralAverage');
    Route::get('getListWarehouseItem', 'WarehouseReportController@getList');
    Route::post('createWarehouse', 'WarehouseController@create');

    //Warehouse Notes
    Route::get('getWarehouseNotes', 'WarehouseNoteController@getNotes');
    Route::post('createNoteWarehouse', 'WarehouseNoteController@create');
});


Route::namespace('App\Http\Controllers\Project')->group(function () {

    Route::get('/gantt', 'TaskController@gantt');
    Route::get('/ganttData', 'TaskController@getAll');


    Route::get('/projects', 'ProjectController@dashboard');
    Route::get('getProjectTasks', 'ProjectController@getProjectTasks');
    Route::get('getListProjects', 'ProjectController@getList'); // retorna name, value para llenar listas

    Route::get('/dashboard-gantt', 'ProjectController@dashboardGantt');
    Route::get('getGanttData', 'ProjectController@getGanttData');
    Route::post('createActivity', 'ProjectController@createActivity');
    Route::post('updateActivity', 'ProjectController@updateActivity');
    Route::post('deleteActivity', 'ProjectController@deleteActivity');


    // Project table
    Route::get('getAllProjects', 'ProjectController@getAll');
});



// Cleaning Namespace
Route::namespace('App\Http\Controllers\Cleaning')->group(function () {

    // CleaningSchedule
    Route::get('/config-cleaningschedule', 'CleaningController@cleaningSchedule');
    Route::get('getCleaningSchedules', 'CleaningController@getCleaningSchedules'); // para la grilla
    Route::get('getListCleaningSchedules', 'CleaningController@getList'); // retorna name, value para llenar listas
    Route::get('/dashboard-cleaning', 'CleaningController@dashboard');

    // Cleaning Plan
    Route::get('getCleaningPlan', 'CleaningController@getCleaningPlan');
    Route::post('generateCleaningPlan', 'CleaningController@generateCleaningPlan');
    Route::post('updateCleaningPlanForSpot', 'CleaningController@updateCleaningPlanForSpot');

    Route::get('getSpotCleaningPlan', 'CleaningController@getSpotCleaningPlan');

    Route::post('createCleaningSchedule', 'CleaningController@createCleaningSchedule');
    Route::post('updateCleaningSchedule', 'CleaningController@updateCleaningSchedule');
    Route::post('saveCleaningPlanSequence', 'CleaningController@saveCleaningPlanSequence');

    //Cleaning-assign
    Route::get('cleaning-assign', 'CleaningController@cleaningAssign');
    Route::get('getAvailableSpots', 'CleaningController@getAvailableSpots');
    Route::get('getCleaningStaff', 'CleaningController@getCleaningStaff');
    Route::get('getCleaningStaffWithPlans', 'CleaningController@getCleaningStaffWithPlans');
    Route::post('assignCleaning', 'CleaningController@assignCleaning');
    Route::post('findCleaningPlan', 'CleaningController@findCleaningPlan');
    Route::post('editCleaningPlan', 'CleaningController@editCleaningPlan');
    Route::post('moveCleaningPlan', 'CleaningController@moveCleaningPlan');
    Route::post('deleteCleaningPlan', 'CleaningController@deleteCleaningPlan');
    Route::get('getCleaningSpots', 'CleaningController@getCleaningSpots');
    Route::get('getCleaningItems', 'CleaningController@getCleaningItems');
    //Cleaning-assign

    //Cleaning-log
    Route::get('getAllCleaningLog', 'CleaningController@getAllCleaningLog');
    //Cleaning-log

    //Cleaning-dashboard
    Route::get('dashboard-cleaning', 'CleaningDashboardController@index');
    Route::get('getCleaningSpots', 'CleaningDashboardController@getCleaningSpots');
    Route::post('changeCleaningStatus', 'CleaningDashboardController@changeCleaningStatus');
    Route::post('createCleaningPlan', 'CleaningDashboardController@createCleaningPlan');
    Route::post('deleteCleaningPlan', 'CleaningDashboardController@deleteCleaningPlan');
    Route::get('getCleaningPlans', 'CleaningDashboardController@getCleaningPlans');
    Route::get('getCleaningNotes', 'CleaningDashboardController@getCleaningNotes');
    Route::get('getCleaningChecklist', 'CleaningDashboardController@getCleaningChecklist');
    Route::post('getLastCleaningChange', 'CleaningDashboardController@getLastCleaningChange');
    Route::post('initializeCleaningDashboard', 'CleaningDashboardController@initializeCleaningDashboard');
    //Cleaning-dashboard
});


//Tickers Namespace
Route::namespace('App\Http\Controllers\Tickets')->group(function () {


    Route::get('/createtask', 'TicketController@createtask');
    Route::get('/', 'TicketController@index');

    //Route Ticket
    Route::get('/dashboard-tasks', 'TicketController@index');
    Route::get('getAllTicket', 'TicketController@getAll');
    Route::get('getStatsTicket', 'TicketController@getStats');
    Route::get('getMyStatsTicket', 'TicketController@getMyStats');
    Route::post('getStatusTicket', 'TicketController@getStatus');
    Route::post('changeStatusTicket', 'TicketController@changeStatus')->middleware('permission');
    Route::post('createTicket', 'TicketController@create')->middleware('permission');
    Route::post('updateTicket', 'TicketController@update')->middleware('permission');
    Route::post('deleteTicket', 'TicketController@delete')->middleware('permission');
    Route::post('verifyTicket', 'TicketController@verify')->middleware('permission');
    Route::post('getTicket', 'TicketController@get');
    Route::post('uploadTicketFile', 'TicketController@uploadFile');
    Route::post('getLastTicket', 'TicketController@getLast');
    Route::get('getEssentialProductTickets', 'TicketController@getEssentialProductTickets');
    Route::post('setTicketDuration', 'TicketController@setDuration')->middleware('permission');
    Route::post('escalateTicket', 'TicketController@escalate')->middleware('permission');
    Route::get('exportTickets', 'TicketController@export');
    Route::get('task-resume', 'TicketController@resumeView');

    //Route Ticket note
    Route::post('getNotes', 'TicketNoteController@getNotes');
    Route::post('createNote', 'TicketNoteController@create');
    Route::post('deleteNote', 'TicketNoteController@delete');

    //Route Ticket checklist
    Route::post('getChecklist', 'TicketChecklistController@get');
    Route::post('saveChecklist', 'TicketChecklistController@save')->middleware('permission');
    Route::post('generatePdfChecklist', 'TicketChecklistController@generatePdf');
    Route::get('viewPdf', 'TicketChecklistController@viewPdf');
    Route::post('assignEvaluator', 'TicketChecklistController@assignEvaluator')->middleware('permission');
});


//Workplan namespace 
Route::namespace('App\Http\Controllers\Workplan')->group(function () {
    //Route Work Plan
    Route::get('config-work-plans', 'WorkPlanController@index');
    Route::get('work-plan', 'WorkPlanController@viewWorkPlan');
    Route::post('createWorkPlan', 'WorkPlanController@create');
    Route::post('updateWorkPlan', 'WorkPlanController@update');
    Route::post('deleteWorkPlan', 'WorkPlanController@delete');
    Route::post('restoreWorkPlan', 'WorkPlanController@restore');
    Route::get('getAllWorkPlans', 'WorkPlanController@getAll');
    Route::get('getDataWorkPlan', 'WorkPlanController@getData');
    Route::get('getWorkPlan', 'WorkPlanController@getWorkPlan');
    Route::post('getPlannerList', 'WorkPlanController@getPlannerList');
    Route::get('exportWorkPlanToExcel', 'WorkPlanController@exportToExcel');
    Route::post('copyWorkPlan', 'WorkPlanController@copyWorkPlan');
});

//AssetLoan namespace 
Route::namespace('App\Http\Controllers\AssetLoan')->group(function () {
    //Routes Dashboard Asset Loan
    Route::get('dashboard-asset-loan', 'AssetLoanController@index');
    Route::get('getDataAssetLoan', 'AssetLoanController@getData');
    Route::post('createAssetLoan', 'AssetLoanController@create');
    Route::post('updateAssetLoan', 'AssetLoanController@update');
    Route::post('deleteAssetLoan', 'AssetLoanController@delete');
    Route::post('changeAssetLoanStatus', 'AssetLoanController@changeStatus');
    Route::post('getAssetLoanDetail', 'AssetLoanController@getDetail');
    Route::post('getLastAssetLoanChange', 'AssetLoanController@getLastChange');


    //Routes Asset Loan Notes
    Route::get('getAssetLoanNotes', 'AssetLoanNoteController@getNotes');
    Route::post('createAssetLoanNote', 'AssetLoanNoteController@create');
    Route::post('deleteAssetLoanNote', 'AssetLoanNoteController@delete');
});

//User namespace 
Route::namespace('App\Http\Controllers\User')->group(function () {

    //??cant find these controllers
    //User Attendance
    // Route::get('attendance', 'UserAttendanceController@index');
    // Route::get('getAllAttendances', 'UserAttendanceController@getAll');
    // Route::get('getAllAttendancesByUser', 'UserAttendanceController@getAllByUser');
    // Route::post('getLastAttendance', 'UserAttendanceController@getLast');
    // Route::get('getDataOvertime', 'AttendanceLogController@getDataOvertime');
    // Route::post('approveOvertime', 'AttendanceLogController@approveOvertime')->middleware('permission');
    // Route::get('getOvertimeDetails', 'AttendanceLogController@getOvertimeDetails');

    //Route User Schedule
    Route::get('exportUserSchedule', 'UserScheduleController@export');

    // User Schedule
    Route::get('/config-user-schedule', 'UserScheduleController@viewUserSchedule');
    Route::get('getUserSchedule', 'UserScheduleController@getUserSchedule');
    Route::post('updateUserSchedule', 'UserScheduleController@updateUserSchedule');

    //ROUTES RESOURCE DEVICE
    Route::post('saveToken', 'UserDeviceController@save');
});










// Route Dashboards
Route::get('/dashboard-analytics', 'App\Http\Controllers\DashboardController@dashboardAnalytics');
Route::get('/dashboard-ecommerce', 'App\Http\Controllers\DashboardController@dashboardEcommerce');


//Route log
Route::get('getAllLog', 'App\Http\Controllers\LogController@getAll');

// locale Route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);


//ROUTES RESOURCE DEVICE


//Route setting update
Route::get('getSettingUpdate', 'App\Http\Controllers\SettingUpdateController@get');

Route::get('ios', function () {
    return view("installers.ios");
});

Route::get('android', function () {
    return view("installers.android");
});

//Route Filter
Route::post('createFilter', 'App\Http\Controllers\FilterController@create');
Route::post('deleteFilter', 'App\Http\Controllers\FilterController@delete');
Route::post('updateFilter', 'App\Http\Controllers\FilterController@update');






//Pagadito
Route::get('pagadito', 'App\Http\Controllers\PagaditoController@index');
Route::post('pagaditoPayment', 'App\Http\Controllers\PagaditoController@payment');
Route::get('pagaditoSuccess', 'App\Http\Controllers\PagaditoController@successPayment');



//Route Notification
Route::post('getNotifications', 'App\Http\Controllers\NotificationController@getNotifications');
Route::post('readNotifications', 'App\Http\Controllers\NotificationController@readNotifications');

//Route Access Directory
Route::get('login365BE', 'App\Http\Controllers\AzureAuthController@login365BE');
Route::get('login365APP', 'App\Http\Controllers\AzureAuthController@login365APP');
Route::get('azureAuthSuccess', 'App\Http\Controllers\AzureAuthController@viewSuccess');





// PowerBI APIs
Route::get('getDataToPowerBI', 'App\Http\Controllers\Tickets\TicketController@getDataToPowerBI');



Route::post('registerClockinAPP', 'App\Http\Controllers\ClockinLogController@registerClockinAPP');
Route::post('registerClockoutAPP', 'App\Http\Controllers\ClockinLogController@registerClockoutAPP');
Route::post('approveClockinTime', 'App\Http\Controllers\ClockinLogController@approveClockinTime');




//Routes Coupon Sheet
Route::get('coupon-sheet', 'App\Http\Controllers\CouponSheetController@index');
Route::get('getDataCouponSheet', 'App\Http\Controllers\CouponSheetController@getData');
Route::post('createSheet', 'App\Http\Controllers\CouponSheetController@create');
Route::post('deleteSheet', 'App\Http\Controllers\CouponSheetController@delete');
Route::post('updateSheet', 'App\Http\Controllers\CouponSheetController@update');
Route::post('closeSheet', 'App\Http\Controllers\CouponSheetController@close');
Route::post('scanCoupon', 'App\Http\Controllers\CouponSheetDetailController@scanCoupon');
Route::post('sendExcelFiles', 'App\Http\Controllers\CouponSheetController@sendFiles');
Route::post('getSheetDetail', 'App\Http\Controllers\CouponSheetController@getDetail');
Route::post('markCouponToReady', 'App\Http\Controllers\CouponSheetDetailController@markCouponToReady');
Route::get('getCouponDeficit', 'App\Http\Controllers\CouponSheetDetailController@getCouponDeficit');
Route::post('getNextCouponSheet', 'App\Http\Controllers\CouponSheetController@getNext');
Route::get('getDataScannedCoupons', 'App\Http\Controllers\CouponSheetController@getDataScannedCoupons');
Route::get('exportCouponsToExcel', 'App\Http\Controllers\CouponSheetController@exportCouponsToExcel');
