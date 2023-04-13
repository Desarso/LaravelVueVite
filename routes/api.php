<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

require_once "app.php";
require_once "chat.php";

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('register', 'App\Http\Controllers\AuthController@register');

    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::get('logout', 'App\Http\Controllers\AuthController@logout');
        Route::get('user', 'App\Http\Controllers\AuthController@user');
    });
});

///////////////////////////////////////////////////////////////////////////////////////////////

Route::namespace('App\Http\Controllers\Config')->group(function () {

    // Ticket Types
    Route::post('createTicketType', 'TicketTypeController@create');
    Route::post('updateTicketType', 'TicketTypeController@update');
    Route::delete('deleteTicketType', 'TicketTypeController@delete');

    // Items
    Route::post('createItem', 'ItemController@create');
    Route::post('updateItem', 'ItemController@update');
    Route::delete('deleteItem', 'ItemController@delete');

    // SpotType
    Route::post('createSpotType', 'SpotTypeController@create');
    Route::post('updateSpotType', 'SpotTypeController@update');
    Route::delete('deleteSpotType', 'SpotTypeController@delete');

    // Spots
    Route::post('createSpot', 'SpotController@create');
    Route::post('updateSpot', 'SpotController@update');
    Route::delete('deleteSpot', 'SpotController@delete');




    // Planner
    Route::post('createPlanner', 'PlannerController@create');
    Route::post('updatePlanner', 'PlannerController@update');
    Route::delete('deletePlanner', 'PlannerController@delete');

    // Teams
    Route::post('createTeam', 'TeamController@create');
    Route::post('updateTeam', 'TeamController@update');
    Route::delete('deleteTeam', 'TeamController@delete');

    // Users
    Route::post('createUser', 'UserController@create');
    Route::post('updateUser', 'UserController@update');
    Route::delete('deleteUser', 'UserController@delete');

    // Roles
    Route::post('createRole', 'RoleController@create');
    Route::post('updateRole', 'RoleController@update');
    Route::delete('deleteRole', 'RoleController@delete');

    // Assets
    Route::post('createAsset', 'AssetController@create');
    Route::post('updateAsset', 'AssetController@update');
    Route::delete('deleteAsset', 'AssetController@delete');

    // DynamicFields
    Route::post('createDynamicField', 'DynamicFieldController@create');
    Route::post('updateDynamicField', 'DynamicFieldController@update');
    Route::delete('deleteDynamicField', 'DynamicFieldController@delete');

    // Checklists
    Route::post('createChecklist', 'ChecklistController@create');
    Route::post('updateChecklist', 'ChecklistController@update');
    Route::delete('deleteChecklist', 'ChecklistController@delete');

    // Checklist Options
    Route::post('createChecklistOption', 'ChecklistController@createOption');
    Route::post('updateChecklistOption', 'ChecklistController@updateOption');
    Route::delete('deleteChecklistOption', 'ChecklistController@deleteOption');

    // Metrics
    Route::post('createMetric', 'MetricController@create');
    Route::post('updateMetric', 'MetricController@update');
    Route::delete('deleteMetric', 'MetricController@delete');

    // Checklist Data
    Route::post('createData', 'ChecklistController@createData');
    Route::post('updateData', 'ChecklistController@updateData');
    Route::delete('deleteData', 'ChecklistController@deleteData');

    // Protocols
    Route::post('createProtocol', 'ProtocolController@create');
    Route::post('updateProtocol', 'ProtocolController@update');
    Route::delete('deleteProtocol', 'ProtocolController@delete');

    //User Teams
    Route::post('createUserTeam', 'UserTeamController@create');
    Route::post('updateUserTeam', 'UserTeamController@update');
    Route::delete('deleteUserTeam', 'UserTeamController@delete');

    //Forms
    Route::post('saveFormImage', 'FormsController@saveImage');
    Route::post('removeFormImage', 'FormsController@removeImage');

    // Items
    Route::post('createQR', 'TaskQRController@create');
    Route::delete('deleteQR', 'TaskQRController@delete');
});

///////////////////////////////////////////////////////////////////////////////////////////////

