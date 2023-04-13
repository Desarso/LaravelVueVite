<?php

//ROUTES LOGIN
Route::post('loginApp', 'App\Http\Controllers\Auth\LoginController@loginApp');
Route::post('loginByIduserApp', 'App\Http\Controllers\Auth\LoginController@loginByIduserApp');
Route::post('logoutApp', 'App\Http\Controllers\Auth\LoginController@logoutApp');
//ROUTES LOGIN

Route::namespace ('App\Http\Controllers\Tickets')->group(function () {
    //ROUTES TICKET
    Route::post('getAllTicketsApp', 'TicketController@getAllTicketsApp');

    Route::post('getNewTicketsApp', 'TicketController@getNewTicketsApp');

    Route::post('syncToServer', 'TicketController@syncToServer');

    Route::post('deleteTaskApp', 'TicketController@deleteTaskApp');

    Route::post('approvedTaskApp', 'TicketController@approvedTaskApp');

    Route::post('updateTaskApp', 'TicketController@updateTaskApp');

    Route::post('signTaskAPP', 'TicketController@signTaskAPP');

    Route::post('createTaskAPP', 'TicketController@createTaskAPP');

    Route::post('getTaskByIdspot', 'TicketController@getTaskByIdspot');

    Route::post('getTaskCleaningProduct', 'TicketController@getTaskCleaningProduct');

    Route::post('changeStatusApp', 'TicketController@changeStatusApp');

    Route::post('escalateTaskApp', 'TicketController@escalateTaskApp');

    Route::post('searchTicketAPP', 'TicketController@searchTicket');

    Route::post('findTicketsApp', 'TicketController@findTicketsApp');

    Route::post('checkTaskExistsAPP', 'TicketController@checkTaskExistsAPP');

    Route::post('assignTaskApp', 'TicketController@assignTaskApp');

    Route::get('showMsFilesApp', 'TicketController@showMsFilesApp');
    //ROUTES TICKET

    //ROUTES CHECKLIST
    Route::post('getChecklistByIdtaskApp', 'TicketChecklistController@getChecklistApp');

    Route::post('addChecklistEvaluatorApp', 'TicketChecklistController@addChecklistEvaluatorApp');

    Route::post('getEvalutionUserChecklistAPP', 'TicketChecklistController@getEvalutionUserChecklistAPP');

    Route::post('synctTaskChecklistAPP', 'TicketChecklistController@synctTaskChecklistAPP');

    Route::post('sendPdfEmailAPP', 'TicketChecklistController@sendPdfEmailAPP');
    //ROUTES CHECKLIST

    //ROUTES NOTE
    Route::post('getNotesByIdtaskApp', 'TicketNoteController@getNotesApp');

    Route::post('deleteNoteApp', 'TicketNoteController@deleteNoteApp');
    //ROUTES NOTE

    //ROUTES Priority
    Route::post('getAllPriorityApp', 'TicketPriorityController@getListApp');

    //ROUTES Dashboard
    Route::post('getMyStats', 'TicketController@getMyStats');
});

Route::post('getTaskLogsApp', 'App\Http\Controllers\LogController@getTaskLogsApp');

//ROUTES Priority

//ROUTES SettingUpdate
Route::post('getSettingUpdateApp', 'App\Http\Controllers\SettingUpdateController@getListApp');

Route::post('getSettingUpdate', 'App\Http\Controllers\SettingUpdateController@get');
//ROUTES SettingUpdate

//ROUTES Dashboard

//ROUTES Evaluation
Route::post('createEvaluteUserAPP', 'App\Http\Controllers\EvaluationUserController@createEvaluteUserAPP');

Route::post('getEvaluationbyUser', 'App\Http\Controllers\EvaluationUserController@getEvaluationbyUser');

Route::post('getEvaluationGroupbyUser', 'App\Http\Controllers\EvaluationUserController@getEvaluationGroupbyUser');

Route::post('getUsersEvaluationAPP', 'App\Http\Controllers\EvaluationUserController@getUsersEvaluationAPP');
//ROUTES Evaluation

// Notification
Route::post('getListNotificationAPP', 'App\Http\Controllers\NotificationController@getListNotificationAPP');

Route::post('getNotificationNotRead', 'App\Http\Controllers\NotificationController@getNotificationNotRead');

