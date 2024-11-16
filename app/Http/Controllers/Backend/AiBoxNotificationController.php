<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\AiBoxNotificationDataTable;
use App\Http\Controllers\Controller;
use App\Models\AiBoxAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;



class AiBoxNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AiBoxNotificationDataTable $dataTable)
    {
        abort_if(Gate::denies('aibox_notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.ai-box-notification.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Display the specified resource.T
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('aibox_notification_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $aiboxAlert = AiBoxAlert::where('id', $id)->first();
                $viewHTML = view('backend.ai-box-notification.show', compact('aiboxAlert'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    public function viewImage(Request $request)
    {
        abort_if(Gate::denies('aibox_notification_view_image'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $aiBoxAlert = AiBoxAlert::find($request->id);
                $imageArrays = [];
                if ($aiBoxAlert && $aiBoxAlert->api_type) {
                    $attachments = config('constant.aibox_notification_attachemnts.aibox_images');

                    if (isset($attachments[$aiBoxAlert->api_type])) {
                        foreach ($attachments[$aiBoxAlert->api_type] as $image) {
                            $imageArrays[] = asset($image);
                        }
                    }
                }
                $viewHTML = view('backend.ai-box-notification.show_image', compact('imageArrays'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
    public function viewVideo(Request $request)
    {
        abort_if(Gate::denies('aibox_notification_view_video'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $aiBoxAlert = AiBoxAlert::find($request->id);
                $videoArrays = [];
                if ($aiBoxAlert && $aiBoxAlert->api_type) {
                    $attachments = config('constant.aibox_notification_attachemnts.aibox_videos');
                    if (isset($attachments[$aiBoxAlert->api_type])) {
                        $videoArrays = $attachments[$aiBoxAlert->api_type];
                    }
                }

                $viewHTML = view('backend.ai-box-notification.show_video', compact('videoArrays'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
}
