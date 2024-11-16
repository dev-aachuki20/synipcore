<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\DeliveryManagement;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $user = Auth::user();
            $society = $user->society->id ?? null;
            $perPage = 10;
            $totalUnread = $user->unreadNotifications()->count();
            $records = [];
            // Fetch notifications for the user
            if($request->type == 'header_notification'){
                $notifications  = $user->unreadNotifications()->latest()->paginate($perPage);
            }else{
                $notifications  = $user->notifications()->latest()->paginate($perPage);
            }

            $paginatedNotifications = $notifications->getCollection()->map(function ($notification) {
                $data = $notification->data;
                $notificationDate = $notification->created_at;
                $dateInHuman = str_replace('before', 'ago', $notificationDate->diffForHumans(now()));

                // Process sender information
                $senderName = '';
                $senderProfile = '';
                $isVisitorRequest = false;

                if (isset($data['sender_id'])) {
                    $senderId = $data['sender_id'];
                    $sender = ($data['module'] == 'visitor_request')
                        ? Visitor::find($senderId)
                        : User::find($senderId);

                    if ($sender) {
                        $senderName = $sender->name ?? '';
                        $senderProfile = $sender->profile_image_url ?? '';
                    }
                    $isVisitorRequest = $data['module'] == 'post';
                } else {
                    $senderName = $data['sender_name'] ?? trans('global.unknown_sender');
                    $senderProfile = '';
                }

                // Determine the delivery status if module_id exists
                $deliveryStatus = null;
                if (isset($data['module_id'])) {
                    $moduleId = $data['module_id'];
                    $deliveryStatus = DeliveryManagement::select('status', 'respondant_id')->where('id', $moduleId)->first();
                }


                // Return structured notification data
                return [
                    'id' => $notification->id,
                    'sender_name' => $senderName,
                    'sender_profile' => $senderProfile,
                    'message' => $data['message'] ?? trans('global.no_message_available'),
                    'notification_time' => $dateInHuman,
                    'created_at' => $notification->created_at->format(config('constant.date_format.date_time')),
                    'is_read' => !is_null($notification->read_at),
                    'is_visitor_request' => $isVisitorRequest,
                    'sender_id' => $senderId ?? '',
                    'module' => $data['module'] ?? 'unknown',
                    'module_id' => $data['module_id'] ?? '',
                    'delivery_status' => $deliveryStatus ?? '',
                ];
            });
            $notifications->setCollection($paginatedNotifications);
            // Check if the request is an AJAX request
            if ($request->ajax()) {
                $html = view('backend.notification.show_notification', compact('notifications'))->render();
                return response()->json([
                    'html' => $html,
                    'totalUnread' => $totalUnread,
                    // 'delivery_status' => $deliveryStatus,
                ]);
            } else {
                return view('backend.notification.index', compact('notifications', 'totalUnread'));
            }
        } catch (\Exception $e) {
            return abort(500, 'Internal Server Error');
        }
    }



    public function markAsReadNotification(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $request->id)->update(['read_at' => now()]);
            if (!$notification) {
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.notification.not_found'),
                ], 400);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.notification.mark_as_read'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return abort(500);
        }
    }

    public function markAllReadNotification(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $notifications = $user->notifications()->update(['read_at' => now()]);

            if (!$notifications) {
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.notification.not_found'),
                ], 400);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.notification.mark_all_read'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return abort(500);
        }
    }

    public function updateStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::user()->id;
            $deliveryManagement = DeliveryManagement::find($request->id);
            $deliveryManagement->update(['status' => $request->hidden_status, 'respondant_id' => $userId]);

            DB::commit();
            return response()->json([
                'success' => true,
                'deliveryManagement' => $deliveryManagement->status,
                'message' => trans('messages.status_update_successfully'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return abort(500);
        }
    }
}
