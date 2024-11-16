<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\APIController;
use App\Mail\AdminSupportMail;
use App\Models\AdminMessage;
use App\Models\AiBoxAlert;
use App\Models\Building;
use App\Models\Camera;
use App\Models\DeliveryManagement;
use App\Models\Faq;
use App\Models\GuardMessage;
use App\Models\Language;
use App\Models\Location;
use App\Models\ResidentDailyHelp;
use App\Models\ResidentFamilyMember;
use App\Models\ResidentFrequestEntry;
use App\Models\ResidentVehicle;
use App\Models\SecurityAlert;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Society;
use App\Models\Support;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorLog;
use App\Notifications\AiBoxNotification;
use App\Notifications\UserActivityNotification;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PhpParser\Node\Expr\FuncCall;

class HomeController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $featuredServices = Service::whereStatus(1)->where('is_featured', 1)
            ->whereHas('provider', function ($query) use ($user) {
                $query->where('society_id', $user->society_id);
            })->select('id', 'title')->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'service_image' => $service->service_image_url,
                    ];
                });

            $featuredProviders = User::where('society_id', $user->society_id)->whereHas('roles', function ($q) {
                $q->where('id', config('constant.roles.provider'));
            })->whereStatus(1)->where('is_featured', 1)->select('id', 'name', 'mobile_number', 'email', 'provider_url', 'description')->get()
                ->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'title' => $provider->name,
                        'profile_image' => $provider->profile_image_url,
                        'mobile_number' => $provider->mobile_number ?? '',
                        'email' => $provider->email ?? '',
                        'provider_url' => $provider->provider_url ?? '',
                        'description' => $provider->description ?? '',
                    ];
                });

            $noticeBoardCount = $user->unreadNotifications()->where('data->module', 'announcement')->count();

            $data = [
                'notification_count' => $user->unreadNotifications()->count(),
                'notice_board_count' => $noticeBoardCount,
                'featured_services' => $featuredServices,
                'featured_providers' => $featuredProviders,
            ];
            return $this->respondOk([
                'success'   => true,
                'data'   => $data,
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->current_session_id = null;
        $user->save();

        // Revoke all tokens...
        $request->user()->tokens()->delete();

        return $this->respondOk([
            'success'   => true,
            'message'   => trans('auth.messages.logout.success'),
        ]);
    }

    public function profile()
    {
        try {
            $user = Auth::user();

            $superAdmin = User::whereStatus(1)->select('id', 'name', 'mobile_number', 'email')->whereHas('roles', function ($q) {
                $q->where('id', config('constant.roles.superadmin'));
            })->first();

            $societyAdmin = $user->society->societyAdmins()->whereStatus(1)->select('id', 'name', 'mobile_number', 'email')->get();

            $guardUnreadMessageCount = $user->guardMessages()->whereNull('read_at')->count();

            return $this->respondOk([
                'status'        => true,
                'data'          => [
                    'name'              => $user->name,
                    'mobile_number'     => $user->mobile_number,
                    'email'             => $user->email,
                    'profile_image'     => $user->profile_image_url ? $user->profile_image_url : '',
                    'society_id'           => $user->society ? $user->society->id : null,
                    'building_id'       => $user->building ? $user->building->id : null,
                    'unit_id'           => $user->unit ? $user->unit->id : null,

                    'society_name'      => $user->society ? $user->society->name : '',
                    'building_name'     => $user->building ? $user->building->title : '',
                    'unit_name'         => $user->unit ? $user->unit->title : '',
                    'language_id'       => $user->language_id,
                    'language_code'     => $user->userLanguage->code ?? 'en',

                    // For Guard
                    'super_admin'       => $superAdmin,
                    'society_secretary' => $societyAdmin,

                    // For Guard Messages count
                    'guard_unread_message_count' => $guardUnreadMessageCount,
                    'notification_count' => $user->unreadNotifications()->count(),
                    'qr_code_path'      => $user->qr_code_path ?? '',
                    'security_pin'      => $user->security_pin ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => ['required', 'string', 'max:255', new NoMultipleSpacesRule],
            'mobile_number' => ['required', 'numeric', 'regex:/^[0-9]{7,15}$/', 'not_in:-', 'unique:users,mobile_number,' . $user->id . ',id,deleted_at,NULL'],
            'email' => ['required', 'string', 'unique:users,email,' . $user->id . ',id,deleted_at,NULL'],
            'profile_image'  => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ], [
            'mobile_number.regex' => 'The mobile number length must be 7 to 15 digits.',
            'mobile_number.unique' => 'The mobile number already exists.',
            'profile_image.image' => 'Please upload image.',
            'profile_image.mimes' => 'Please upload image with extentions: jpeg,png,jpg.',
            'profile_image.max' => 'The image size must equal or less than ' . config('constant.profile_max_size_in_mb'),
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('profile_image')) {
                $uploadId = null;
                $actionType = 'save';
                if ($profileImageRecord = $user->profileImage) {
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($user, $request->profile_image, 'user/profile-images', "user_profile", 'original', $actionType, $uploadId);
            }
            $user->update(['name' => $request->name, 'mobile_number' => $request->mobile_number, 'email' => $request->email]);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.profile_updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function updateUserLanguage(Request $request)
    {
        $request->validate([
            'language_id' => ['required', 'exists:languages,id']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $user->update(['language_id' => $request->language_id]);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.language_updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function languageList()
    {
        try {
            $languages = Language::whereStatus(1)->select('name', 'id')->get()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $languages
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function locationList()
    {
        try {
            $cities = Location::whereStatus(1)->where('scope_id', 3)->select('title', 'id')->get()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $cities
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function districtList($cityId)
    {
        try {
            $districts = Location::whereStatus(1)->where('parent_id', $cityId)->where('scope_id', 4)->select('title', 'id')->get()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $districts
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function societyList($districtId = '')
    {
        try {
            if (!empty($districtId) && $districtId) {
                $societies = Society::whereStatus(1)->where('district', $districtId)->select('name', 'id')->get()->toArray();
            } else {
                $societies = Society::whereStatus(1)->select('name', 'id')->get()->toArray();
            }

            return $this->respondOk([
                'status'   => true,
                'data'   => $societies
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function buildingList($societyId)
    {
        try {
            $buildings = Building::whereStatus(1)->where('society_id', $societyId)->select('title', 'id')->get()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $buildings
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function unitList($societyId, $buildingId)
    {
        try {
            $units = Unit::whereStatus(1)->where('society_id', $societyId)->where('building_id', $buildingId)->select('title', 'id')->get()->toArray();

            return $this->respondOk([
                'status'   => true,
                'data'   => $units
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function getSupport(Request $request)
    {
        $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'message' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $input = $request->all();
            $input['user_id'] = $user->id;

            $support = Support::create($input);

            // Send Mail to Support
            Mail::to(config('constant.support.email'))->send(new AdminSupportMail($support));

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.support.submit_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function faqList()
    {
        try {
            $faqs = Faq::whereStatus(1)
                ->select('id', 'title', 'short_description', 'description')
                ->get();

            return $this->respondOk([
                'status'   => true,
                'data'   => $faqs
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function getGatepass(Request $request)
    {
        $request->validate([
            'type' => ['required']
        ]);
        $type = $request->type;
        $id = $request->id;
        try {
            if ($type == 'visitor') {
                $visitor = Visitor::find($id);
                $data = [
                    'gatepass_qr_image' => $visitor->gatepass_qr_image,
                    'gatepass_code'     => $visitor->gatepass_code,
                    'name'              => $visitor->name,
                    'type'              => $visitor->visitor_type,
                    'society_name'      => $visitor->user->society->name ?? '',
                    'building_name'     => $visitor->user->building->title ?? '',
                    'unit_name'         => $visitor->user->unit->title ?? '',
                ];
            } else if ($request->type == 'daily_help') {
                $dailyHelp = ResidentDailyHelp::find($request->id);
                $data = [
                    'gatepass_qr_image' => $dailyHelp->gatepass_qr_image,
                    'gatepass_code'     => $dailyHelp->gatepass_code,
                    'name'              => $dailyHelp->name,
                    'society_name'      => $dailyHelp->society->name ?? '',
                    'building_name'     => $dailyHelp->building->title ?? '',
                    'unit_name'         => $dailyHelp->unit->title ?? '',
                ];
            } else if ($request->type == 'vehicle') {
                $vehicle = ResidentVehicle::find($request->id);
                $data = [
                    'gatepass_qr_image' => $vehicle->gatepass_qr_image,
                    'gatepass_code'     => $vehicle->gatepass_code,
                    'name'              => $vehicle->vehicle_number ?? '',
                    'society_name'      => $vehicle->society->name ?? '',
                    'building_name'     => $vehicle->building->title ?? '',
                    'unit_name'         => $vehicle->unit->title ?? '',
                ];
            } else if ($request->type == 'family_member') {
                $familyMember = ResidentFamilyMember::find($request->id);
                $data = [
                    'gatepass_qr_image' => $familyMember->gatepass_qr_image,
                    'gatepass_code'     => $familyMember->gatepass_code,
                    'name'              => $familyMember->name ?? '',
                    'society_name'      => $familyMember->resident && $familyMember->resident->society ? $familyMember->resident->society->name : '',
                    'building_name'     => $familyMember->resident && $familyMember->resident->building  ? $familyMember->resident->building->title : '',
                    'unit_name'         => $familyMember->resident && $familyMember->resident->unit  ? $familyMember->resident->unit->title : '',
                ];
            } else if ($request->type == 'frequest_entry') {
                $frequestEntry = ResidentFrequestEntry::find($request->id);
                $data = [
                    'gatepass_qr_image' => $frequestEntry->gatepass_qr_image,
                    'gatepass_code'     => $frequestEntry->gatepass_code,
                    'name'              => $frequestEntry->name ?? '',
                    'society_name'      => $frequestEntry->resident && $frequestEntry->resident->society ? $frequestEntry->resident->society->name : '',
                    'building_name'     => $frequestEntry->resident && $frequestEntry->resident->building  ? $frequestEntry->resident->building->title : '',
                    'unit_name'         => $frequestEntry->resident && $frequestEntry->resident->unit  ? $frequestEntry->resident->unit->title : '',
                ];
            } else {
                return $this->setStatusCode(422)->respondWithError('please select valid type');
            }

            return $this->respondOk([
                'status'   => true,
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function termsCondition()
    {
        try {
            $termConditionSetting = Setting::whereStatus(1)
                ->where('key', 'terms_condition')
                ->first();

            $docUrl = $termConditionSetting->doc_url ? $termConditionSetting->doc_url : asset(config('constant.default.terms_condition_pdf'));

            return $this->respondOk([
                'status'   => true,
                'data'   => $docUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function userNotifications()
    {
        try {
            $user = Auth::user();

            $user->unreadNotifications()->update(['read_at' => now()]);

            $unreadCount = $user->unreadNotifications()->count();

            $notifications = $user->notifications()->latest()->get()
                ->map(function ($notification) {
                    $notificationDate = $notification->created_at;
                    $data = $notification->data;
                    $dateInHuman = str_replace('before', 'ago', $notificationDate->diffForHumans(now()));

                    $senderName = '';
                    $senderProfile = '';
                    $isVisitorRequest = false;
                    $moduleId = '';
                    $delManageStatus = '';
                    if ($data['module'] == 'delivery_management') {
                        $senderId = '';
                        $senderName = $data['title'];
                        $senderProfile = '';
                        $moduleId = $data['module_id'] ?? '';
                        if ($moduleId) {
                            $delManageStatus = DeliveryManagement::find($moduleId)->status;
                        }
                    } else {
                        if (isset($data['sender_id'])) {
                            $senderId = $notification->data['sender_id'];
                            if ($data['module'] == 'visitor_request') {
                                $sender = Visitor::find($senderId);
                            } else {
                                $sender = User::find($senderId);
                            }
                            if ($sender) {
                                $senderName = $sender->name ?? '';
                                $senderProfile = $sender->profile_image_url ?? '';
                            }
                            if ($data['module'] == 'visitor_request') {
                                $isVisitorRequest = true;
                            }
                        } else {
                            $senderId = '';
                            $senderName = $data['sender_name'];
                            $senderProfile = '';
                        }
                    }

                    $receiver = User::find($notification->notifiable_id);

                    return [
                        'id' => $notification->id,
                        'sender_name' => $senderName,
                        'sender_profile' => $senderProfile,
                        'message' => $data['message'],
                        'notification_time' => $dateInHuman,
                        'create_at' => $notification->created_at->format(config('constant.date_format.date_time')),
                        'is_read' => is_null($notification->read_at) ? false : true,
                        'is_visitor_request' => $isVisitorRequest,

                        'sender_id' => $senderId,
                        'sender_society_id' => $sender->society->id ?? '',
                        'sender_society_name' => $sender->society->name ?? '',
                        'sender_building_id' => $sender->building->id ?? '',
                        'sender_building_name' => $sender->building->title ?? '',
                        'sender_unit_id' => $sender->unit->id ?? '',
                        'sender_unit_namer' => $sender->unit->title ?? '',

                        'module' => $data['module'],
                        'module_id' => $moduleId,
                        'delivery_management_status' => $delManageStatus,

                        'receiver_id' => $notification->notifiable_id,
                        'receiver_society_id' => $receiver->society->id ?? '',
                        'receiver_society_name' => $receiver->society->name ?? '',
                        'receiver_building_id' => $receiver->building->id ?? '',
                        'receiver_building_name' => $receiver->building->title ?? '',
                        'receiver_unit_id' => $receiver->unit->id ?? '',
                        'receiver_unit_namer' => $receiver->unit->title ?? '',
                    ];
                });

            return $this->respondOk([
                'status'        => true,
                'unread_count'  => $unreadCount,
                'data'          => $notifications,
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function readNotification(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $notification = $user->notifications()->where('id', $request->id)->update(['read_at' => now()]);
            if (!$notification) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.notification.not_found'));
            }

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.notification.mark_as_read')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function sendMessageToGuard(Request $request)
    {
        $request->validate([
            'message'     => ['required', 'string', new NoMultipleSpacesRule]
        ], [], [
            'message' => trans('validation.attributes.message'),
        ]);

        DB::beginTransaction();
        try {
            $resident = Auth::user();
            $society = $resident->society;

            if (!$society) {
                return $this->setStatusCode(404)->respondWithError(trans('messages.society_not_found'));
            }

            $guards = User::whereHas('roles', function ($query) {
                $query->where('id', config('constant.roles.guard'));
            })->where('society_id', $society->id)->get();

            if ($guards->isEmpty()) {
                return $this->setStatusCode(404)->respondWithError(trans('messages.no_guards_found'));
            }

            $messages = [];
            foreach ($guards as $guard) {
                $messages[] = GuardMessage::create([
                    'resident_id' => $resident->id,
                    'guard_id'    => $guard->id,
                    'message'     => $request->message,
                ]);

                // Send Notification to Guard
                $notificationTitle = trans('messages.notification_messages.message_to_guard_admin.title', ['user_name' => $resident->name]);
                $notificationmessage   = trans('messages.notification_messages.message_to_guard_admin.message', ['user_name' => $resident->name]);
                $notificationData = [
                    'sender_id' => $resident->id,
                    'message'   => $notificationmessage,
                    'module'    => 'security_alert'
                ];
                Notification::send($guard, new UserActivityNotification($notificationData));

                SendPushNotification([$guard->id], $notificationTitle, $notificationmessage, 'guard');
            }
            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message' => trans('messages.message_to_guard_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    // Threat Alert to Society
    public function securityAlert(Request $request)
    {
        $request->validate([
            'alert_type' => ['required', 'in:' . implode(',', config('constant.alert_types'))]
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $userSocietyId = $user->society ? $user->society->id : '';

            $successApi = false;
            if ($userSocietyId) {
                $successApi = true;

                SecurityAlert::create([
                    'resident_id' => $user->id,
                    'alert_type' => $request->alert_type
                ]);

                $societyResidents = User::whereStatus(1)->where('society_id', $userSocietyId)->where('id', '!=', $user->id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('id', [config('constant.roles.resident')]);
                    })
                    ->get();
                if ($societyResidents->count() > 0) {
                    // Send Notification
                    $title = ucwords(str_replace('_', ' ', $request->alert_type));
                    $message = trans('messages.notification_messages.security_alert')[$request->alert_type];
                    $notificationData = [
                        'sender_id' => $user->id,
                        'message'   => $message,
                        'module'    => 'security_alert'
                    ];
                    Notification::send($societyResidents, new UserActivityNotification($notificationData));

                    // send Push Notification
                    $societyResidentIds = $societyResidents->pluck('id');
                    SendPushNotification($societyResidentIds, $title, $message, 'user');
                }

                $societyGuards = User::whereStatus(1)->where('society_id', $userSocietyId)->where('id', '!=', $user->id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('id', [config('constant.roles.guard')]);
                    })
                    ->get();
                if ($societyGuards->count() > 0) {
                    // Send Push Notification to Guard 
                    $title = ucwords(str_replace('_', ' ', $request->alert_type));
                    $message = trans('messages.notification_messages.security_alert')[$request->alert_type];
                    $notificationData = [
                        'sender_id' => $user->id,
                        'message'   => $message,
                        'module'    => 'security_alert'
                    ];
                    Notification::send($societyGuards, new UserActivityNotification($notificationData));

                    $societyGuardIds = $societyGuards->pluck('id');
                    SendPushNotification($societyGuardIds, $title, $message, 'guard');
                }
            }

            if ($successApi) {
                DB::commit();
                return $this->respondOk([
                    'status'   => true,
                    'message'   => trans('messages.notification.threat_alert_success')
                ]);
            } else {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function changeVisitorStatus(Request $request)
    {
        $request->validate([
            'visitor_id' => ['required', 'exists:visitors,id'],
            'status' => ['required', 'in:approved,rejected'],
        ]);

        DB::beginTransaction();
        try {
            $visitor = Visitor::find($request->visitor_id);
            if ($visitor->status != 'pending') {
                return $this->setStatusCode(500)->respondWithError(trans('messages.notification.visitor_status_error_already_updated'));
            }

            // Make visitor log status In
            $visitorStatus = "rejected";
            if ($request->status == 'approved') {
                $visitorStatus = "in";
                $visitorLog = new VisitorLog();
                $visitorLog->status = 'in';
                $visitor->visitorLogs()->save($visitorLog);
            }
            $visitor->update(['status' => $visitorStatus]);

            $user = Auth::user();
            $userSocietyId = $user->society ? $user->society->id : '';

            if ($userSocietyId) {
                $societyUsers = User::whereStatus(1)->where('society_id', $userSocietyId)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('id', [config('constant.roles.guard')]);
                    })
                    ->get();
                if ($societyUsers->count() > 0) {
                    // Send Push Notification to Guard
                    $title = "Visitor " . ucfirst($request->status);
                    $message = trans('messages.notification_messages.visitor_status_by_resident', ['status' => $request->status]);
                    $notificationData = [
                        'sender_id' => $user->id,
                        'message'   => $message,
                        'module'    => 'security_alert'
                    ];
                    Notification::send($societyUsers, new UserActivityNotification($notificationData));

                    $societyUserIds = $societyUsers->pluck('id');
                    SendPushNotification($societyUserIds, $title, $message, 'guard');
                }
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.notification.visitor_status_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function sendMessageToAdmin(Request $request)
    {
        $request->validate([
            'message'     => ['required', 'string', new NoMultipleSpacesRule]
        ], [], [
            'message' => trans('validation.attributes.message'),
        ]);

        DB::beginTransaction();
        try {
            $resident = Auth::user();

            $messages = AdminMessage::create([
                'resident_id' => $resident->id,
                'message'     => $request->message,
            ]);

            // Send Notification to Admin
            $admins = getUserForNotification(['admin'], $resident->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $resident->id,
                    'message'   => trans('messages.notification_messages.message_to_guard_admin.message', ['user_name' => $resident->name]),
                    'module'    => 'message_to_admin'
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message' => trans('messages.message_to_admin_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function getSocietyAdmin($societyId)
    {
        try {
            $society = Society::find($societyId);

            $societyAdmin = $society->societyAdmins()->whereStatus(1)->select('id', 'name', 'mobile_number', 'email')->get();

            return $this->respondOk([
                'status'   => true,
                'data'   => $societyAdmin
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function aiBoxFallDetection(Request $request)
    {
        $request->validate([
            // 'Item' => ['required'], 
            'Event_ID' => ['required'],
            'Evenet_Code' => ['required'],
            'Coordinate' => ['required'],
            'Date' => ['required'],
            'Camera_ID' => ['required', 'exists:cameras,camera_id'],
        ], [], [
            'Event_ID' => 'Event ID',
            'Evenet_Code' => 'Event Code',
            'Camera_ID' => 'Camera ID',
        ]);

        DB::beginTransaction();
        try {
            $camera = Camera::where('camera_id', $request->Camera_ID)->where('status', 1)->first();

            $society = $camera->society;
            if (!$society) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.notification_messages.ai_box.fall_detection.society_error'));
            }

            $apiTypes = config('constant.aibox_notification.api_type');
            $apiType = $apiTypes[$request->Evenet_Code] ?? null;

            if (!$apiType) {
                return $this->setStatusCode(400)->respondWithError(trans('messages.notification_messages.ai_box.fall_detection.invalid_event_code'));
            }

            AiBoxAlert::create([
                'camera_id' => $camera->id,
                'society_id' => $society->id,
                'notification_data' => $request->all(),
                'api_type' => $apiType
            ]);

            // Society Users
            $societyUsers = getUserForNotification(['admin', 'guard', 'resident'], $society, true);

            $notificationTitle = trans('messages.notification_messages.ai_box.fall_detection.title');
            $notificationMessage = trans('messages.notification_messages.ai_box.fall_detection.message', ['camera_location' => $camera->lacated_at, 'society_name' => $society->name]);
            $notificationData = [
                'message'   => $notificationMessage,
            ];

            if ($societyUsers->count() > 0) {
                Notification::send($societyUsers, new AiBoxNotification($notificationData));
            }

            // Firebase Push Notification
            $guards = $societyUsers->filter(function ($user) {
                return $user->roles->contains('id', config('constant.roles.guard'));
            });

            $residents = $societyUsers->filter(function ($user) {
                return $user->roles->contains('id', config('constant.roles.resident'));
            });

            // Send To Guard
            if ($guards->count() > 0) {
                SendPushNotification($guards->pluck('id'), $notificationTitle, $notificationMessage, 'guard');
            }

            // Send to Resident
            if ($residents->count() > 0) {
                SendPushNotification($residents->pluck('id'), $notificationTitle, $notificationMessage, 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message' => trans('messages.notification_messages.ai_box.fall_detection.success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function updateDeliveryManagementStatus(Request $request)
    {
        $request->validate([
            'delivery_management_id' => ['required', 'exists:delivery_management,id'],
            'status' => ['required', 'in:processing,delivered'],
        ]);
        DB::beginTransaction();
        try {
            $deliverymanagement = DeliveryManagement::find($request->delivery_management_id);

            /* if($deliverymanagement->status == 'new'){
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            } */

            $deliverymanagement->update(['status' => $request->status]);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message' => trans('messages.crud.status_update'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function changeLogo()
    {
        try {
            $setting = Setting::whereStatus(1)->where('key', 'splash_logo')->first();

            $logo = $setting
                ? getSetting('splash_logo')
                : asset(config('constant.default.splash_logo'));

            return $this->respondOk([
                'status'   => true,
                'data'   => $logo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
