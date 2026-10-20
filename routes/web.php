<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\AcademicStructureController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\AcademicTermController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\KrsManagementController;
use App\Http\Controllers\Admin\AcademicResultsController;
use App\Http\Controllers\Admin\UktPaymentController;
use App\Http\Controllers\Admin\ReferenceSettingsController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureStudent;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\AccountSettingsController;

Route::redirect('/', '/login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', EnsureAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Existing pages: dashboard, students, lecturers, academic structure, courses
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/students', [MahasiswaController::class, 'index'])->name('students.index');
        Route::get('/students/create', [MahasiswaController::class, 'create'])->name('students.create');
        Route::post('/students', [MahasiswaController::class, 'store'])->name('students.store');
        Route::get('/students/{npm}', [MahasiswaController::class, 'show'])->where('npm', '[A-Za-z0-9-]+')->name('students.show');
        Route::get('/students/{npm}/edit', [MahasiswaController::class, 'edit'])->where('npm', '[A-Za-z0-9-]+')->name('students.edit');
        Route::patch('/students/{npm}', [MahasiswaController::class, 'update'])->where('npm', '[A-Za-z0-9-]+')->name('students.update');

        Route::get('/lecturers', [DosenController::class, 'index'])->name('lecturers.index');
        Route::get('/lecturers/create', [DosenController::class, 'create'])->name('lecturers.create');
        Route::post('/lecturers', [DosenController::class, 'store'])->name('lecturers.store');
        Route::get('/lecturers/{id}/edit', [DosenController::class, 'edit'])->whereNumber('id')->name('lecturers.edit');
        Route::patch('/lecturers/{id}', [DosenController::class, 'update'])->whereNumber('id')->name('lecturers.update');
        Route::delete('/lecturers/{id}', [DosenController::class, 'destroy'])->whereNumber('id')->name('lecturers.destroy');

        Route::get('/academic-structure', [AcademicStructureController::class, 'index'])->name('academic-structure');
        Route::get('/faculties/create', [AcademicStructureController::class, 'createFaculty'])->name('faculties.create');
        Route::post('/faculties', [AcademicStructureController::class, 'storeFaculty'])->name('faculties.store');
        Route::get('/faculties/{id}/edit', [AcademicStructureController::class, 'editFaculty'])->whereNumber('id')->name('faculties.edit');
        Route::patch('/faculties/{id}', [AcademicStructureController::class, 'updateFaculty'])->whereNumber('id')->name('faculties.update');
        Route::delete('/faculties/{id}', [AcademicStructureController::class, 'destroyFaculty'])->whereNumber('id')->name('faculties.destroy');
        Route::get('/programs/create', [AcademicStructureController::class, 'createProgram'])->name('programs.create');
        Route::post('/programs', [AcademicStructureController::class, 'storeProgram'])->name('programs.store');
        Route::get('/programs/{id}/edit', [AcademicStructureController::class, 'editProgram'])->whereNumber('id')->name('programs.edit');
        Route::patch('/programs/{id}', [AcademicStructureController::class, 'updateProgram'])->whereNumber('id')->name('programs.update');
        Route::delete('/programs/{id}', [AcademicStructureController::class, 'destroyProgram'])->whereNumber('id')->name('programs.destroy');

        Route::get('/courses', [MataKuliahController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [MataKuliahController::class, 'create'])->name('courses.create');
        Route::post('/courses', [MataKuliahController::class, 'store'])->name('courses.store');
        Route::get('/courses/{id}/edit', [MataKuliahController::class, 'edit'])->whereNumber('id')->name('courses.edit');
        Route::patch('/courses/{id}', [MataKuliahController::class, 'update'])->whereNumber('id')->name('courses.update');
        Route::delete('/courses/{id}', [MataKuliahController::class, 'destroy'])->whereNumber('id')->name('courses.destroy');

        // New page 1: academic term management
        Route::get('/academic-terms', [AcademicTermController::class, 'index'])->name('academic-terms.index');
        Route::get('/academic-terms/create', [AcademicTermController::class, 'create'])->name('academic-terms.create');
        Route::post('/academic-terms', [AcademicTermController::class, 'store'])->name('academic-terms.store');
        Route::get('/academic-terms/{kode}/edit', [AcademicTermController::class, 'edit'])->where('kode', '[A-Za-z0-9]+')->name('academic-terms.edit');
        Route::patch('/academic-terms/{kode}', [AcademicTermController::class, 'update'])->where('kode', '[A-Za-z0-9]+')->name('academic-terms.update');
        Route::delete('/academic-terms/{kode}', [AcademicTermController::class, 'destroy'])->where('kode', '[A-Za-z0-9]+')->name('academic-terms.destroy');

        // New page 2: class schedule management
        Route::get('/class-schedules', [ClassScheduleController::class, 'index'])->name('class-schedules.index');
        Route::get('/class-schedules/create', [ClassScheduleController::class, 'create'])->name('class-schedules.create');
        Route::post('/class-schedules', [ClassScheduleController::class, 'store'])->name('class-schedules.store');
        Route::get('/class-schedules/{id}/edit', [ClassScheduleController::class, 'edit'])->whereNumber('id')->name('class-schedules.edit');
        Route::patch('/class-schedules/{id}', [ClassScheduleController::class, 'update'])->whereNumber('id')->name('class-schedules.update');
        Route::delete('/class-schedules/{id}', [ClassScheduleController::class, 'destroy'])->whereNumber('id')->name('class-schedules.destroy');

        // New page 3 and 4: KRS list and detailed academic advisor review
        Route::get('/krs-management', [KrsManagementController::class, 'index'])->name('krs-management.index');
        Route::get('/krs-management/{id}/review', [KrsManagementController::class, 'review'])->whereNumber('id')->name('krs-management.review');
        Route::patch('/krs-management/{id}/status', [KrsManagementController::class, 'updateStatus'])->whereNumber('id')->name('krs-management.status');

        // New page 5: KHS / transcript
        Route::get('/academic-results', [AcademicResultsController::class, 'index'])->name('academic-results.index');

        // New page 6: UKT payment management
        Route::get('/ukt-payments', [UktPaymentController::class, 'index'])->name('ukt-payments.index');
        Route::post('/ukt-payments', [UktPaymentController::class, 'store'])->name('ukt-payments.store');
        Route::patch('/ukt-payments/{id}/status', [UktPaymentController::class, 'updateStatus'])->whereNumber('id')->name('ukt-payments.status');

        // New page 7: reference settings
        Route::get('/reference-settings', [ReferenceSettingsController::class, 'index'])->name('reference-settings.index');
        Route::post('/reference-settings/{resource}', [ReferenceSettingsController::class, 'store'])
            ->where('resource', 'status-mahasiswa|jalur-masuk|ruangan')->name('reference-settings.store');
        Route::patch('/reference-settings/{resource}/{id}', [ReferenceSettingsController::class, 'update'])
            ->where('resource', 'status-mahasiswa|jalur-masuk|ruangan')->where('id', '[A-Za-z0-9-]+')->name('reference-settings.update');
        Route::delete('/reference-settings/{resource}/{id}', [ReferenceSettingsController::class, 'destroy'])
            ->where('resource', 'status-mahasiswa|jalur-masuk|ruangan')->where('id', '[A-Za-z0-9-]+')->name('reference-settings.destroy');
    // Pengaturan akun admin
    Route::get('/account-settings', [AccountSettingsController::class, 'edit'])->name('account-settings');
    Route::put('/account-settings', [AccountSettingsController::class, 'update'])->name('account-settings.update');

    });

// Portal mahasiswa. Semua data dibatasi ke NPM user yang sedang login.
Route::middleware(['auth', EnsureStudent::class])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/krs', [StudentPortalController::class, 'krs'])->name('krs');
    Route::post('/krs/details', [StudentPortalController::class, 'addKrsDetail'])->name('krs.add');
    Route::delete('/krs/details/{detail}', [StudentPortalController::class, 'removeKrsDetail'])->whereNumber('detail')->name('krs.remove');
    Route::post('/krs/submit', [StudentPortalController::class, 'submitKrs'])->name('krs.submit');
    Route::get('/khs', [StudentPortalController::class, 'khs'])->name('khs');
    Route::get('/schedule', [StudentPortalController::class, 'schedule'])->name('schedule');
    Route::get('/ukt-payments', [StudentPortalController::class, 'uktPayments'])->name('ukt-payments');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::get('/account-settings', [AccountSettingsController::class, 'edit'])->name('account-settings');
    Route::put('/account-settings', [AccountSettingsController::class, 'update'])->name('account-settings.update');
});
