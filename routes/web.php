<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrganizationRegistrationController;
use App\Http\Controllers\Treasurer\ReportController;

// President Controllers
use App\Http\Controllers\President\DashboardController as PresidentDashboardController;
use App\Http\Controllers\President\EventController as PresidentEventController;
use App\Http\Controllers\President\StudentController as PresidentStudentController;
use App\Http\Controllers\President\EvaluationController as PresidentEvaluationController;
use App\Http\Controllers\President\ProfileController as PresidentProfileController;

// Adviser Controllers
use App\Http\Controllers\Adviser\DashboardController as AdviserDashboardController;
use App\Http\Controllers\Adviser\ApprovalController as AdviserApprovalController;
use App\Http\Controllers\Adviser\EvaluationController as AdviserEvaluationController;
use App\Http\Controllers\Adviser\ProfileController as AdviserProfileController;
use App\Http\Controllers\Adviser\StudentController as AdviserStudentController;
use App\Http\Controllers\Adviser\EventController as AdviserEventController;

// Treasurer Controllers
use App\Http\Controllers\Treasurer\DashboardController as TreasurerDashboardController;
use App\Http\Controllers\Treasurer\CollectionController as TreasurerCollectionController;
use App\Http\Controllers\Treasurer\ProfileController as TreasurerProfileController;

