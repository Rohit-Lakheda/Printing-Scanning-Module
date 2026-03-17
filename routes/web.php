<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BadgeDisplaySettingController;
use App\Http\Controllers\Admin\BadgeLayoutController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BlockedRegidController;
use App\Http\Controllers\Admin\MasterBadgeController;
use App\Http\Controllers\Admin\BypassedRegidController;
use App\Http\Controllers\Admin\ScanningReportController;
use App\Http\Controllers\Admin\PrintingReportController;
use App\Http\Controllers\Admin\UniquePrintController;
use App\Http\Controllers\Admin\EventLogoController;
use App\Http\Controllers\Admin\ImportDataController;
use App\Http\Controllers\Admin\LeadSettingsController;
use App\Http\Controllers\Admin\GetDataApiConfigurationController;
use App\Http\Controllers\Admin\ScanningTypeController;
use App\Http\Controllers\Operator\BadgeController;
use App\Http\Controllers\Operator\RegistrationController;
use App\Http\Controllers\Operator\OperatorHomeController;
use App\Http\Controllers\Operator\ScanningController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Lead\LeadScannerController;
use App\Http\Controllers\Lead\LeadPortalController;

Route::get('/', function () {
    return redirect()->route('operator.home');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Badge Display Settings
    Route::get('badge-display-settings', [BadgeDisplaySettingController::class, 'index'])->name('badge-display-settings.index');
    Route::get('badge-display-settings/create', [BadgeDisplaySettingController::class, 'create'])->name('badge-display-settings.create');
    Route::post('badge-display-settings', [BadgeDisplaySettingController::class, 'store'])->name('badge-display-settings.store');
    Route::get('badge-display-settings/{category}/edit', [BadgeDisplaySettingController::class, 'edit'])->name('badge-display-settings.edit');
    Route::put('badge-display-settings/{category}', [BadgeDisplaySettingController::class, 'update'])->name('badge-display-settings.update');

    // Badge Layout
    Route::get('badge-layout', [BadgeLayoutController::class, 'index'])->name('badge-layout.index');
    Route::get('badge-layout/{category}/edit', [BadgeLayoutController::class, 'edit'])->name('badge-layout.edit');
    Route::put('badge-layout/{category}', [BadgeLayoutController::class, 'update'])->name('badge-layout.update');

    // Unique Print Settings
    Route::get('unique-print', [UniquePrintController::class, 'index'])->name('unique-print.index');
    Route::put('unique-print', [UniquePrintController::class, 'update'])->name('unique-print.update');

    // Event Logo
    Route::get('event-logo', [EventLogoController::class, 'index'])->name('event-logo.index');
    Route::post('event-logo/upload', [EventLogoController::class, 'upload'])->name('event-logo.upload');
    Route::delete('event-logo', [EventLogoController::class, 'delete'])->name('event-logo.delete');

    // Import Data (Excel)
    Route::get('import-data', [ImportDataController::class, 'index'])->name('import-data.index');
    Route::get('import-data/template', [ImportDataController::class, 'downloadTemplate'])->name('import-data.template');
    Route::post('import-data/import', [ImportDataController::class, 'import'])->name('import-data.import');

    // Locations
    Route::resource('locations', LocationController::class);
    Route::get('scanning/type', [ScanningTypeController::class, 'edit'])->name('scanning.type.edit');
    Route::put('scanning/type', [ScanningTypeController::class, 'update'])->name('scanning.type.update');

    // Blocked RegIDs
    Route::resource('blocked-regids', BlockedRegidController::class);

    // Master RegIDs
    Route::resource('master-regids', MasterBadgeController::class);

    // Bypassed RegIDs
    Route::resource('bypassed-regids', BypassedRegidController::class);

    // Reports
    Route::get('reports/scanning/download', [ScanningReportController::class, 'download'])->name('reports.scanning.download');
    Route::get('reports/printing/download', [PrintingReportController::class, 'download'])->name('reports.printing.download');

    // API Configurations
    Route::resource('api-configurations', \App\Http\Controllers\Admin\ApiConfigurationController::class);
    Route::resource('get-data-api-configurations', GetDataApiConfigurationController::class)
        ->parameters(['get-data-api-configurations' => 'getDataApiConfiguration']);

    // Lead / QR settings and credential mailing
    Route::get('leads/settings', [LeadSettingsController::class, 'index'])->name('leads.settings');
    Route::get('leads/share', [LeadSettingsController::class, 'shareIndex'])->name('leads.share');
    Route::post('leads/mail-config', [LeadSettingsController::class, 'saveMailConfig'])->name('leads.mail-config.save');
    Route::post('leads/send-credentials', [LeadSettingsController::class, 'generateAndSendCredentials'])->name('leads.send-credentials');
    Route::post('leads/share-settings', [LeadSettingsController::class, 'saveLeadShareSettings'])->name('leads.share-settings.save');
});

