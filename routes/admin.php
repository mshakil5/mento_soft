<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\SoftCodeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ContactMailController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\ClientReviewController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientProjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\InvoiceController;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin']], function () {

    Route::get('/toggle-sidebar', [HomeController::class, 'toggleSidebar'])->name('toggle.sidebar');

    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetails');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    Route::get('/about-us', [CompanyDetailsController::class, 'aboutUs'])->name('admin.aboutUs');
    Route::post('/about-us', [CompanyDetailsController::class, 'aboutUsUpdate'])->name('admin.aboutUs');

    Route::get('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicy'])->name('admin.privacy-policy');
    Route::post('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicyUpdate'])->name('admin.privacy-policy');

    Route::get('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditions'])->name('admin.terms-and-conditions');
    Route::post('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditionsUpdate'])->name('admin.terms-and-conditions');

    Route::get('/faq-questions', [FAQController::class, 'index'])->name('allFaq');    
    Route::post('/faq-questions', [FAQController::class, 'store']);
    Route::get('/faq-questions/{id}/edit', [FAQController::class, 'edit']);
    Route::post('/faq-questions-update', [FAQController::class, 'update']);
    Route::get('/faq-questions/{id}', [FAQController::class, 'delete']);

    Route::get('/soft-code', [SoftCodeController::class, 'index'])->name('allSoftCode');
    Route::post('/soft-code', [SoftCodeController::class, 'store']);
    Route::get('/soft-code/{id}/edit', [SoftCodeController::class, 'edit']);
    Route::post('/soft-code-update', [SoftCodeController::class, 'update']);
    Route::get('/soft-code/{id}', [SoftCodeController::class, 'delete']);

    Route::get('/master', [MasterController::class, 'index'])->name('allMaster');
    Route::post('/master', [MasterController::class, 'store']);
    Route::get('/master/{id}/edit', [MasterController::class, 'edit']);
    Route::post('/master-update', [MasterController::class, 'update']);
    Route::get('/master/{id}', [MasterController::class, 'delete']);

    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::get('/slider/{id}', [SliderController::class, 'sliderDelete']);
    Route::post('/slider-status', [SliderController::class, 'toggleStatus']);

    Route::get('/contact-email', [ContactMailController::class, 'getContactEmail'])->name('allcontactemail');
    Route::post('/contact-email', [ContactMailController::class, 'contactEmailStore']);
    Route::get('/contact-email/{id}/edit', [ContactMailController::class, 'contactEmailEdit']);
    Route::post('/contact-email-update', [ContactMailController::class, 'contactEmailUpdate']);
    Route::get('/contact-email/{id}', [ContactMailController::class, 'contactEmailDelete']);

    Route::get('/service', [ServiceController::class, 'getService'])->name('allservice');
    Route::post('/service', [ServiceController::class, 'serviceStore']);
    Route::get('/service/{id}/edit', [ServiceController::class, 'serviceEdit']);
    Route::post('/service-update', [ServiceController::class, 'serviceUpdate']);
    Route::get('/service/{id}', [ServiceController::class, 'serviceDelete']);
    Route::post('/service-status', [ServiceController::class, 'toggleStatus']);

    // Projects Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit']);
    Route::post('/projects/update', [ProjectController::class, 'update']);
    Route::get('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::post('/projects/status', [ProjectController::class, 'toggleStatus'])->name('projects.status');
    Route::post('/projects/featured', [ProjectController::class, 'toggleFeatured'])->name('projects.featured');

    // Client Reviews Routes
    Route::get('/client-reviews', [ClientReviewController::class, 'index'])->name('client-reviews.index');
    Route::post('/client-reviews', [ClientReviewController::class, 'store']);
    Route::get('/client-reviews/{id}/edit', [ClientReviewController::class, 'edit']);
    Route::post('/client-reviews/update', [ClientReviewController::class, 'update']);
    Route::get('/client-reviews/{id}', [ClientReviewController::class, 'destroy']);
    Route::post('/client-reviews/status', [ClientReviewController::class, 'toggleStatus'])->name('client-reviews.status');

    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::get('/contacts/{id}/delete', [ContactController::class, 'destroy']);
    Route::post('/contacts/status', [ContactController::class, 'toggleStatus'])->name('contacts.status');

    Route::get('/client-types', [ClientTypeController::class, 'index'])->name('client-types.index');
    Route::post('/client-types', [ClientTypeController::class, 'store']);
    Route::get('/client-types/{id}/edit', [ClientTypeController::class, 'edit']);
    Route::post('/client-types/update', [ClientTypeController::class, 'update']);
    Route::get('/client-types/{id}', [ClientTypeController::class, 'destroy']);

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store']);
    Route::get('/clients/{id}/edit', [ClientController::class, 'edit']);
    Route::post('/clients/update', [ClientController::class, 'update']);
    Route::get('/clients/{id}', [ClientController::class, 'destroy']);
    Route::post('/clients/status', [ClientController::class, 'toggleStatus'])->name('clients.status');

    Route::get('/client-projects', [ClientProjectController::class, 'index'])->name('client-projects.index');
    Route::post('/client-projects', [ClientProjectController::class, 'store']);
    Route::get('/client-projects/{id}/edit', [ClientProjectController::class, 'edit']);
    Route::post('/client-projects/update', [ClientProjectController::class, 'update']);
    Route::get('/client-projects/{id}', [ClientProjectController::class, 'destroy']);
    Route::post('/client-projects/status', [ClientProjectController::class, 'toggleStatus'])->name('client-projects.status');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
    Route::post('/employees/update', [EmployeeController::class, 'update']);
    Route::get('/employees/{id}', [EmployeeController::class, 'destroy']);
    Route::post('/employees/status', [EmployeeController::class, 'toggleStatus']);

    Route::get('/project-tasks/{project_id}', [ProjectTaskController::class, 'getByProject'])->name('project-tasks.by-project');
    Route::post('/project-tasks/store', [ProjectTaskController::class, 'storeTask'])->name('project-tasks.store');
    Route::post('/project-tasks/update', [ProjectTaskController::class, 'updateTask'])->name('project-tasks.update');
    Route::delete('/project-tasks/{task}', [ProjectTaskController::class, 'deleteTask'])->name('project-tasks.destroy');
    Route::post('/project-tasks/toggle-status', [ProjectTaskController::class, 'toggleStatus'])->name('project-tasks.toggle-status');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::get('/invoices/create', [InvoiceController::class, 'create']);
    Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit']);
    Route::post('/invoices/update', [InvoiceController::class, 'update']);
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);
    Route::get('/invoices/show/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/client-info/{id}', [InvoiceController::class, 'getClientInfo']);
    Route::get('/invoices/client-projects/{id}', [InvoiceController::class, 'getClientProjects']);
    Route::get('/invoices/project-info/{id}', [InvoiceController::class, 'getProjectInfo']);
    Route::post('/invoices/send-email/{id}', [InvoiceController::class, 'sendEmail'])->name('invoices.send.email');

});