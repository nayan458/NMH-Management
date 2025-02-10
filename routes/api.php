<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessFeeManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RebateController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentManagementController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:sanctum'],function () {
    /*
    ||--------------------------------------------------------------------------
    || ADMIN Routes
    ||--------------------------------------------------------------------------
    ||
    || Here are admin routes that are used to define.
    || routes are loaded by the AdminController 
    ||
    */

    Route::group(['middleware' => 'abilities:Admin', 'prefix' => 'admin'], function () {
        
        // Admin logout
        Route::controller(AuthController::class)->group( function() {
            Route::get('/logout', 'logout');
        });

        // AdminController
        Route::controller(AdminController::class)->prefix('admin')->group(function () {
            // view all rooms
            // view all students and allocated room
            // view all mess fee payable students
            // view all rebate students
        });

        // RoomController
        Route::controller(RoomController::class)->prefix('room')->group(function () {
            // Add room
            Route::post('/create', 'createRoom');
            // view all rooms
            Route::get('/view-rooms', 'viewRooms');
            // view room by id
            Route::get('/view-room/{id}', 'viewRoom');
            // update room by id
            Route::put('/update-room/{id}', 'updateRoom');
            // delete room by id
            Route::delete('/delete-room/{id}', 'deleteRoom');
        });

        // StudentManagementController
        Route::controller(StudentManagementController::class)->prefix('student-management')->group(function () {
            // add student
            Route::post('/add-student', 'addStudent');
            // allocate room
            Route::post('/allocate-room', 'allocateRoom');
            // view all students
            Route::get('/view-students', 'viewAllStudents');
            // view student by id
            Route::get('/view-student/{id}', 'viewStudentById');
            // update student by id
            Route::put('/update-student/{id}', 'updateStudentById');
            // delete student by id
            Route::delete('/delete-student/{id}', 'deleteStudentById');
        });

        // MessFeeManagementController
        Route::controller(MessFeeManagementController::class)->prefix('mess-fee-management')->group(function () {
            // add mess fee
            Route::post('/add-mess-fee', 'addMessFee');
            // update mess fee by id
            Route::put('/update-mess-fee/{id}', 'updateMessFee');
            // view all mess fee
            // view mess fee by id
            // delete mess fee by id
        });

        // InvoiceController
        Route::controller(InvoiceController::class)->prefix('invoice')->group(function () {
            // view all invoices
            // view invoice by id
            // update invoice by id
            // delete invoice by id
        });

        // RebateController
        Route::controller(RebateController::class)->prefix('rebate')->group(function () {
            // view all rebate
            // view rebate by id
            // update rebate by id
            // delete rebate by id
        });

        // NotificationController
        Route::controller(NotificationController::class)->prefix('notice')->group(function () {
            // view all notices
            // view notice by id
            // update notice by id
            // delete notice by id
        });
    });

    /*
    ||--------------------------------------------------------------------------
    || STUDENT Routes
    ||--------------------------------------------------------------------------
    ||
    || Here are student routes that are used to define.
    || routes are loaded by the StudentController 
    ||
    */

    Route::group(['middleware' => 'abilities:Student', 'prefix' => 'student'], function () {
        // StudentController
        Route::controller(StudentController::class)->group(function () {
            // view student by id
            // update student by id
            // delete student by id
        });
    });

});


Route::post('create-order',[PaymentController::class, 'createOrder']);
Route::post('handle-payment',[PaymentController::class, 'handlePayment']);

/*
||--------------------------------------------------------------------------
|| ADMIN Routes
||--------------------------------------------------------------------------
||
|| Here are admin routes that are used to define.
|| routes are loaded by the AdminController 
||
*/

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'adminLogin');
});

Route::controller(RebateController::class)->group(function(){
    Route::post('request-rebate', 'requestRebate');
    Route::post('update-rebate-status', 'updateRebateStatus');
});