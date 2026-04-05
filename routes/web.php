<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrganizationRegistrationController;
use App\Http\Controllers\Admin\EvaluationController as AdminEvaluationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController as AdminLogController;

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
use App\Http\Controllers\Treasurer\ReportController as TreasurerReportController;

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
    // Main form - includes verification
    Route::get('/{evaluation}/form', [PublicEvaluationController::class, 'form'])->name('form');
    
    // API endpoints for the form
    Route::post('/{evaluation}/verify', [PublicEvaluationController::class, 'verifyStudent'])->name('verify');
    Route::post('/{evaluation}', [PublicEvaluationController::class, 'store'])->name('store');
    Route::get('/{evaluation}/student-submissions', [PublicEvaluationController::class, 'getStudentSubmissions'])->name('student-submissions');
    
    // Redirect old verify page to form (to prevent 404/422 errors)
    Route::get('/{evaluation}/verify-page', [PublicEvaluationController::class, 'form'])->name('verify-page');
    
    // Other pages
    Route::get('/{evaluation}/already-submitted', [PublicEvaluationController::class, 'alreadySubmitted'])->name('already-submitted');
    Route::get('/thankyou', [PublicEvaluationController::class, 'thankyou'])->name('thankyou');
    Route::get('/{evaluation}/dates', [PublicEvaluationController::class, 'getAvailableDates'])->name('dates');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (QUAMS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // ==================== EVALUATION MANAGEMENT ====================
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/pending-requests', [AdminEvaluationController::class, 'getPendingRequests'])->name('pending-requests');
        Route::get('/', [AdminEvaluationController::class, 'index'])->name('index');
        Route::get('/create', [AdminEvaluationController::class, 'create'])->name('create');
        Route::post('/', [AdminEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}', [AdminEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/edit', [AdminEvaluationController::class, 'edit'])->name('edit');
        Route::put('/{evaluation}', [AdminEvaluationController::class, 'update'])->name('update');
        Route::post('/{evaluation}/activate', [AdminEvaluationController::class, 'activate'])->name('activate');
        Route::get('/{evaluation}/qr', [AdminEvaluationController::class, 'showQRCode'])->name('qr');
        Route::post('/{evaluation}/close', [AdminEvaluationController::class, 'close'])->name('close');
        Route::post('/{evaluation}/reopen', [AdminEvaluationController::class, 'reopen'])->name('reopen');
        
        // AI Insights Routes
        Route::post('/{evaluation}/generate-insights', [AdminEvaluationController::class, 'generateInsights'])->name('generate-insights');
        Route::get('/{evaluation}/ai-insights', [AdminEvaluationController::class, 'getAIInsights'])->name('ai-insights');
        
        // Data export routes
        Route::get('/{evaluation}/raw-responses', [AdminEvaluationController::class, 'getRawResponses'])->name('raw-responses');
        Route::get('/{evaluation}/stats', [AdminEvaluationController::class, 'getStatsByDate'])->name('stats');
        Route::post('/{evaluation}/bulk-upload', [AdminEvaluationController::class, 'bulkUpload'])->name('bulk-upload');
        Route::get('/{evaluation}/download-template', [AdminEvaluationController::class, 'downloadCsvTemplate'])->name('download-template');
        Route::delete('/{evaluation}', [AdminEvaluationController::class, 'destroy'])->name('destroy');
        Route::get('/{evaluation}/eligibility-info', [AdminEvaluationController::class, 'getEligibilityInfo'])->name('eligibility-info');
    });
    
    // ==================== REPORTS MANAGEMENT ====================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::post('/{evaluation}/generate', [AdminReportController::class, 'generateReport'])->name('generate');
        Route::post('/{evaluation}/regenerate', [AdminReportController::class, 'regenerateReport'])->name('regenerate');
        Route::get('/{evaluation}/view', [AdminReportController::class, 'viewReport'])->name('view');
        Route::get('/{evaluation}/download', [AdminReportController::class, 'downloadReport'])->name('download');
        Route::post('/{evaluation}/send', [AdminReportController::class, 'sendReport'])->name('send');
    });
    
    // ==================== ORGANIZATIONS MANAGEMENT ====================
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [AdminController::class, 'indexOrganizations'])->name('index');
        Route::get('/create', [OrganizationRegistrationController::class, 'create'])->name('create');
        Route::post('/', [OrganizationRegistrationController::class, 'store'])->name('store');
        Route::get('/{organization}', [AdminController::class, 'showOrganization'])->name('show');
        Route::get('/{organization}/edit', [OrganizationRegistrationController::class, 'edit'])->name('edit');
        Route::put('/{organization}', [OrganizationRegistrationController::class, 'update'])->name('update');
        Route::delete('/{organization}', [AdminController::class, 'destroyOrganization'])->name('destroy');
        Route::get('/{organization}/members', [OrganizationRegistrationController::class, 'getOrganizationMembers'])->name('members');
        Route::get('/{organization}/settings', [OrganizationRegistrationController::class, 'getOrganizationSettings'])->name('settings');
        Route::post('/{organization}/members', [AdminController::class, 'addOrganizationMember'])->name('members.store');
    });
    
    // ==================== USER MANAGEMENT (Organization Users) ====================
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('/{user}/block', [AdminController::class, 'blockUser'])->name('block');
        Route::post('/{user}/unblock', [AdminController::class, 'unblockUser'])->name('unblock');
        Route::put('/{user}', [AdminController::class, 'updateOrganizationMember'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'deleteOrganizationMember'])->name('delete');
        Route::post('/{user}/reset-password', [AdminController::class, 'resetMemberPassword'])->name('reset-password');
    });
    
    // ==================== LOG MANAGEMENT ====================
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [AdminLogController::class, 'index'])->name('index');
        Route::get('/export', [AdminLogController::class, 'export'])->name('export');
        Route::get('/auth', [AdminLogController::class, 'getAuthLogs'])->name('auth');
        Route::get('/action', [AdminLogController::class, 'getActionLogs'])->name('action');
        Route::post('/clear', [AdminLogController::class, 'clear'])->name('clear');
    });
    
    // ==================== PROFILE ====================
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
    
    // Events
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
    Route::post('/events/{event}/refresh-students', [PresidentEventController::class, 'refreshEligibleStudents'])->name('events.refresh-students');
    Route::post('/events/{event}/sync-students', [PresidentEventController::class, 'syncAllEligibleStudents'])->name('events.sync-students');
    
    // Students
    Route::get('/students', [PresidentStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [PresidentStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [PresidentStudentController::class, 'store'])->name('students.store');
    Route::get('/students/bulk-upload', [PresidentStudentController::class, 'bulkUpload'])->name('students.bulk-upload');
    Route::post('/students/bulk-upload', [PresidentStudentController::class, 'bulkStore'])->name('students.bulk-upload.store');
    Route::get('/students/{student}/edit', [PresidentStudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [PresidentStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [PresidentStudentController::class, 'destroy'])->name('students.destroy');
    
    // Guests
    Route::get('/events/{event}/guests', [PresidentEventController::class, 'showGuests'])->name('events.guests');
    Route::post('/events/{event}/guests', [PresidentEventController::class, 'addGuest'])->name('events.guests.store');
    Route::post('/events/{event}/guests/bulk', [PresidentEventController::class, 'bulkAddGuests'])->name('events.guests.bulk');
    Route::delete('/events/{event}/guests/{guest}', [PresidentEventController::class, 'deleteGuest'])->name('events.guests.delete');
    Route::get('/events/{event}/guests/template', [PresidentEventController::class, 'downloadGuestTemplate'])->name('events.guests.template');
    
    // Evaluations
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [PresidentEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [PresidentEvaluationController::class, 'show'])->name('show');
    });
    
    // Profile
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
    
    // Events (Read-only)
    Route::get('/events', [AdviserEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [AdviserEventController::class, 'show'])->name('events.show');
    
    // Approvals
    Route::get('/approvals', [AdviserApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/history', [AdviserApprovalController::class, 'history'])->name('approvals.history');
    Route::get('/approvals/{event}', [AdviserApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{event}/approve', [AdviserApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{event}/reject', [AdviserApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/approvals/stats', [AdviserApprovalController::class, 'getStats'])->name('approvals.stats');
    
    // Students (Read-only)
    Route::get('/students', [AdviserStudentController::class, 'index'])->name('students.index');
    
    // Evaluations (Read-only)
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
| TREASURER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['org_user:treasurer'])->prefix('treasurer')->name('treasurer.')->group(function () {
    Route::get('/dashboard', [TreasurerDashboardController::class, 'index'])->name('dashboard');
    
    // Collection Management
    Route::get('/collections', [TreasurerCollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/{event}', [TreasurerCollectionController::class, 'show'])->name('collections.show');
    Route::post('/collections/{event}/{student}/pay', [TreasurerCollectionController::class, 'pay'])->name('collections.pay');
    Route::post('/collections/{event}/bulk-pay', [TreasurerCollectionController::class, 'bulkPay'])->name('collections.bulk-pay');
    Route::get('/collections/{event}/summary', [TreasurerCollectionController::class, 'summary'])->name('collections.summary');
    
    // Receipt Management
    Route::get('/receipts/{eventId}/{studentId}/download', [TreasurerCollectionController::class, 'downloadReceipt'])->name('receipts.download');
    Route::get('/receipts/{eventId}/{studentId}/view', [TreasurerCollectionController::class, 'viewReceipt'])->name('receipts.view');
    Route::post('/receipts/{eventId}/{studentId}/resend', [TreasurerCollectionController::class, 'resendReceiptEmail'])->name('receipts.resend');
    
    // Report Management
    Route::get('/reports', [TreasurerReportController::class, 'index'])->name('reports.index');
    Route::post('/collection-reports/{eventId}/generate', [TreasurerReportController::class, 'generate'])->name('collection-reports.generate');
    Route::post('/collection-reports/{eventId}/regenerate', [TreasurerReportController::class, 'regenerate'])->name('collection-reports.regenerate');
    Route::get('/collection-reports/{eventId}/view', [TreasurerReportController::class, 'view'])->name('collection-reports.view');
    Route::get('/collection-reports/{eventId}/download', [TreasurerReportController::class, 'download'])->name('collection-reports.download');
    Route::post('/reports/summary', [TreasurerReportController::class, 'summaryReport'])->name('reports.summary');
    Route::post('/reports/collection', [TreasurerReportController::class, 'collectionReport'])->name('reports.collection');
    
    // Profile
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

// TEMPORARY ROUTES - Remove after use
Route::get('/migrate-fresh', function() {
    try {
        \Artisan::call('migrate:fresh', ['--force' => true]);
        return "✅ Database reset completed!<br><br>" . nl2br(\Artisan::output());
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/run-seeders', function() {
    try {
        \Artisan::call('db:seed', ['--force' => true]);
        return "✅ Seeders completed!<br><br>" . nl2br(\Artisan::output());
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/check-db', function() {
    try {
        \DB::connection()->getPdo();
        return "✅ Database connection successful!";
    } catch (\Exception $e) {
        return "❌ Database connection failed: " . $e->getMessage();
    }
});

Route::get('/test-email', function() {
    try {
        \Mail::raw('Test email from EventFlow on Railway!', function($message) {
            $message->to('daleoncarpio@gmail.com')
                    ->subject('Test Email from EventFlow');
        });
        return "✅ Email sent successfully! Check your inbox.";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});
Route::get('/test-ai-direct', function() {
    try {
        $response = Illuminate\Support\Facades\Http::timeout(30)->post('https://creative-fulfillment-production.up.railway.app/analyze', [
            'positive_comments' => ['This event was great!', 'Very organized event'],
            'suggestion_comments' => [],
            'total_respondents' => 2,
            'response_rate' => 1.0,
        ]);
        
        return response()->json([
            'status' => $response->status(),
            'success' => $response->successful(),
            'data' => $response->json(),
            'error' => $response->failed() ? $response->body() : null
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});