<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main;
use App\Http\Controllers\CSV;
use Illuminate\Support\Facades\Auth;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['middleware' => 'auth'], function () {
    Route::get('/',[Main::class, "index"]);
    Route::get('/home',[Main::class, "index"]);
    Route::get('/Owners',[Main::class, "owners"]);
    Route::get('/Owners/Add',[Main::class, "add"]);
    Route::get('/Owner/Edit/{id}',[Main::class, "editowner"]);
    Route::post('/editowner/{id}',[Main::class, "updateowner"]);
    Route::get('/Truck',[Main::class, "truck"]);
    Route::get('/Dispatcher/Add',[Main::class, "addDispatcher"]);
    Route::get('/Truck/Add',[Main::class, "addTruck"]);
    Route::get('/Edit-Truck/{id}',[Main::class, "edittruck"]);
    Route::post('/updatetruck/{id}',[Main::class, "updatetruck"]);
    Route::get('/Drivers',[Main::class, "drivers"]);
    Route::get('/Old-Drivers',[Main::class, "old_drivers"]);
    Route::get('/Dispatcher',[Main::class, "dispatcher"]);
    Route::get('/Drivers/Add',[Main::class, "addDriver"]);
    Route::get('/Edit-Dispatcher/{id}',[Main::class, "editdispatcher"]);
    Route::post('/updatedispatcher/{id}',[Main::class, "updatedispatcher"]);
    Route::get('/Edit-Driver/{id}',[Main::class, "editdriver"]);
    Route::post('/updatedriver/{id}',[Main::class, "updatedriver"]);
    Route::get('/Drivers/Roster',[Main::class, "roster"]);
    Route::get('/Truck/Roster',[Main::class, "truckRoster"]);
    Route::post('/SaveOwner',[Main::class, "saveOwner"]);
    Route::post('/SaveTruck',[Main::class, "saveTruck"]);
    Route::post('/SaveDriver',[Main::class, "saveDriver"]);
    Route::post('/SaveDispatcher',[Main::class, "saveDispatcher"]);
    Route::get('/ActivateDriver/{id}',[Main::class, "activate"]);
    Route::get('/De-ActivateDriver/{id}',[Main::class, "deactivate"]);
    Route::get('/Delete-Owner/{id}',[Main::class, "deleteOwner"]);
    Route::get('/Delete-Driver/{id}',[Main::class, "deleteDriver"]);
    Route::post('/Delete-Truck/{id}',[Main::class, "deleteTruck"]);
    Route::post('/getOwnerDetails',[Main::class, "getOwnerDetails"]);//getOwnerDetails
    Route::post('/getDriverDetails',[Main::class, "getDriverDetails"]);//getOwnerDetails
    Route::post('/getTruckDetails',[Main::class, "getTruckDetails"]);//getOwnerDetails
    Route::post('/getDispatcherDetails',[Main::class, "getDispatcherDetails"]);//getOwnerDetails
    Route::post('/loadTrucks',[Main::class, "loadTrucks"]);//getOwnerDetails
    Route::get('/Truck-Dispatch',[Main::class, "dispatch_view"]);//getOwnerDetails
    Route::post('/dispatchTrucks',[Main::class, "getDispatchTrucks"]);//getOwnerDetails
    Route::post('/Change-Status',[Main::class, "changeStatus"]);
    Route::post('/Change-Status',[Main::class, "changeStatus"]);
    Route::get('/Truck/Dispatch',[Main::class, "dispatchTruck"]);
    Route::post('getNonDispatchTrucks',[Main::class, "getNonDispatchTrucks"]);
    Route::get('DispatchTruck/{truck_id}/{dis_id}',[Main::class, "dispatchTrucks"]);
    Route::get('Delete-Dispatch/{id}',[Main::class, "deleteDispatch"]);
    Route::get('Delete-Dispatcher/{id}',[Main::class, "deleteDispatcher"]);
    Route::get('/Dispatcher/Roster',[Main::class, "dispatcherRoster"]);
    Route::get('/Documents',[Main::class, "documents"]);
    Route::post('/Upload',[Main::class, "upload"]);
    Route::get('/Upcomings',[Main::class, "upcomings"]);
    Route::get('/Categories',[Main::class, "categories"]);
    Route::post('/Save-Category',[Main::class, "saveCategory"]);
    Route::get('/Delete-Category/{id}',[Main::class, "deleteCategory"]);
    Route::post('/saveTruckAccountInfo',[Main::class, "saveTruckAccountInfo"]);
    Route::get('/Truck/Accounting/PDF/{id}',[Main::class, "truckAccountPDF"]);
    Route::get('/Dispatch-Statement',[Main::class, "dispatchStatement"]);
    Route::post('/getWeeksByTruck',[Main::class, "getWeeksByTruck"]);
    Route::get('/Profile',[Main::class, "profile"]);
    Route::post('/UpdateProfile',[Main::class, "updateProfile"]);
    Route::get('/YTD',[Main::class, "ytd"]);
    Route::get('/YTD/2',[Main::class, "ytd_2"]);
    Route::post('/getCompanyTrucks',[Main::class, "getCompanyTrucks"]);//getCompanyTrucks
    Route::get('/Invoice/Create',[Main::class, "createInvoice"]);
    Route::post('/SaveInvoice',[Main::class, "saveInvoice"]);
    Route::get('/Invoice/Pending',[Main::class, "pendingInvoice"]);
    Route::get('/Invoice/Paid',[Main::class, "paidInvoice"]);
    Route::get('/Delete-Invoice/{id}',[Main::class, "deleteInvoice"]);
    Route::get('/Invoice-Paid/{id}',[Main::class, "payInvoice"]);
    Route::get('/Edit-Invoice/{id}',[Main::class, "editInvoice"]);
    Route::post('/UpdateInvoice/{id}',[Main::class, "updateInvoice"]);
    Route::get('/1099',[Main::class, "onenineReport"]);
    Route::post('/getYear',[Main::class, "getYear"]);
    Route::get('/Accident-Report',[Main::class, "accident"]);
    Route::post('/SaveReport',[Main::class, "saveReport"]);
    Route::get('/Delete-Report/{id}',[Main::class, "deleteReport"]);
    Route::get('/Users',[Main::class, "users"]);
    Route::post('/Save-User',[Main::class, "saveUser"]);
    Route::post('/getdispatchfilterdata',[Main::class, "getdispatchfilterdata"]);
    Route::get('/Truck/Accounting',[Main::class, "truckAccounting"]);
    Route::get('/Truck/Accounting/{id}',[Main::class, "truckaccounting_"]);
    Route::get('/Truck/Accounting/Delete/{id}',[Main::class, "truckAccountingDelete"]);
    Route::get('/Truck/Accounting/previous/{id}',[Main::class, "truckaccounting_previous"]);
    Route::POST('/dispatch/truck/{id}',[Main::class, "dispatch_truck"]);
    Route::POST('/saveTruckExpense',[Main::class, "saveTruckExpense"]);
    Route::get('/Dispatch/Statement/PDF/{id}',[Main::class, "Dispatch_Statement_PDF_"]);
    Route::get('/Dispatch/Statement/PDF',[Main::class, "Dispatch_Statement_PDF"]);
    Route::get('/Return/truck/{id}',[Main::class, "return_truck"]);
    Route::get('/print-Invoice/{id}',[Main::class, "invoice_pdf"]);
    Route::get('/make/csv/{company_id}/{year}',[CSV::class, "make_csv"]);
    Route::get('View/Company',[Main::class, "view_company"]);
    Route::post('save_company',[Main::class, "save_company"]);
    Route::post('update_company/{id}',[Main::class, "update_company"]);
    Route::get('Delete-company/{id}',[Main::class, "delete_company"]);
    Route::get('Company/Expense',[Main::class, "view_company_expense"]);
    Route::post('save_expense',[Main::class, "save_expense"]);
    Route::post('update_expense/{id}',[Main::class, "update_expense"]);
    Route::get('Delete-expense/{id}',[Main::class, "delete_expense"]);
    Route::get('escrow',[Main::class, "escrow"]);
    Route::get('escrow/return',[Main::class, "escrow_return"]);
    Route::get('Quite-Truck/{id}',[Main::class, "quite_truck"]);
    Route::get('go/escrow/return/{id}',[Main::class, "do_escrow_return"]);
    Route::get('go/escrow/returnToEscrow/{id}',[Main::class, "do_escrow_return_to_escrow"]);
    Route::post('/1099/pdf',[Main::class, "make_1099"]);
    Route::get('/archive-Truck/{id}',[Main::class, "archive_truck"]);
    Route::get('/archive-owner/{id}',[Main::class, "archive_owner"]);
    Route::get('/archive-dispatcher/{id}',[Main::class, "archive_dispatcher"]);
    Route::get('/archive-driver/{id}',[Main::class, "archive_driver"]);
    Route::get('rehire/driver/{id}',[Main::class, "rehire_driver"]);
    Route::get('/Old-trucks',[Main::class, "old_trucks"]);
    Route::get('rehire/truck/{id}',[Main::class, "rehire_truck"]);
    Route::get('/Old-Owners',[Main::class, "old_owners"]);
    Route::get('rehire/owner/{id}',[Main::class, "rehire_owner"]);
    Route::get('/Old-dispatcher',[Main::class, "old_dispatcher"]);
    Route::get('rehire/dispatcher/{id}',[Main::class, "rehire_dispatcher"]);
    Route::get('/Old-Company',[Main::class, "old_company"]);
    Route::get('rehire/company/{id}',[Main::class, "rehire_company"]);
    Route::get('uploads/delete/{id}',[Main::class, "deleteDoc"]);
    Route::get('/download-files/{id}',[Main::class, "download_files"]);
    Route::post('/sendEmailPDF/{id}',[Main::class, "sendEmail"]);
    Route::post('/sendTruckAccountingPDF/{id}',[Main::class, "sendTruckAccountingEmail"]);
    Route::post('/sendInvoicePDF/{id}',[Main::class, "sendInvoicePDF"]);
    Route::post('/update-settings',[Main::class, "updateSettings"]);

    Route::get('/perc-settings',[Main::class, "percSettings"]);
    Route::post('/save/notification',[Main::class, "saveNotification"]);
    Route::get('/update/notification/{id}',[Main::class, "resolveNotification"]);
    Route::post('add/note',[Main::class, "saveNote"]);
    Route::get('Delete-note/{id}',[Main::class, "deleteNote"]);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/cron/truck/dispatch/clear/after/week',[Main::class, "cron_truck_dispatch_clear_after_week"]);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
