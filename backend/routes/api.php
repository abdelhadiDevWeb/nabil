<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users/me', [AuthController::class, 'me']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/employee/password-reset-request', [AuthController::class, 'passwordResetRequest']);

// Admin routes
Route::middleware('admin')->group(function () {
    Route::get('/admin/employees', [AdminController::class, 'getEmployees']);
    Route::post('/admin/employees', [AdminController::class, 'createEmployee']);
    Route::post('/admin/payslips', [AdminController::class, 'uploadPayslip']);
    Route::post('/admin/salary-charts', [AdminController::class, 'uploadSalaryChart']);
    Route::get('/admin/logs', [AdminController::class, 'getConnectionLogs']);
    Route::get('/admin/password-requests', [AdminController::class, 'getPasswordRequests']);
    
    // Events
    Route::get('/admin/events', [AdminController::class, 'getEvents']);
    Route::post('/admin/events', [AdminController::class, 'createEvent']);
    Route::put('/admin/events/{id}', [AdminController::class, 'updateEvent']);
    Route::delete('/admin/events/{id}', [AdminController::class, 'deleteEvent']);
    
    // Announcements
    Route::get('/admin/announcements', [AdminController::class, 'getAnnouncements']);
    Route::post('/admin/announcements', [AdminController::class, 'createAnnouncement']);
    Route::put('/admin/announcements/{id}', [AdminController::class, 'updateAnnouncement']);
    Route::delete('/admin/announcements/{id}', [AdminController::class, 'deleteAnnouncement']);
    
    // Administrative Documents
    Route::get('/admin/documents', [AdminController::class, 'getDocuments']);
    Route::post('/admin/documents', [AdminController::class, 'createDocument']);
    Route::put('/admin/documents/{id}', [AdminController::class, 'updateDocument']);
    Route::delete('/admin/documents/{id}', [AdminController::class, 'deleteDocument']);
    
    // Employee Requests
    Route::get('/admin/requests', [AdminController::class, 'getEmployeeRequests']);
    Route::put('/admin/requests/{id}', [AdminController::class, 'handleEmployeeRequest']);
});

// Employee routes
Route::middleware('employee')->group(function () {
    Route::get('/employee/profile', [EmployeeController::class, 'getProfile']);
    Route::get('/employee/payslips', [EmployeeController::class, 'getPayslips']);
    Route::get('/employee/salary-chart', [EmployeeController::class, 'getSalaryChart']);
    Route::get('/employee/events', [EmployeeController::class, 'getEvents']);
    Route::get('/employee/announcements', [EmployeeController::class, 'getAnnouncements']);
    Route::get('/employee/documents', [EmployeeController::class, 'getDocuments']);
    Route::get('/employee/requests', [EmployeeController::class, 'getRequests']);
    Route::post('/employee/requests', [EmployeeController::class, 'createRequest']);
});