Route::namespace('App\Http\Controllers\Production')->group(function () {
    // Equipment
    Route::post('createEquipment', 'EquipmentController@create');
    Route::post('updateEquipment', 'EquipmentController@update');
    Route::delete('deleteEquipment', 'EquipmentController@delete');

    // Products
    Route::post('createProduct', 'ProductController@create');
    Route::post('updateProduct', 'ProductController@update');
    Route::delete('deleteProduct', 'ProductController@delete');

    // ProductionStop
    Route::post('createProductionStop', 'ProductionStopController@create');
    Route::post('updateProductionStop', 'ProductionStopController@update');
    Route::delete('deleteProductionStop', 'ProductionStopController@delete');

    // ProductionBreak
    Route::post('createProductionBreak', 'ProductionBreakController@create');
    Route::post('updateProductionBreak', 'ProductionBreakController@update');
    Route::delete('deleteProductionBreak', 'ProductionBreakController@delete');

    // ProductionSchedule
    Route::post('createProductionSchedule', 'ProductionScheduleController@create');
    Route::post('updateProductionSchedule', 'ProductionScheduleController@update');
    Route::delete('deleteProductionSchedule', 'ProductionScheduleController@delete');

    // ProductionInput
    Route::post('createProductionInput', 'ProductionInputController@create');
    Route::post('updateProductionInput', 'ProductionInputController@update');
    Route::delete('deleteProductionInput', 'ProductionInputController@delete');

    // ProductionFormula
    Route::post('createProductionFormula', 'ProductionFormulaController@create');
    Route::post('updateProductionFormula', 'ProductionFormulaController@update');
    Route::delete('deleteProductionFormula', 'ProductionFormulaController@delete');

    // Presentation
    Route::post('createPresentation', 'PresentationController@create');
    Route::post('updatePresentation', 'PresentationController@update');
    Route::delete('deletePresentation', 'PresentationController@delete');



    // ProductionLog



    // Production

    Route::delete('deleteProduction', 'ProductionController@delete');
    Route::post('createProductionFromApp', 'ProductionController@createFromApp');


    // Production Detail    

    Route::post('createProductionDetail', 'ProductionController@createProductionDetail');
    Route::post('updateProductionDetail', 'ProductionController@updateProductionDetail');
    Route::delete('deleteProductionDetail', 'ProductionController@deleteProductionDetail');

    // Log (ProductionController)
    Route::post('reportStop', 'ProductionController@reportStop');
    Route::post('updateReportedStop', 'ProductionController@updateReportedStop');
    Route::delete('discardStop', 'ProductionController@discardStop');
    Route::post('startStop', 'ProductionController@startStop');
    Route::post('finishStop', 'ProductionController@finishStop');
    Route::post('pauseStop', 'ProductionController@pauseStop');
    Route::post('resumeStop', 'ProductionController@resumeStop');




    Route::post('startProduction', 'ProductionController@startProduction');
    Route::post('finishProduction', 'ProductionController@finishProduction');

    // From Production Dashboard
    Route::post('updateEquipmentProduction', 'ProductionController@updateEquipmentProduction');
});

//Attendance Device
Route::post('registerAttendanceDevice', 'App\Http\Controllers\ClockinLogController@registerAttendanceDevice');



Route::namespace('App\Http\Controllers\Project')->group(function () {

    Route::get('/ganttData', 'TaskController@getGanttData');
    Route::resource('task', 'TaskController');
    Route::resource('link', 'LinkController');

    // Projects
    Route::post('createProject', 'ProjectController@create');
    Route::post('updateProject', 'ProjectController@update');
    Route::delete('deleteProject', 'ProjectController@delete');
});

//////////////////////////////////////////////////////////////////////

Route::namespace('App\Http\Controllers\Cleaning')->group(function () {
    // Cleaning Schedule
    Route::delete('deleteCleaningSchedule', 'CleaningController@deleteCleaningSchedule');
    Route::post('updateSpotCleaningInfo', 'CleaningController@updateSpotCleaningInfo');
});

Route::post('deleteFile', 'App\Http\Controllers\Tickets\TicketController@deleteFile');

//Warehouse
Route::namespace('App\Http\Controllers\Warehouse')->group(function () {
    Route::post('createWarehouse', 'WarehouseController@create')->name('createWarehouse');
    Route::post('updateWarehouse', 'WarehouseController@update');
    Route::delete('deleteWarehouse', 'WarehouseController@delete');

    //Warehouse Item
    Route::post('createWarehouseItem', 'WarehouseItemController@create');
    Route::post('updateWarehouseItem', 'WarehouseItemController@update');
    Route::delete('deleteWarehouseItem', 'WarehouseItemController@delete');
});


Route::namespace('App\Http\Controllers\Tickets')->group(function () {
    // General DashBoard
    Route::get('getgeneralStats', 'TicketController@getgeneralStats');
    Route::get('getTrendStats', 'TicketController@getTrendStats');
        // Route CDN methodo
    Route::post('uploadBase64', 'TicketController@uploadBase64');
    Route::post('deleteImage', 'TicketController@deleteImage');

    //Excel
    Route::post('syncFromExcel', 'TicketController@syncFromExcel');

});






Route::namespace('App\Http\Controllers\Config')->group(function () {
    Route::get('getUserCount', 'UserController@getUserCount');
    Route::post('setUserLimit', 'UserController@setUserLimit');
});




//Route Access Directory
Route::get('callback', 'App\Http\Controllers\AzureAuthController@callback');




// Route COUPON SHEET
Route::post('scanCoupon', 'App\Http\Controllers\CouponSheetDetailController@scanCoupon');
