<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ComplaintDataTable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\StoreRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;


class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ComplaintDataTable $dataTable)
    {
        abort_if(Gate::denies('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.complaint.index');
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('complaint_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $complaint = Complaint::where('uuid', $id)->first();
                if (!$complaint) {
                    return response()->json(['success' => false, 'error_type' => 'not_found', 'error' => trans('messages.complaint_not_found')], 404);
                }
                $viewHTML = view('backend.complaint.show', compact('complaint'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $uuid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, $uuid)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        abort_if(Gate::denies('complaint_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $complaint = Complaint::where('uuid', $uuid)->first();
                if ($complaint) {
                    $complaint->delete();
                }

                DB::commit();
                return response()->json([
                    'success'    => true,
                    'message'    => trans('messages.deleted_successfully'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function statusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id'     => 'required|exists:complaints,uuid',
                'status' => 'required|in:resolved,pending,in_progress,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $complaint = Complaint::where('uuid', $request->id)->first();
                if ($complaint) {
                    $complaint->update(['status' => $request->status]);

                    $user = Auth::user();
                    $status = $request->status;
                    $userId = $complaint->user_id;
                    $message = trans('messages.notification_messages.complaint.status_message', ['status' => ucfirst($status)]);

                    $this->sendNotifications($user, $status, $message, $userId);
                }
                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }

    protected function sendNotifications($user, $status, $message, $userId)
    {
        $title = '';

        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'message'   => $message,
            'module'    => 'complaint'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
        ];

        $societyResidents = User::whereStatus(1)->where('id', $userId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['resident']]);
            })
            ->get();

        // Send notifications
        if ($societyResidents->isNotEmpty()) {
            Notification::send($societyResidents, new UserActivityNotification($notificationData));
            $societyResidentIds = $societyResidents->pluck('id');
            SendPushNotification($societyResidentIds, $title, $message, 'user');
        }
    }

    public function viewImage(Request $request)
    {
        if ($request->ajax()) {
            try {
                $complaint = Complaint::find($request->id);
                $imageArrays = [];
                if ($complaint) {
                    if (!empty($complaint->complaint_image_urls) && is_array($complaint->complaint_image_urls)) {
                        foreach ($complaint->complaint_image_urls as $image) {
                            $imageArrays[] = asset($image);
                        }
                    }
                }
                $viewHTML = view('backend.complaint.show_image', compact('imageArrays'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
}
