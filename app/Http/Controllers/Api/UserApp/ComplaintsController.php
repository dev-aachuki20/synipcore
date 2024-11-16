<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ComplaintsController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $complaintList = $user->complaints()
                ->select('id', 'complaint_type_id', 'category', 'description', 'status', 'created_at')
                ->get()
                ->map(function ($complaint) use ($user) {
                    return [
                        'id' => $complaint->id,
                        'user_name' => $user->name,
                        'complaint_type_name' => $complaint->complaintType ? $complaint->complaintType->title : '',
                        'category' => ucfirst($complaint->category),
                        'status' => ucfirst($complaint->status),
                        'description' => $complaint->description,
                        'created_date' => $complaint->created_at->format(config('constant.date_format.date_time')),
                        'comment_count' => $complaint->total_approved_comments
                    ];
                });

            return $this->respondOk([
                'status'   => true,
                'data'   => $complaintList
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function create()
    {
        try {
            $complaintTypes = ComplaintType::whereStatus(1)->select('id', 'title')->get()->toArray();
            $types = config('constant.complaint_categories');

            return $this->respondOk([
                'status'   => true,
                'data'   => ['complaint_types' => $complaintTypes, 'types' => $types]
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'complaint_type_id' => ['required', 'exists:complaint_types,id'],
            'category'              => ['required', 'in:' . implode(',', config('constant.complaint_categories'))],
            'description'       => ['required'],
            'complaint_images*' => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'complaint_images' => ['array', 'max:' . config('constant.post_image_max_file_count')],
        ], [], [
            'complaint_type_id' => 'complaint type',
            'complaint_images'  => 'complaint image',
        ]);
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $input = $request->all();
            $input['user_id'] = $user->id;

            $complaint = Complaint::create($input);

            if ($complaint && $request->has('complaint_images')) {
                foreach ($request->complaint_images as $file) {
                    uploadImage($complaint, $file,  'complaint/complaint-images', "complaint_image", 'original', 'save', null);
                }
            }

            // Send Notification to Admin
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.complaint.raise_complaint', ['user_name' => $user->name]),
                    'module'    => 'complaint',
                    'module_id'    => $complaint->uuid,
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.complaint.store_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function complaintDetail($complaint_id)
    {
        try {
            $complaint = Complaint::where('id', $complaint_id)
                ->select('id', 'user_id', 'complaint_type_id', 'category', 'description', 'created_at', 'status')
                ->first();

            $complaint['user_name'] = auth()->user()->name;

            $complaint['category'] = ucfirst($complaint->category);
            $complaint['status'] = ucfirst($complaint->status);

            $complaint['complaint_type_name'] = $complaint->complaintType ? $complaint->complaintType->title : '';

            $complaint['created_date'] = $complaint->created_at->format(config('constant.date_format.date_time'));

            $complaintImages = $complaint->complaint_image_urls;
            $complaint['complaint_image_urls'] = $complaintImages;

            $comments = $complaint->comments()->where('is_approve', 1)->get()
                ->map(function ($comment) {
                    $remainingTime = $comment->created_at->diffForHumans(now());
                    return [
                        'user_image' => $comment->user->profile_image_url,
                        'user_name' => $comment->user->name,
                        'unit_name' => $comment->user->unit->title,
                        'comment' => $comment->comment,
                        'comment_time' => str_replace(['before'], ['ago'], $remainingTime),
                    ];
                });
            $complaint['comments'] = $comments;

            $complaint = collect($complaint)->except(['complaint_type', 'complaint_images'])->all();

            return $this->respondOk([
                'status'   => true,
                'data'   => $complaint
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function resolveComplaint(Request $request)
    {
        $request->validate([
            'complaint_id' => ['required', 'exists:complaints,id']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $complaint = Complaint::find($request->complaint_id);

            $complaint->update(['status' => 'resolved']);

            // Send Notification to Admin
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.complaint.resolve_complaint', ['user_name' => $user->name]),
                    'module'    => 'complaint',
                    'module_id' => $complaint->uuid,
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.complaint.mark_resolved')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function sendComment(Request $request)
    {
        $request->validate([
            'complaint_id'  => ['required', 'exists:complaints,id'],
            'comment'       => ['required']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $complaint = Complaint::find($request->complaint_id);

            uploadComment($complaint, $user->id, $request->comment);

            $receiverUser = $complaint->user ?? '';
            // Send Notification to User
            if ($receiverUser && $user->id != $complaint->user_id) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.complaint.comment_by_other_complaint', ['user_name' => $user->name]),
                    'module'    => 'complaint',
                    'module_id' => $complaint->uuid,
                ];
                Notification::send($receiverUser, new UserActivityNotification($notificationData));

                // Send Push Notification
                $notificationTitle = trans('messages.notification_messages.complaint.comment_by_other_complaint_title');
                SendPushNotification([$receiverUser->id], $notificationTitle, $notificationData['message'], 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.complaint.comment_send')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