// Public Evaluation Controllers
use App\Http\Controllers\Public\EvaluationController as PublicEvaluationController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Single Login for ALL Users)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| PUBLIC EVALUATION ROUTES (Accessible via QR - NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::prefix('evaluations')->name('evaluations.')->group(function () {
    // IMPORTANT: verify-student MUST come before any {evaluation} routes
    Route::post('/verify-student', [PublicEvaluationController::class, 'verifyStudent'])->name('verify-student');
    
    // These routes come after verify-student to prevent conflicts
    Route::get('/{evaluation}/form', [PublicEvaluationController::class, 'form'])->name('form');
    Route::post('/{evaluation}', [PublicEvaluationController::class, 'store'])->name('store');
    Route::get('/thankyou', [PublicEvaluationController::class, 'thankyou'])->name('thankyou');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Uses default 'web' guard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Organizations Management
    Route::get('/organizations', [AdminController::class, 'indexOrganizations'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizationRegistrationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationRegistrationController::class, 'store'])->name('organizations.store');
    Route::get('/organizations/{organization}', [AdminController::class, 'showOrganization'])->name('organizations.show');
    Route::delete('/organizations/{organization}', [AdminController::class, 'destroyOrganization'])->name('organizations.destroy');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| PRESIDENT ROUTES (Uses 'org_user' guard)
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:president'])->prefix('president')->name('president.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PresidentDashboardController::class, 'index'])->name('dashboard');
    
    // Events Management
    Route::get('/events', [PresidentEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [PresidentEventController::class, 'create'])->name('events.create');
    Route::post('/events', [PresidentEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [PresidentEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [PresidentEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [PresidentEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [PresidentEventController::class, 'destroy'])->name('events.destroy');
    
    // Document Upload Route
    Route::post('/events/{event}/upload-document', [PresidentEventController::class, 'uploadDocument'])->name('events.upload-document');
    
    // Mark Event as Finished
    Route::post('/events/{event}/mark-finished', [PresidentEventController::class, 'markAsFinished'])->name('events.mark-finished');
    
    // Students Management
    Route::get('/students', [PresidentStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [PresidentStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [PresidentStudentController::class, 'store'])->name('students.store');
    Route::get('/students/bulk-upload', [PresidentStudentController::class, 'bulkUpload'])->name('students.bulk-upload');
    Route::post('/students/bulk-upload', [PresidentStudentController::class, 'bulkStore'])->name('students.bulk-upload.store');
    Route::put('/students/{student}', [PresidentStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [PresidentStudentController::class, 'destroy'])->name('students.destroy');
    
    // Evaluations - COMPLETE CRUD ROUTES
  // Evaluations - COMPLETE CRUD ROUTES
  Route::prefix('evaluations')->name('evaluations.')->group(function () {
    Route::get('/', [PresidentEvaluationController::class, 'index'])->name('index');
    Route::get('/create', [PresidentEvaluationController::class, 'create'])->name('create');
    Route::post('/', [PresidentEvaluationController::class, 'store'])->name('store');
    Route::get('/{evaluation}', [PresidentEvaluationController::class, 'show'])->name('show');
    Route::get('/{evaluation}/edit', [PresidentEvaluationController::class, 'edit'])->name('edit');
    Route::put('/{evaluation}', [PresidentEvaluationController::class, 'update'])->name('update');
    Route::post('/{evaluation}/activate-qr', [PresidentEvaluationController::class, 'activateQR'])->name('activate-qr');
    Route::get('/{evaluation}/qr', [PresidentEvaluationController::class, 'showQRCode'])->name('qr');
    Route::post('/{evaluation}/close', [PresidentEvaluationController::class, 'close'])->name('close');
    Route::post('/{evaluation}/reopen', [PresidentEvaluationController::class, 'reopen'])->name('reopen');
    Route::post('/{evaluation}/generate-insights', [PresidentEvaluationController::class, 'generateInsights'])->name('generate-insights');
    Route::get('/{evaluation}/ai-insights', [PresidentEvaluationController::class, 'getAIInsights'])->name('ai-insights');
    Route::delete('/{evaluation}', [PresidentEvaluationController::class, 'destroy'])->name('destroy');
    Route::post('/{evaluation}/bulk-upload', [PresidentEvaluationController::class, 'bulkUploadResponses'])->name('bulk-upload');
Route::get('/{evaluation}/download-template', [PresidentEvaluationController::class, 'downloadCsvTemplate'])->name('download-template');
});
    
    // Profile
    Route::get('/profile', [PresidentProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [PresidentProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADVISER ROUTES (Uses 'org_user' guard) - COMPLETE WITH ALL ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdviserDashboardController::class, 'index'])->name('dashboard');
    
    // Events (Read-only)
    Route::get('/events', [AdviserEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [AdviserEventController::class, 'show'])->name('events.show');
    
    // Approvals - Unified with tab support
    Route::get('/approvals', [AdviserApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/{event}', [AdviserApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{event}/approve', [AdviserApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{event}/reject', [AdviserApprovalController::class, 'reject'])->name('approvals.reject');
    
    // API Routes for stats
    Route::get('/approvals/stats', [AdviserApprovalController::class, 'getStats'])->name('approvals.stats');
    
    // Students (Read-only for advisers)
    Route::get('/students', [AdviserStudentController::class, 'index'])->name('students.index');
    
    // Evaluations - READ-ONLY ROUTES
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [AdviserEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [AdviserEvaluationController::class, 'show'])->name('show');
    });
    
    // Profile
    Route::get('/profile', [AdviserProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [AdviserProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| TREASURER ROUTES (Uses 'org_user' guard) - COMPLETE WITH ALL ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:treasurer'])->prefix('treasurer')->name('treasurer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TreasurerDashboardController::class, 'index'])->name('dashboard');
    
    // Collections
    Route::get('/collections', [TreasurerCollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/{event}', [TreasurerCollectionController::class, 'show'])->name('collections.show');
    Route::post('/collections/{event}/{student}/pay', [TreasurerCollectionController::class, 'pay'])->name('collections.pay');
    Route::post('/collections/{event}/bulk-pay', [TreasurerCollectionController::class, 'bulkPay'])->name('collections.bulk-pay');
    Route::get('/collections/{event}/summary', [TreasurerCollectionController::class, 'summary'])->name('collections.summary');
    
    // Receipt Routes - Using composite keys (event_id and student_id)
    Route::get('/receipts/{eventId}/{studentId}/download', [TreasurerCollectionController::class, 'downloadReceipt'])->name('receipts.download');
    Route::get('/receipts/{eventId}/{studentId}/view', [TreasurerCollectionController::class, 'viewReceipt'])->name('receipts.view');
    Route::post('/receipts/{eventId}/{studentId}/resend', [TreasurerCollectionController::class, 'resendReceiptEmail'])->name('receipts.resend');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/collection', [ReportController::class, 'collectionReport'])->name('reports.collection');
    Route::post('/reports/summary', [ReportController::class, 'summaryReport'])->name('reports.summary');
    
    // Profile
    Route::get('/profile', [TreasurerProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [TreasurerProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES (Remove in production if not needed)
|--------------------------------------------------------------------------
*/
Route::get('/debug-session', function() {
    return response()->json([
        'session' => session()->all(),
        'auth_org_user' => auth()->guard('org_user')->user(),
        'auth_web' => auth()->user(),
    ]);
})->middleware('auth');