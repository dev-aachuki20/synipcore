<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserApp\{
	VisitorsController,
	ComplaintsController,
	AnnouncementController,
	SocietyPaymentController,
	ActivityController,
	PostController,
	ConversationController,
	ServicesController,
	SettingController,
	ResidentVehicleController,
	ResidentFamilyMemberController,
	ResidentFrequestEntryController,
	ResidentDailyHelpController,
	AmenityController,
	ResidentSecurityAlertController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api'], function () {

	Route::post('register', [AuthController::class, 'register']);

	Route::post('login', [AuthController::class, 'login']);

	Route::post('society-details', [AuthController::class, 'getSocietyDetails']);

	Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

	Route::post('password/verify-otp', [AuthController::class, 'verifyOtp']);

	Route::post('password/reset-password', [AuthController::class, 'resetPassword']);

	Route::get('get-languages', [HomeController::class, 'languageList']);

	Route::get('get-locations', [HomeController::class, 'locationList']);

	Route::get('get-districts/{city_id}', [HomeController::class, 'districtList']);

	Route::get('get-societies/{district_id?}', [HomeController::class, 'societyList']);

	Route::get('get-buildings/{society_id}', [HomeController::class, 'buildingList']);

	Route::get('get-units/{society_id}/{building_id}', [HomeController::class, 'unitList']);

	Route::post('guard-login', [AuthController::class, 'guardLogin']);

	Route::get('get-society-admin/{society_id}', [HomeController::class, 'getSocietyAdmin']);

	// AI box Api // fall detection uri change by client
	Route::post('ai-box/event-notification', [HomeController::class, 'aiBoxFallDetection']);

	Route::get('change-logo', [HomeController::class, 'changeLogo']);
});


