<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrganizationRegistrationController;
use App\Http\Controllers\Admin\EvaluationController as AdminEvaluationController;
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
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| PUBLIC EVALUATION ROUTES (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::prefix('evaluations')->name('evaluations.')->group(function () {
    Route::get('/{evaluation}/form', [PublicEvaluationController::class, 'form'])->name('form');
    Route::post('/{evaluation}/verify', [PublicEvaluationController::class, 'verifyStudent'])->name('verify');
    Route::post('/{evaluation}', [PublicEvaluationController::class, 'store'])->name('store');
    Route::get('/{evaluation}/already-submitted', [PublicEvaluationController::class, 'alreadySubmitted'])->name('already-submitted');
    Route::get('/thankyou', [PublicEvaluationController::class, 'thankyou'])->name('thankyou');
});
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (QUAMS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Evaluation Management
    Route::get('/evaluations/pending-requests', [AdminEvaluationController::class, 'getPendingRequests'])->name('evaluations.pending-requests');
    Route::get('/evaluations', [AdminEvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/create', [AdminEvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations', [AdminEvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{evaluation}', [AdminEvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('/evaluations/{evaluation}/edit', [AdminEvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::put('/evaluations/{evaluation}', [AdminEvaluationController::class, 'update'])->name('evaluations.update');
    Route::post('/evaluations/{evaluation}/activate', [AdminEvaluationController::class, 'activate'])->name('evaluations.activate');
    Route::get('/evaluations/{evaluation}/qr', [AdminEvaluationController::class, 'showQRCode'])->name('evaluations.qr');
    Route::post('/evaluations/{evaluation}/close', [AdminEvaluationController::class, 'close'])->name('evaluations.close');
    Route::post('/evaluations/{evaluation}/reopen', [AdminEvaluationController::class, 'reopen'])->name('evaluations.reopen');
    Route::post('/evaluations/{evaluation}/generate-insights', [AdminEvaluationController::class, 'generateInsights'])->name('evaluations.generate-insights');
    Route::get('/evaluations/{evaluation}/ai-insights', [AdminEvaluationController::class, 'getAIInsights'])->name('evaluations.ai-insights');
    Route::post('/evaluations/{evaluation}/bulk-upload', [AdminEvaluationController::class, 'bulkUpload'])->name('evaluations.bulk-upload');
    Route::get('/evaluations/{evaluation}/download-template', [AdminEvaluationController::class, 'downloadCsvTemplate'])->name('evaluations.download-template');
    Route::delete('/evaluations/{evaluation}', [AdminEvaluationController::class, 'destroy'])->name('evaluations.destroy');
    Route::get('/evaluations/{evaluation}/eligibility-info', [AdminEvaluationController::class, 'getEligibilityInfo'])->name('evaluations.eligibility-info');
    
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
| PRESIDENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:president'])->prefix('president')->name('president.')->group(function () {
    Route::get('/dashboard', [PresidentDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/events', [PresidentEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [PresidentEventController::class, 'create'])->name('events.create');
    Route::post('/events', [PresidentEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [PresidentEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [PresidentEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [PresidentEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [PresidentEventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/upload-document', [PresidentEventController::class, 'uploadDocument'])->name('events.upload-document');
    Route::post('/events/{event}/mark-finished', [PresidentEventController::class, 'markAsFinished'])->name('events.mark-finished');
    Route::post('/events/{event}/request-evaluation', [PresidentEventController::class, 'requestEvaluation'])->name('events.request-evaluation');
    
    Route::get('/students', [PresidentStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [PresidentStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [PresidentStudentController::class, 'store'])->name('students.store');
    Route::get('/students/bulk-upload', [PresidentStudentController::class, 'bulkUpload'])->name('students.bulk-upload');
    Route::post('/students/bulk-upload', [PresidentStudentController::class, 'bulkStore'])->name('students.bulk-upload.store');
    Route::put('/students/{student}', [PresidentStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [PresidentStudentController::class, 'destroy'])->name('students.destroy');
    
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [PresidentEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [PresidentEvaluationController::class, 'show'])->name('show');
    });
    
    Route::get('/profile', [PresidentProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [PresidentProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADVISER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    Route::get('/dashboard', [AdviserDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/events', [AdviserEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [AdviserEventController::class, 'show'])->name('events.show');
    
    Route::get('/approvals', [AdviserApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/{event}', [AdviserApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{event}/approve', [AdviserApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{event}/reject', [AdviserApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/approvals/stats', [AdviserApprovalController::class, 'getStats'])->name('approvals.stats');
    
    Route::get('/students', [AdviserStudentController::class, 'index'])->name('students.index');
    
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [AdviserEvaluationController::class, 'index'])->name('index');
        Route::get('/{event}/results', [AdviserEvaluationController::class, 'results'])->name('results');
    });
    
    Route::get('/profile', [AdviserProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [AdviserProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| TREASURER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:treasurer'])->prefix('treasurer')->name('treasurer.')->group(function () {
    Route::get('/dashboard', [TreasurerDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/collections', [TreasurerCollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/{event}', [TreasurerCollectionController::class, 'show'])->name('collections.show');
    Route::post('/collections/{event}/{student}/pay', [TreasurerCollectionController::class, 'pay'])->name('collections.pay');
    Route::post('/collections/{event}/bulk-pay', [TreasurerCollectionController::class, 'bulkPay'])->name('collections.bulk-pay');
    Route::get('/collections/{event}/summary', [TreasurerCollectionController::class, 'summary'])->name('collections.summary');
    
    Route::get('/receipts/{eventId}/{studentId}/download', [TreasurerCollectionController::class, 'downloadReceipt'])->name('receipts.download');
    Route::get('/receipts/{eventId}/{studentId}/view', [TreasurerCollectionController::class, 'viewReceipt'])->name('receipts.view');
    Route::post('/receipts/{eventId}/{studentId}/resend', [TreasurerCollectionController::class, 'resendReceiptEmail'])->name('receipts.resend');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/collection', [ReportController::class, 'collectionReport'])->name('reports.collection');
    Route::post('/reports/summary', [ReportController::class, 'summaryReport'])->name('reports.summary');
    
    Route::get('/profile', [TreasurerProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [TreasurerProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES (Remove in production)
|--------------------------------------------------------------------------
*/
Route::get('/debug-session', function() {
    return response()->json([
        'session' => session()->all(),
        'auth_org_user' => auth()->guard('org_user')->user(),
        'auth_web' => auth()->user(),
    ]);
})->middleware('auth');