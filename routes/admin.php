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
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LiabilityController;
use App\Http\Controllers\EquityController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\EquityHolderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFeatureController;
use App\Http\Controllers\ProductClientController;
use App\Http\Controllers\ProductFaqController;
use App\Http\Controllers\ProductClientVideoController;
use App\Http\Controllers\ProjectRecentUpdateController;
use App\Http\Controllers\ProjectServiceController;
use App\Http\Controllers\ProjectServiceDetailController;
use App\Http\Controllers\DaybookController;
use App\Http\Controllers\FinancialStatementController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TransactionsController;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin']], function () {
// Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin', 'role:admin']], function () {

    Route::get('/toggle-sidebar', [HomeController::class, 'toggleSidebar'])->name('toggle.sidebar');

    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    //Frontend Part

    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetails');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    Route::get('/about-us', [CompanyDetailsController::class, 'aboutUs'])->name('admin.aboutUs');
    Route::post('/about-us', [CompanyDetailsController::class, 'aboutUsUpdate'])->name('admin.aboutUs');

    Route::get('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicy'])->name('admin.privacy-policy');
    Route::post('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicyUpdate'])->name('admin.privacy-policy');

    Route::get('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditions'])->name('admin.terms-and-conditions');
    Route::post('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditionsUpdate'])->name('admin.terms-and-conditions');

    Route::get('/mail-footer', [CompanyDetailsController::class, 'mailFooter'])->name('admin.mail-footer');
    Route::post('/mail-footer', [CompanyDetailsController::class, 'mailFooterUpdate'])->name('admin.mail-footer');

    Route::get('/company/seo-meta', [CompanyDetailsController::class, 'seoMeta'])->name('admin.company.seo-meta');
    Route::post('/company/seo-meta/update', [CompanyDetailsController::class, 'seoMetaUpdate'])->name('admin.company.seo-meta.update');

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

    // Project Types
    Route::get('/project-types', [ProjectTypeController::class, 'index'])->name('project-types.index');
    Route::post('/project-types', [ProjectTypeController::class, 'store']);
    Route::get('/project-types/{id}/edit', [ProjectTypeController::class, 'edit']);
    Route::post('/project-types/update', [ProjectTypeController::class, 'update']);
    Route::get('/project-types/{id}', [ProjectTypeController::class, 'destroy']);
    Route::post('/project-types/status', [ProjectTypeController::class, 'toggleStatus'])->name('project-types.status');

    // Projects Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit']);
    Route::post('/projects/update', [ProjectController::class, 'update']);
    Route::get('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::post('/projects/status', [ProjectController::class, 'toggleStatus'])->name('projects.status');
    Route::post('/projects/featured', [ProjectController::class, 'toggleFeatured'])->name('projects.featured');
    Route::delete('/projects/sliders/{id}', [ProjectController::class, 'destroySlider'])->name('projects.sliders.destroy');

    // Client Reviews Routes
    Route::get('/client-reviews', [ClientReviewController::class, 'index'])->name('client-reviews.index');
    Route::post('/client-reviews', [ClientReviewController::class, 'store']);
    Route::get('/client-reviews/{id}/edit', [ClientReviewController::class, 'edit']);
    Route::post('/client-reviews/update', [ClientReviewController::class, 'update']);
    Route::get('/client-reviews/{id}', [ClientReviewController::class, 'destroy']);
    Route::post('/client-reviews/status', [ClientReviewController::class, 'toggleStatus'])->name('client-reviews.status');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::get('/contacts/{id}/delete', [ContactController::class, 'destroy']);
    Route::post('/contacts/status', [ContactController::class, 'toggleStatus'])->name('contacts.status');

    //Quotation
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/{id}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::delete('/quotations/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::post('/quotations/status', [QuotationController::class, 'toggleStatus'])->name('quotations.status');

    // Product 
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
    Route::post('/products/update', [ProductController::class, 'update']);
    Route::get('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/status', [ProductController::class, 'toggleStatus'])->name('products.status');

    // Product Features
    Route::get('/products/{product}/features', [ProductFeatureController::class, 'index'])->name('products.features.index');
    Route::post('/products/features', [ProductFeatureController::class, 'store']);
    Route::get('/products/features/{id}/edit', [ProductFeatureController::class, 'edit']);
    Route::post('/products/features/update', [ProductFeatureController::class, 'update']);
    Route::get('/products/features/{id}', [ProductFeatureController::class, 'destroy']);
    Route::post('/products/features/status', [ProductFeatureController::class, 'toggleStatus'])->name('products.features.status');

    // Product Clients Routes
    Route::get('/products/{product}/clients', [ProductClientController::class, 'index'])->name('products.clients.index');
    Route::post('/products/clients', [ProductClientController::class, 'store']);
    Route::get('/products/clients/{id}', [ProductClientController::class, 'destroy']);
    Route::post('/products/clients/status', [ProductClientController::class, 'toggleStatus'])->name('products.clients.status');

    // Product FAQs
    Route::get('/products/{product}/faqs', [ProductFaqController::class, 'index'])->name('products.faqs.index');
    Route::post('/products/faqs', [ProductFaqController::class, 'store']);
    Route::get('/products/faqs/{id}/edit', [ProductFaqController::class, 'edit']);
    Route::post('/products/faqs/update', [ProductFaqController::class, 'update']);
    Route::get('/products/faqs/{id}', [ProductFaqController::class, 'destroy']);
    Route::post('/products/faqs/status', [ProductFaqController::class, 'toggleStatus'])->name('products.faqs.status');

    // Product client video
    Route::get('/products/{product}/client-videos', [ProductClientVideoController::class, 'index'])->name('products.clients.video');
    Route::post('/products/client-videos', [ProductClientVideoController::class, 'store']);
    Route::get('/products/client-videos/{id}/edit', [ProductClientVideoController::class, 'edit']);
    Route::post('/products/client-videos/update', [ProductClientVideoController::class, 'update']);
    Route::get('/products/client-videos/{id}', [ProductClientVideoController::class, 'destroy']);
    Route::post('/products/client-videos/status', [ProductClientVideoController::class, 'toggleStatus'])->name('products.client-videos.status');

    // Team Members
    Route::get('/team-members', [TeamMemberController::class, 'index'])->name('team-members.index');
    Route::post('/team-members', [TeamMemberController::class, 'store']);
    Route::get('/team-members/{id}/edit', [TeamMemberController::class, 'edit']);
    Route::post('/team-members/update', [TeamMemberController::class, 'update']);
    Route::get('/team-members/{id}', [TeamMemberController::class, 'destroy']);
    Route::post('/team-members/status', [TeamMemberController::class, 'toggleStatus'])->name('team-members.status');

    Route::post('/remove-file', [FileController::class, 'removeFile'])->name('remove.file');
    
    //Software Part

    // Client Types
    Route::get('/client-types', [ClientTypeController::class, 'index'])->name('client-types.index');
    Route::post('/client-types', [ClientTypeController::class, 'store']);
    Route::get('/client-types/{id}/edit', [ClientTypeController::class, 'edit']);
    Route::post('/client-types/update', [ClientTypeController::class, 'update']);
    Route::get('/client-types/{id}', [ClientTypeController::class, 'destroy']);

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('admin.clients.store');
    Route::get('/clients/{id}/edit', [ClientController::class, 'edit']);
    Route::post('/clients/update', [ClientController::class, 'update']);
    Route::get('/clients/{id}', [ClientController::class, 'destroy']);
    Route::post('/clients/status', [ClientController::class, 'toggleStatus'])->name('clients.status');

    Route::get('/client-mail/{id}', [ClientController::class, 'clientEmail'])->name('client.email');
    Route::post('/client-mail', [ClientController::class, 'sendClientEmail'])->name('client.email.send');

    Route::get('/clients/{client_id}/emails', [ClientController::class, 'sentEmails'])->name('client.email.logs');

    // Client Projects
    Route::get('/client-projects', [ClientProjectController::class, 'index'])->name('client-projects.index');
    Route::post('/client-projects', [ClientProjectController::class, 'store'])->name('admin.client-projects.store');
    Route::get('/client-projects/{id}/edit', [ClientProjectController::class, 'edit']);
    Route::post('/client-projects/update', [ClientProjectController::class, 'update']);
    Route::get('/client-projects/{id}', [ClientProjectController::class, 'destroy']);
    Route::post('/client-projects/status', [ClientProjectController::class, 'toggleStatus'])->name('client-projects.status');

    // Project Tasks
    Route::get('/client-projects/{project}/tasks', [ProjectTaskController::class, 'index'])->name('client-projects.tasks');
    Route::post('/client-projects/{project}/tasks', [ProjectTaskController::class, 'store']);
    Route::get('/client-projects-task/{task}/edit', [ProjectTaskController::class, 'edit']);
    Route::get('/client-projects-task/{task}/edit-page', [ProjectTaskController::class, 'editPage'])->name('client-projects-task.edit-page');
    Route::post('/client-projects-task/{task}', [ProjectTaskController::class, 'update']);
    Route::delete('/client-projects-task/{task}', [ProjectTaskController::class, 'destroy']);
    Route::post('/client-projects-task/{task}/toggle-status', [ProjectTaskController::class, 'toggleStatus']);

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    Route::get('/all-tasks', [TaskController::class, 'allTasks'])->name('tasks.all');

    Route::get('/tasks/{task}/messages', [TaskController::class, 'messages'])->name('tasks.messages');

    Route::post('/tasks/{task}/messages', [TaskController::class, 'store'])->name('tasks.messages.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Project Updates
    Route::get('/client-projects/{project}/updates', [ProjectRecentUpdateController::class, 'index'])->name('client-projects.updates');
    Route::post('/client-projects/{project}/updates', [ProjectRecentUpdateController::class, 'store'])->name('client-projects.updates.store');
    Route::get('/client-projects-update/{update}/edit', [ProjectRecentUpdateController::class, 'edit']);
    Route::post('/client-projects-update/{update}', [ProjectRecentUpdateController::class, 'update']);
    Route::delete('/client-projects-update/{update}', [ProjectRecentUpdateController::class, 'destroy']);

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
    Route::post('/employees/update', [EmployeeController::class, 'update']);
    Route::get('/employees/{id}', [EmployeeController::class, 'destroy']);
    Route::post('/employees/status', [EmployeeController::class, 'toggleStatus']);

    // Project Services
    Route::get('/service-type', [ServiceTypeController::class, 'index'])->name('service-type.index');
    Route::post('/service-type', [ServiceTypeController::class, 'store']);
    Route::get('/service-type/{service}/edit', [ServiceTypeController::class, 'edit']);
    Route::post('/service-type/{service}', [ServiceTypeController::class, 'update']);
    Route::delete('/service-type/{service}', [ServiceTypeController::class, 'destroy']);
    Route::post('/service-type/{service}/toggle-status', [ServiceTypeController::class, 'toggleStatus']);

    // Project Services
    Route::get('/project-services', [ProjectServiceController::class, 'index'])->name('project-services.index');
    Route::post('/project-services', [ProjectServiceController::class, 'store']);
    Route::get('/project-services/{service}/edit', [ProjectServiceController::class, 'edit']);
    Route::post('/project-services/{service}', [ProjectServiceController::class, 'update']);
    Route::delete('/project-services/{service}', [ProjectServiceController::class, 'destroy']);
    Route::post('/project-services/{service}/toggle-status', [ProjectServiceController::class, 'toggleStatus']);

    Route::post('/project-service/receive', [ProjectServiceController::class, 'receive'])->name('project-service.receive');
    Route::get('/clients/{client}/projects', [ProjectServiceController::class, 'projects'])->name('clients.projects');

    Route::post('/project-service/renew', [ProjectServiceController::class, 'renew'])->name('project-service.renew');
    Route::post('/project-service/advance-receive', [ProjectServiceController::class, 'advanceReceive'])->name('project-service.advance-receive');

    // Project Service Details
    Route::get('/client-project-services/{service}/details', [ProjectServiceDetailController::class, 'index'])->name('client-project-services.details');
    Route::post('/client-project-services/{service}/details', [ProjectServiceDetailController::class, 'store']);
    Route::get('/client-project-service-detail/{detail}/edit', [ProjectServiceDetailController::class, 'edit']);
    Route::post('/client-project-service-detail/{detail}', [ProjectServiceDetailController::class, 'update']);
    Route::delete('/client-project-service-detail/{detail}', [ProjectServiceDetailController::class, 'destroy']);
    Route::post('/client-project-service-detail/{detail}/toggle-status', [ProjectServiceDetailController::class, 'toggleStatus']);
    Route::post('/project-service-details/{id}/receive', [ProjectServiceDetailController::class, 'receive'])->name('project-service-details.receive');

    Route::get('project-services/invoice', [ProjectServiceController::class, 'invoice'])->name('project-services.invoice.show');
    Route::get('/project-services/send-multi-email', [ProjectServiceController::class, 'sendMultiEmail']);

    // Invoices
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
    Route::post('/invoices/{invoice}/receive', [InvoiceController::class, 'receive'])->name('invoices.receive');

    //Transactions
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');

    Route::get('/transaction-invoice/{id}', [TransactionsController::class, 'transactionInvoice'])->name('transaction.invoice');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::get('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/update', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    //Chart of account
    Route::get('chart-of-account', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount');
    Route::post('chart-of-accounts', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount.filter');
    Route::post('chart-of-account', [ChartOfAccountController::class, 'store']);
    Route::get('chart-of-account/{id}', [ChartOfAccountController::class, 'edit']);
    Route::put('chart-of-account/{id}', [ChartOfAccountController::class, 'update']);
    Route::get('chart-of-account/{id}/change-status', [ChartOfAccountController::class, 'changeStatus']);

    //Income
    Route::get('income', [IncomeController::class, 'index'])->name('admin.income');
    Route::post('incomes', [IncomeController::class, 'index'])->name('admin.income.filter');
    Route::post('income', [IncomeController::class, 'store']);
    Route::get('income/{id}', [IncomeController::class, 'edit']);
    Route::put('income/{id}', [IncomeController::class, 'update']); 

    //Expense
    Route::get('expense', [ExpenseController::class, 'index'])->name('admin.expense');
    Route::post('expenses', [ExpenseController::class, 'index'])->name('admin.expense.filter');
    Route::post('expense', [ExpenseController::class, 'store']);
    Route::get('expense/{id}', [ExpenseController::class, 'edit']);
    Route::put('expense/{id}', [ExpenseController::class, 'update']); 

    //Asset
    Route::get('asset', [AssetController::class, 'index'])->name('admin.asset');
    Route::post('assets', [AssetController::class, 'index'])->name('admin.asset.filter');
    Route::post('asset', [AssetController::class, 'store']);
    Route::get('asset/{id}', [AssetController::class, 'edit']);
    Route::put('asset/{id}', [AssetController::class, 'update']); 

    //Liability
    Route::get('liabilities', [LiabilityController::class, 'index'])->name('admin.liabilities');
    Route::post('liability', [LiabilityController::class, 'index'])->name('admin.liability.filter');
    Route::post('liabilities', [LiabilityController::class, 'store']);
    Route::get('liabilities/{id}', [LiabilityController::class, 'edit']);
    Route::put('liabilities/{id}', [LiabilityController::class, 'update']);

    //Equity
    Route::get('equity', [EquityController::class, 'index'])->name('admin.equity');
    Route::post('equities', [EquityController::class, 'index'])->name('admin.equity.filter');
    Route::post('equity', [EquityController::class, 'store']);
    Route::get('equity/{id}', [EquityController::class, 'edit']);
    Route::put('equity/{id}', [EquityController::class, 'update']);

    //Equity holders
    Route::get('equity-holders', [EquityHolderController::class, 'index'])->name('admin.equityholders');
    Route::post('equity-holders', [EquityHolderController::class, 'store']);
    Route::get('equity-holders/{id}', [EquityHolderController::class, 'edit']);
    Route::put('equity-holders/{id}', [EquityHolderController::class, 'update']);
    Route::get('equity-holders/{id}/change-status', [EquityHolderController::class, 'changeStatus']);

    Route::match(['get', 'post'], 'cash-book', [DaybookController::class, 'cashbook'])->name('cashbook');
    Route::match(['get', 'post'], 'bank-book', [DaybookController::class, 'bankbook'])->name('bankbook');

    Route::get('income-statement', [FinancialStatementController::class, 'incomeStatement'])->name('income-statement');
    Route::post('income-statement', [FinancialStatementController::class, 'incomeStatementReport'])->name('income-statement.report');

    Route::get('balance-sheet', [FinancialStatementController::class, 'balanceSheet'])->name('balance-sheet');
    Route::post('balance-sheet', [FinancialStatementController::class, 'balanceSheetReport'])->name('balance-sheet.report');

    Route::get('/clean-db', [HomeController::class, 'cleanDB']);
});