Route::post('setNotificationsRead', 'App\Http\Controllers\NotificationController@setNotificationsRead');
// Notification

Route::namespace ('App\Http\Controllers\Config')->group(function () {

    //ROUTES ITEM
    Route::post('getAllItemApp', 'ItemController@getListApp');
    //ROUTES ITEM

    //ROUTES TEAMS
    Route::post('getAllTeamsApp', 'TeamController@getListApp');
    //ROUTES TEAMS

    //ROUTES USER
    Route::post('getAllUserApp', 'UserController@getListApp');

    Route::post('updateAvatarApp', 'UserController@updateAvatarApp');

    Route::post('updateUserApp', 'UserController@updateUserApp');

    Route::post('checkForceloginAPP', 'UserController@checkForceloginAPP');

    Route::post('changeAvailableApp', 'UserController@changeAvailableApp');
    //ROUTES USER

    //ROUTES SPOT
    Route::post('getListSpotApp', 'SpotController@getListApp');

    Route::post('searchSpotBranchAPP', 'SpotController@searchSpotBranchAPP');
    //ROUTES SPOT

    //ROUTES CHECKLIST
    Route::post('getAllChecklist', 'ChecklistController@getListApp');

    Route::post('getAllChecklistData', 'ChecklistController@getChecklistDataApp');
    //ROUTES CHECKLIST

    //ROUTES Protocol
    Route::get('showTaskProtocolApp', 'ProtocolController@show');

    Route::get('showProtocolApp', 'ProtocolController@showProtocolApp');

    Route::post('getAllProtocolApp', 'ProtocolController@getListApp');
    //ROUTES Protocol

    //ROUTES TAG
    Route::post('getAllTagAPP', 'TagController@getAllTagAPP');
    //ROUTES TAG

    //ROUTES STATUS
    Route::post('getAllTicketStatusAPP', 'TicketStatusController@getAllTicketStatusAPP');
    //ROUTES STATUS

    // Clening Catalogs
    Route::post('getCleningSpotsAPP', 'SpotController@getCleningSpotsAPP');

    Route::post('getCleaningTypesAPP', 'ItemController@getCleaningTypesAPP');

    Route::post('getCleaningProductsAPP', 'ItemController@getCleaningProductsAPP');

    Route::post('searchCleaningProductsAPP', 'ItemController@searchCleaningProductsAPP');

    Route::post('getCleaningUsersAPP', 'UserController@getCleaningUsersAPP');
    // Clening Catalogs

    //ROUTES APPS
    Route::post('getAllApps', 'AppController@getAllApps');
    //ROUTES APPS
});

//ROUTES Cleaning
Route::namespace ('App\Http\Controllers\Cleaning')->group(function () {

    Route::post('getRoomsAPP', 'CleaningController@getRoomsAPP');

    Route::post('getRoomByIdAPP', 'CleaningController@getRoomByIdAPP');

    Route::post('searchRoomsAPP', 'CleaningController@searchRoomsAPP');

    Route::post('getCleaningStatusAPP', 'CleaningController@getCleaningStatusAPP');

    Route::post('chageCleaningPlanStatusAPP', 'CleaningController@chageCleaningPlanStatusAPP');

    Route::post('chageRoomStatusAPP', 'CleaningController@chageRoomStatusAPP');

    Route::post('getMyCleaningPlanAPP', 'CleaningController@getMyCleaningPlanAPP');

    Route::post('getCleaningNotesAPP', 'CleaningController@getCleaningNotesAPP');

    Route::post('CreateCleaningNotesAPP', 'CleaningController@CreateCleaningNotesAPP');

    Route::post('getCleaningChecklistAPP', 'CleaningController@getCleaningChecklistAPP');

    Route::post('syncCleaningChecklistAPP', 'CleaningController@syncCleaningChecklistAPP');

    Route::post('createCleaningPlanAPP', 'CleaningController@createCleaningPlanAPP');

    Route::post('createPlanFromSliderPlanAPP', 'CleaningController@createPlanFromSliderPlanAPP');

    Route::post('getCleaningPlanAPP', 'CleaningController@getCleaningPlanAPP');

    Route::post('getCleaningPlanBySpotAPP', 'CleaningController@getCleaningPlanBySpotAPP');

    Route::post('deleteCleaningPlanAPP', 'CleaningController@deleteCleaningPlanAPP');

    Route::post('assingCleaningPlanAPP', 'CleaningController@assingCleaningPlanAPP');
});
//ROUTES Cleaning

