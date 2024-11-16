<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Auth\{
    LoginController,
    ForgotPasswordController,
    ResetPasswordController,
};
use App\Http\Controllers\Backend\LanguageController;

Route::get('/', function () {
    return to_route('login');
});

//Clear Cache facade value:
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return '<h1>Optimize cleared</h1>';
});
Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return '<h1>All view cleared</h1>';
});
Route::get('/config-clear', function () {
    Artisan::call('config:clear');
    return '<h1>All config cleared</h1>';
});
Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    return '<h1>All cache cleared</h1>';
});

// Authentication Routes
Route::group(['middleware' => ['PreventBackHistory', 'guest'], 'prefix' => 'admin'], function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showAdminLogin')->name('login');
        Route::post('login', 'login')->name('admin.authenticate');
    });

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forgot-password', 'showAdminForgetPassword')->name('admin.forgot.password');
        Route::post('forgot-pass-mail', 'sendResetLinkEmail')->name('admin.password_mail_link');
    });

    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'showAdminResetPassword')->name('admin.resetPassword');
        Route::post('reset-password', 'reset')->name('admin.reset-new-password');
    });
});

Route::middleware(['auth', 'PreventBackHistory', 'userinactive'])->group(function () {
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'App\Http\Controllers\Backend'], function () {

        Route::get('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('get-society-building-unit-list', 'HomeController@getSocietyBuildingUnits')->name('get-society-building-unit-options');

        Route::get('profile', 'ProfileController@showProfile')->name('show.profile');
        Route::post('profile', 'ProfileController@updateProfile')->name('update.profile');

        Route::post('change-password', 'ProfileController@updateChangePassword')->name('update.change.password');

        Route::get('settings', 'SettingController@index')->name('show.setting');
        Route::post('update-settings', 'SettingController@update')->name('update.setting');

        // Users
        Route::post('users/change-status', "UserController@changeStatus")->name('user.status');
        Route::get('users/all', 'UserController@getAllRoles')->name('roles.all');

        Route::resource('users', 'UserController');

        // Roles and Permission   
        Route::resource('roles', 'RoleController'); // roles       
        Route::resource('permissions', 'PermissionController'); // permissions

        Route::get('locations/create-location-slug', 'LocationController@createLocationSlug')->name('createLocationSlug');
        Route::resource('locations', 'LocationController');

        /* For Services */
        Route::get('services/create-service-slug', 'ServiceController@createServiceSlug')->name('createServiceSlug');
        Route::resource('services', 'ServiceController');

        // Service Bookings
        Route::post('service-bookings/service-status-update', 'ServiceBookingController@statusChange')->name('statusChange');
        Route::resource('service-bookings', 'ServiceBookingController');

        Route::get('societies/all', 'SocietyController@getAllSocieties')->name('societies.all');
        Route::get('societies/districts-by-city', 'SocietyController@getDistrictsByCity')->name('getDistricts');
        Route::resource('societies', 'SocietyController');
        Route::resource('buildings', 'BuildingController');
        Route::resource('units', 'UnitController');
        Route::resource('guards', 'GuardController');

        /* For Residents */
        Route::get('residents/filter-user-resident', 'ResidentController@filterResidentData')->name('filterResident');
        Route::post('residents/verified-user-resident', 'ResidentController@isVerified')->name('verifiedUser');
        Route::resource('residents', 'ResidentController');

        /* For Posts */
        Route::get('posts/create-post-slug', 'PostController@createPostSlug')->name('createPostSlug');
        Route::post('post/post-status-update', 'PostController@postStatusChange')->name('postStatusChange');
        Route::get('posts/comment-detail/{uuid}', 'PostController@postCommentDetail')->name('postCommentDetail');
        Route::resource('posts', 'PostController');

        //  Comments
        Route::post('comments/change-status', "CommentController@changeStatus")->name('comments.status');
        Route::resource('comments', 'CommentController');

        // complaints
        Route::post('complaints/complaint-status-update', 'ComplaintController@statusChange')->name('complaintStatusChange');
        Route::post('complaints/view-images/{id}', 'ComplaintController@viewImage')->name('complaint.viewImage');
        Route::resource('complaints', 'ComplaintController');

        //complaint-types
        Route::get('complaint-types/create-complaint-slug',  'ComplaintTypeController@createComplaintSlug')->name('createComplaintSlug');
        Route::resource('complaint-types', 'ComplaintTypeController');

        Route::resource('faqs', 'FaqController');
        Route::resource('supports', 'SupportController');

        Route::post('visitors/visitor-status-update', 'VisitorController@visitorStatusChange')->name('visitorStatusChange');
        Route::resource('visitors', 'VisitorController');

        Route::post('amenity-bookings/amenity-status-update', 'AmenityController@amenityStatusChange')->name('amenityStatusChange');
        Route::get('amenity/bookings', 'AmenityController@amenityBookings')->name('amenity.booking.index');
        Route::resource('amenities', 'AmenityController');

        Route::get('payment-methods/create-payment-method-slug', 'PaymentMethodController@createPaymentMethodSlug')->name('createPaymentMethodSlug');
        Route::resource('payment-methods', 'PaymentMethodController');

        // For Provider
        Route::post('providers/verified-user-provider', 'ProviderController@isVerified')->name('verifiedUserProvider');
        Route::resource('providers', 'ProviderController');

        Route::post('announcements/view-images/{id}', 'AnnouncementController@viewImage')->name('announcements.viewImage');
        Route::resource('announcements', 'AnnouncementController');

        Route::get('transaction-reports', 'TransactionController@transactionReport')->name('transaction-reports');
        Route::get('transactions', 'TransactionController@index')->name('transactions.index');

        Route::resource('payment-requests', 'PaymentRequestController');
        Route::resource('features', 'FeatureController');
        Route::resource('prpoertyTypes', 'PropertyTypeController');
        Route::resource('categories', 'CategoryController');
        Route::resource('maintenance-items', 'MaintenanceItemController');

        Route::get('preapproved-resident-vehicles', 'ResidentVehicleController@preApprovedVehicle')->name('preApprovedVehicle');
        Route::post('resident-vehicles/status-update', 'ResidentVehicleController@vehicleStatusChange')->name('vehicleStatusChange');
        Route::get('resident-vehicles/filter-location', 'ResidentVehicleController@filterLocation')->name('filterLocation');

        Route::resource('resident-vehicles', 'ResidentVehicleController');
        Route::resource('resident-daily-helps', 'ResidentDailyHelpController');
        Route::resource('maintenance-plans', 'MaintenancePlanController');

        Route::get('property-managements-reports', 'PropertyManagementController@reports')->name('reports');
        Route::get('property-managements-filter', 'PropertyManagementController@filterBySociety')->name('filterBySociety');
        Route::post('property-managements-reports/view-images/{id}', 'PropertyManagementController@viewImage')->name('property-managements.viewImage');
        Route::resource('property-managements', 'PropertyManagementController');
        Route::resource('delivery-types', 'DeliveryTypeController');

        Route::get('delivery-managements/user-role', 'DeliveryManagementController@getNotifyUserRole')->name('getNotifyUserRole');
        Route::resource('delivery-managements', 'DeliveryManagementController');

        Route::resource('messages', 'AdminMessageController');

        Route::resource('cameras', 'CameraController');
        Route::get('notifications', 'NotificationController@index')->name('notifications');
        Route::post('notifications/read-notification', 'NotificationController@markAsReadNotification')->name('read.notifications');
        Route::post('notifications/read-all-notification', 'NotificationController@markAllReadNotification')->name('read.allNotifications');
        Route::post('notifications/status-update', 'NotificationController@updateStatus')->name('updateStatus');

        Route::resource('service-categories', 'ServiceCategoryController');

        Route::get('ai-box-notifications', 'AiBoxNotificationController@index')->name('aibox.index');
        Route::get('ai-box-notifications/view-details/{id}', 'AiBoxNotificationController@show')->name('aibox.show');
        Route::post('ai-box-notifications/view-images/{id}', 'AiBoxNotificationController@viewImage')->name('aibox.viewImage');
        Route::post('ai-box-notifications/view-videos/{id}', 'AiBoxNotificationController@viewVideo')->name('aibox.viewVideo');
    });
    Route::get('switch-language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');
});