// API Route for getting visible fields
Route::get('api/category/{category}/visible-fields', function($category) {
    $displaySettings = \App\Models\BadgeDisplaySetting::where('Category', $category)
        ->where('layout_type', 'normal')
        ->first();
    if (!$displaySettings) {
        return response()->json([]);
    }
    
    $visibleFields = [];
    $fields = ['RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company', 
              'Country', 'State', 'City', 'Additional1', 'Additional2', 
              'Additional3', 'Additional4', 'Additional5'];
    
    foreach($fields as $field) {
        if($displaySettings->$field) {
            $visibleFields[] = $field;
        }
    }
    
    return response()->json($visibleFields);
})->name('api.visible-fields');

// Operator Routes
Route::prefix('operator')->name('operator.')->group(function () {
    // Home
    Route::get('home', [OperatorHomeController::class, 'index'])->name('home');

    // Badge menu + Search and Print
    Route::get('badge', [BadgeController::class, 'menu'])->name('badge.menu');
    Route::get('badge/scan-print', [BadgeController::class, 'scanPrint'])->name('badge.scan-print');
    Route::get('badge/search-print', [BadgeController::class, 'searchPrint'])->name('badge.search-print');
    Route::post('badge/print', [BadgeController::class, 'print'])->name('badge.print');
    Route::get('badge/print', [BadgeController::class, 'print'])->name('badge.print');
    Route::get('badge/bulk', [BadgeController::class, 'bulkForm'])->name('badge.bulk.form');
    Route::post('badge/bulk', [BadgeController::class, 'bulkPrint'])->name('badge.bulk.print');

    // Onsite Registration
    Route::get('registration', [RegistrationController::class, 'create'])->name('registration.create');
    Route::post('registration', [RegistrationController::class, 'store'])->name('registration.store');
    Route::get('registration/receipt', [RegistrationController::class, 'receipt'])->name('registration.receipt');
    Route::post('registration/receipt', [RegistrationController::class, 'storeReceipt'])->name('registration.store-receipt');
    Route::post('registration/cancel', [RegistrationController::class, 'cancel'])->name('registration.cancel');

    // Scanning
    Route::get('scanning/select-location', [ScanningController::class, 'selectLocation'])->name('scanning.select-location');
    Route::post('scanning/store-location', [ScanningController::class, 'storeLocation'])->name('scanning.store-location');
    Route::get('scanning/scan', [ScanningController::class, 'scan'])->name('scanning.scan');
    Route::post('scanning/check-user', [ScanningController::class, 'checkUser'])->name('scanning.check-user');
    Route::get('scanning/clear-location', [ScanningController::class, 'clearLocation'])->name('scanning.clear-location');
});

// Lead / QR-based lead generation scanner (separate from operator scanning)
Route::prefix('lead')->name('lead.')->group(function () {
    // Portal login + protected area
    Route::get('login', [LeadPortalController::class, 'showLoginForm'])->name('login.form');
    Route::post('login', [LeadPortalController::class, 'login'])->name('login');
    Route::get('forgot-password', [LeadPortalController::class, 'showForgotPasswordForm'])->name('password.forgot.form');
    Route::post('forgot-password', [LeadPortalController::class, 'sendPasswordResetLink'])->name('password.forgot.send');
    Route::get('reset-password/{token}', [LeadPortalController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('reset-password', [LeadPortalController::class, 'resetPassword'])->name('password.reset');
    Route::post('change-password', [LeadPortalController::class, 'changePassword'])->name('password.change');
    Route::post('logout', [LeadPortalController::class, 'logout'])->name('logout');

    Route::get('portal', [LeadPortalController::class, 'portal'])->name('portal');
    Route::get('download', [LeadPortalController::class, 'downloadScans'])->name('download');

    // API endpoint for storing scans (used by portal scanner)
    Route::post('scan/precheck', [LeadScannerController::class, 'precheckScan'])->name('scan.precheck');
    Route::post('scan', [LeadScannerController::class, 'storeScan'])->name('scan.store');
});