//ROUTES Warehouse
Route::namespace ('App\Http\Controllers\Warehouse')->group(function () {
    Route::post('getWarehouseRequestAPP', 'WarehouseController@getWarehouseRequestAPP')->name('createWarehouse');
    Route::post('saveWarehouseRequestAPP', 'WarehouseController@saveWarehouseRequestAPP');

    //Warehouse catalogs
    Route::post('searchItemsWarehouseAPP', 'WarehouseItemController@searchItemsWarehouseAPP');
});
//ROUTES Warehouse

Route::namespace ('App\Http\Controllers\Reports')->group(function () {
    //Report Overtime
    Route::post('getAttendanceOvertimeAPP', 'ReportController@getDataOvertimeAPP');
    Route::post('getOvertimeDetailsAPP', 'ReportController@getOvertimeDetails');
});

// Route ClockIN
Route::post('getClockinActivyList', 'App\Http\Controllers\ClockinActivityController@getListApp');
Route::post('getUserClockinLogAPP', 'App\Http\Controllers\ClockinLogController@getUserClockinLogAPP');
Route::post('registerClockinAPP', 'App\Http\Controllers\ClockinLogController@registerClockinAPP');
Route::post('registerClockoutAPP', 'App\Http\Controllers\ClockinLogController@registerClockoutAPP');
Route::post('getUserClockinSummaryAPP', 'App\Http\Controllers\ClockinLogController@getUserClockinSummaryAPP');
Route::post('getUserClockLogHistoryAPP', 'App\Http\Controllers\ClockinLogController@getUserClockLogHistoryAPP');
Route::post('verifyClockInCodeAPP', 'App\Http\Controllers\ClockinLogController@verifyClockInCodeAPP');
Route::post('pauseClockinAPP', 'App\Http\Controllers\ClockinLogController@pauseClockinAPP');

// Route Tasks Favorite
Route::post('saveFavoriteAPP', 'App\Http\Controllers\TaskFavoriteController@saveFavorite');
Route::post('deleteFavoriteAPP', 'App\Http\Controllers\TaskFavoriteController@deleteFavoriteAPP');
Route::post('getFavoritesByIduserAPP', 'App\Http\Controllers\TaskFavoriteController@getFavoritesByIduser');

Route::namespace ('App\Http\Controllers\Workplan')->group(function () {
    // Route Work Plan
    Route::post('getWorkPlanByUser', 'WorkPlanController@getWorkPlanAPP');
    Route::post('getPlannerToEvaluateAPP', 'WorkPlanController@getPlannerToEvaluateAPP');
    Route::post('checkPendingPlansApp', 'WorkPlanController@checkPendingPlansApp');
});

//ROUTES Assets
Route::namespace ('App\Http\Controllers\Config')->group(function () {

    Route::post('getListAssetsAPP', 'AssetController@getListAPP');

    Route::post('getAssetInfoAPP', 'AssetController@getAssetInfoAPP');

    Route::post('searchAssetsAPP', 'AssetController@searchAssetsAPP');

    Route::post('getTicketAssetAPP', 'AssetController@getTicketAssetAPP');
});

Route::namespace ('App\Http\Controllers\AssetLoan')->group(function () {
    Route::post('getListAssetsLoanAPP', 'AssetLoanController@getListLoanAPP');
    Route::post('createAssetLoanAPP', 'AssetLoanController@createAssetLoanAPP');
    Route::post('getAssetLoanDetailsAPP', 'AssetLoanController@getAssetLoanDetailsAPP');
    Route::post('closeAssetLoanAPP', 'AssetLoanController@closeAssetLoanAPP');
    Route::post('getAssetLoanNotesAPP', 'AssetLoanController@getAssetLoanNotesAPP');
    Route::post('createAssetLoanNotesAPP', 'AssetLoanController@createAssetLoanNotesAPP');
    Route::post('deleteAssetLoanNotesAPP', 'AssetLoanController@deleteAssetLoanNotesAPP');
    Route::post('deleteAssetLoanAPP', 'AssetLoanController@deleteAssetLoanAPP');
});
