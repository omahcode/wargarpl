<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiController;

// Health check (public)
Route::get('/health', [ApiController::class, 'health']);

// API V1 Group
Route::prefix('v1')->middleware('api.client')->group(function () {
    
    // Students
    Route::get('/students', [ApiController::class, 'students'])->middleware('api.client:students.read');
    Route::get('/students/{uuid}', [ApiController::class, 'student'])->middleware('api.client:students.read');
    Route::get('/students/{uuid}/class-history', [ApiController::class, 'studentClassHistory'])->middleware('api.client:students.read');
    
    // Employees
    Route::get('/employees', [ApiController::class, 'employees'])->middleware('api.client:teachers.read');
    Route::get('/employees/{uuid}', [ApiController::class, 'employee'])->middleware('api.client:teachers.read');
    
    // Classes & Majors
    Route::get('/classes', [ApiController::class, 'classes'])->middleware('api.client:classes.read');
    Route::get('/classes/{classIdOrName}/students', [ApiController::class, 'classStudents'])->middleware('api.client:classes.read');
    
    // Stats & School Configs
    Route::get('/statistics/students', [ApiController::class, 'statistics']);
    Route::get('/school-years', [ApiController::class, 'schoolYears']);
    Route::get('/semesters', [ApiController::class, 'semesters']);
});
