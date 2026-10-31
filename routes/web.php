<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;

// Admin controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\SchoolClassController as AdminSchoolClassController;
use App\Http\Controllers\Admin\MajorController as AdminMajorController;
use App\Http\Controllers\Admin\PtkTypeController as AdminPtkTypeController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\ApiClientController as AdminApiClientController;
use App\Http\Controllers\Admin\ImportExportController as AdminImportExportController;
use App\Http\Controllers\Admin\AcademicPeriodController as AdminAcademicPeriodController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password')->middleware('auth');

// Redirect root to dashboard/login
Route::get('/', function () {
    return redirect()->route('login');
});

// Student Protected Routes
Route::middleware(['auth', 'role:siswa'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/verify-correct', [StudentDashboardController::class, 'verifyCorrect'])->name('verify-correct');
    Route::get('/edit', [StudentDashboardController::class, 'showEdit'])->name('edit');
    Route::post('/edit', [StudentDashboardController::class, 'update'])->name('update');
});

// Employee Protected Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [EmployeeDashboardController::class, 'classes'])->name('classes');
    Route::get('/profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [EmployeeDashboardController::class, 'update'])->name('update');
    Route::get('/search-students', [EmployeeDashboardController::class, 'searchApi'])->name('search-students');
});

// Admin Protected Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');
    
    // Students CRUD
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::post('/students/delete-all', [AdminStudentController::class, 'deleteAll'])->name('students.delete-all');
    Route::get('/students/import', [AdminImportExportController::class, 'showStudentImportUi'])->name('students.import-export-ui');
    Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
    Route::get('/students/{uuid}', [AdminStudentController::class, 'show'])->name('students.show');
    Route::get('/students/{uuid}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{uuid}', [AdminStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{uuid}', [AdminStudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{uuid}/unlock', [AdminStudentController::class, 'unlock'])->name('students.unlock');
    Route::post('/students/{uuid}/approve-correction', [AdminStudentController::class, 'approveCorrection'])->name('students.approve-correction');
    Route::post('/students/{uuid}/reject-correction', [AdminStudentController::class, 'rejectCorrection'])->name('students.reject-correction');
    Route::post('/students/{uuid}/reset-password', [AdminStudentController::class, 'resetPassword'])->name('students.reset-password');
    
    // Students Import / Export Preview & Actions
    Route::post('/students-import-preview', [AdminImportExportController::class, 'previewImport'])->name('students.import-preview');
    Route::post('/students-import-submit', [AdminImportExportController::class, 'submitImport'])->name('students.import-submit');
    Route::post('/students-import-chunk', [AdminImportExportController::class, 'submitImportChunk'])->name('students.import-chunk');
    Route::get('/students-export', [AdminImportExportController::class, 'export'])->name('students.export');
    
    // Employees CRUD
    Route::get('/employees', [AdminEmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees/delete-all', [AdminEmployeeController::class, 'deleteAll'])->name('employees.delete-all');
    Route::get('/employees/import', [AdminImportExportController::class, 'showEmployeeImportUi'])->name('employees.import-ui');
    Route::get('/employees/create', [AdminEmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [AdminEmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{uuid}', [AdminEmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{uuid}/edit', [AdminEmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{uuid}', [AdminEmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{uuid}', [AdminEmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/{uuid}/reset-password', [AdminEmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    
    // Academics (Classes & Majors) CRUD
    Route::get('/classes', [AdminSchoolClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [AdminSchoolClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}', [AdminSchoolClassController::class, 'show'])->name('classes.show');
    Route::put('/classes/{id}', [AdminSchoolClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}', [AdminSchoolClassController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/delete-all', [AdminSchoolClassController::class, 'deleteAll'])->name('classes.delete-all');

    Route::get('/majors', [AdminMajorController::class, 'index'])->name('majors.index');
    Route::post('/majors', [AdminMajorController::class, 'store'])->name('majors.store');
    Route::put('/majors/{id}', [AdminMajorController::class, 'update'])->name('majors.update');
    Route::delete('/majors/{id}', [AdminMajorController::class, 'destroy'])->name('majors.destroy');
    Route::post('/majors/delete-all', [AdminMajorController::class, 'deleteAll'])->name('majors.delete-all');

    // PTK Types CRUD
    Route::get('/ptk-types', [AdminPtkTypeController::class, 'index'])->name('ptk-types.index');
    Route::post('/ptk-types', [AdminPtkTypeController::class, 'store'])->name('ptk-types.store');
    Route::put('/ptk-types/{id}', [AdminPtkTypeController::class, 'update'])->name('ptk-types.update');
    Route::delete('/ptk-types/{id}', [AdminPtkTypeController::class, 'destroy'])->name('ptk-types.destroy');
    
    // API Integrations (Token Management)
    Route::get('/clients', [AdminApiClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [AdminApiClientController::class, 'store'])->name('clients.store');
    Route::post('/clients/{id}/toggle', [AdminApiClientController::class, 'toggleStatus'])->name('clients.toggle');
    Route::post('/clients/{id}/regenerate', [AdminApiClientController::class, 'regenerateToken'])->name('clients.regenerate');
    Route::delete('/clients/{id}', [AdminApiClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/api-docs', fn() => view('admin.api-docs'))->name('api-docs');
    
    // Monitoring & Auditing
    Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring');
    Route::get('/audit-logs', [AdminAuditController::class, 'index'])->name('audit-logs');

    // Academic Periods Management
    Route::get('/periods', [AdminAcademicPeriodController::class, 'index'])->name('periods.index');
    Route::post('/periods/school-year', [AdminAcademicPeriodController::class, 'storeSchoolYear'])->name('periods.school-year.store');
    Route::post('/periods/school-year/{id}/activate', [AdminAcademicPeriodController::class, 'activateSchoolYear'])->name('periods.school-year.activate');
    Route::post('/periods/semester/{id}/activate', [AdminAcademicPeriodController::class, 'activateSemester'])->name('periods.semester.activate');
    Route::delete('/periods/school-year/{id}', [AdminAcademicPeriodController::class, 'destroySchoolYear'])->name('periods.school-year.destroy');
});