/*
| Auth Common Api List
|--------------------------------------------------------------------------
| Base Route : http://localhost:8000/api
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['auth:sanctum', 'checkUserStatus']], function () {
	Route::post('logout', [HomeController::class, 'logout']);

	Route::get('home', [HomeController::class, 'index']);

	Route::get('profile', [HomeController::class, 'profile']);
	Route::post('profile', [HomeController::class, 'updateProfile']);
	Route::post('profile/update-language', [HomeController::class, 'updateUserLanguage']);

	// visitors
	Route::post('allow-visitor', [VisitorsController::class, 'allowVisitor']);
	Route::post('get-gatepass/', [HomeController::class, 'getGatepass']);

	// complaints
	Route::get('complaints', [ComplaintsController::class, 'index']);
	Route::get('create-complaint', [ComplaintsController::class, 'create']);
	Route::post('store-complaint', [ComplaintsController::class, 'store']);
	Route::get('complaint-detail/{complaint_id}', [ComplaintsController::class, 'complaintDetail']);
	Route::post('resolve-complaint', [ComplaintsController::class, 'resolveComplaint']);
	Route::post('complaints/send-comment', [ComplaintsController::class, 'sendComment']);

	// notice board
	Route::get('announcements', [AnnouncementController::class, 'index']);
	Route::post('announcements/send-comment', [AnnouncementController::class, 'sendComment']);
	Route::post('announcements/reaction', [AnnouncementController::class, 'sendReaction']);
	Route::post('announcements/poll-vote', [AnnouncementController::class, 'addPollVote']);
	Route::get('announcements/{id}/comments', [AnnouncementController::class, 'getAnnouncementComments']);

	// Payment dues
	Route::get('payments', [SocietyPaymentController::class, 'index']);
	Route::post('payments/transaction', [SocietyPaymentController::class, 'paymentTransaction']);
	Route::get('payments/due', [SocietyPaymentController::class, 'paymentsDue']);


	// Amenities
	Route::get('amenities', [AmenityController::class, 'index']);
	Route::get('amenities/detail/{amenity_id}', [AmenityController::class, 'amenityDetail']);
	Route::post('amenities/book', [AmenityController::class, 'amenityBook']);
	Route::post('amenities/transaction', [AmenityController::class, 'paymentTransaction']);
	Route::get('booked-amenities/', [AmenityController::class, 'bookedAmenities']);

	// activities
	Route::get('activities/today', [ActivityController::class, 'todayActivities']);
	Route::get('activities/upcoming', [ActivityController::class, 'upcomingActivities']);
	Route::post('activities/delete', [ActivityController::class, 'deleteActivity']);

	// Posts
	Route::get('posts', [PostController::class, 'index']);
	Route::post('posts/store', [PostController::class, 'store']);
	Route::get('posts/edit/{id}', [PostController::class, 'edit']);
	Route::post('posts/update/{id}', [PostController::class, 'update']);
	Route::delete('posts/delete/{id}', [PostController::class, 'destroy']);
	Route::post('posts/send-comment', [PostController::class, 'sendComment']);
	Route::post('posts/like', [PostController::class, 'likePost']);
	Route::post('posts/save', [PostController::class, 'savePost']);
	Route::get('post-detail/{post_id}', [PostController::class, 'postDetail']);
	Route::get('posts/{id}/comments', [PostController::class, 'getPostComments']);


	// Conversations
	Route::get('conversations', [ConversationController::class, 'index']);
	Route::post('block-conversation', [ConversationController::class, 'blockConversation']);
	Route::delete('delete-conversation/{conversation_id}', [ConversationController::class, 'deleteConversation']);
	Route::post('conversations/messages/', [ConversationController::class, 'singleConversationMessages']);
	Route::post('conversations/send-message/', [ConversationController::class, 'SendMessage']);
	Route::delete('conversations/delete-message/{message_id}', [ConversationController::class, 'deleteMessage']);

	// Resident List
	Route::get('social/residents', [PostController::class, 'residentsList']);

	// Services
	Route::get('services', [ServicesController::class, 'index']);
	Route::get('services/{id}/', [ServicesController::class, 'serviceDetail']);
	Route::post('service-booking/', [ServicesController::class, 'serviceBooking']);
	Route::get('booked-services/', [ServicesController::class, 'bookedServices']);

	// For resident api
	Route::group(['prefix' => 'residents'], function () {
		// Resident's Family Members
		Route::resource('family-members', ResidentFamilyMemberController::class);

		// Resident's Daily Helps
		Route::resource('daily-helps', ResidentDailyHelpController::class);

		// Resident's Vehicles
		Route::resource('vehicles', ResidentVehicleController::class);

		// Resident's Frequest Entries
		Route::resource('frequest-entries', ResidentFrequestEntryController::class);

		// Security Alert
		Route::resource('security-alerts', ResidentSecurityAlertController::class);
	});

	// Notification apis
	Route::get('notifications', [HomeController::class, 'userNotifications']);
	Route::post('notifications/read-notification', [HomeController::class, 'readNotification']);

	Route::post('send-message-to-guard', [HomeController::class, 'sendMessageToGuard']);
	Route::post('send-message-to-admin', [HomeController::class, 'sendMessageToAdmin']);


	Route::post('security-alert', [HomeController::class, 'securityAlert']);

	// Accept or Decline Visitor that on Gate
	Route::post('accept-decline-visitor', [HomeController::class, 'changeVisitorStatus']);

	// Settings
	Route::group(['prefix' => 'settings'], function () {
		// Current User's Post
		Route::get('user-posts/', [SettingController::class, 'currentUserPosts']);

		// Current user's household details Like: Family Mambers, Daily helps, vehicles, frequest entries
		Route::get('household/', [SettingController::class, 'household']);

		// Send Mail to support
		Route::post('get-support', [HomeController::class, 'getSupport']);

		// Get Faqs
		Route::get('faqs', [HomeController::class, 'faqList']);

		// Terms And Condition
		Route::get('term-condition', [HomeController::class, 'termsCondition']);
	});

	// Delevery Management Status Update
	Route::post('update-delivery-management-status', [HomeController::class, 'updateDeliveryManagementStatus']);


	// guard Apis
	Route::group(['prefix' => 'guard', 'namespace' => 'App\Http\Controllers\Api\GuardApp'], function () {
		Route::post('pre-approved-visitor', 'VisitorsController@preApprovedVisitor');

		Route::get('in-visitor', 'VisitorsController@inVisitorList');
		Route::get('waiting-visitor', 'VisitorsController@waitingVisitorList');
		Route::get('out-visitor', 'VisitorsController@outVisitorList');
		Route::post('add-visitor', 'VisitorsController@addVisitor');
		Route::post('in-out-visitor-status', 'VisitorsController@inOutVisitorStatus');
		Route::post('allow-visitor-by-guard', 'VisitorsController@allowDeclineVisitorByGuard');
		Route::delete('delete-visitor-by-guard/{visitor_id}', 'VisitorsController@deleteVisitorByGuard');


		Route::get('recent-messages', 'HomeController@recentMessages');

		Route::get('check-visitor-status/{visitor_id}', 'VisitorsController@checkVisitorStatusByGuard');
	});
});
