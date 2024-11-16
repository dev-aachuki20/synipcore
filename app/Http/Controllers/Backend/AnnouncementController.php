<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\AnnouncementDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\StoreRequest;
use App\Http\Requests\Announcement\UpdateRequest;
use App\Models\Announcement;
use App\Models\Society;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;




class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AnnouncementDataTable $dataTable)
    {
        abort_if(Gate::denies('announcement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.announcement.index');
        } catch (\Exception $e) {

            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('announcement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $society = Society::whereStatus(1)->latest()->get();
            return view('backend.announcement.create', compact('society', 'user'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('announcement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $validatedData['posted_by'] = Auth::user()->id;
                $announcement = Announcement::create($validatedData);

                if (!empty($announcement) && $request['announcement_type'] == 2) {
                    $options = $request['options'];
                    foreach ($options as $key => $value) {
                        $pollData = [
                            'notice_board_id'   => $announcement->id,
                            'option'            => $value,
                        ];
                        $announcement->options()->create($pollData);
                    }
                }

                if ($announcement && $request->has('announcement_image')) {
                    $files = $request->file('announcement_image');
                    if (is_array($files)) {
                        array_map(function ($file) use ($announcement) {
                            if ($file->isValid()) {
                                uploadImage($announcement, $file, 'announcement/announcement-images', 'announcement_image', 'original', 'save', null);
                            }
                        }, $files);
                    }
                }
                $announcementTypeLabel = config('constant.annuncement_types')[$request['announcement_type']];
                $user = Auth::user();
                $title = $validatedData['title'];
                $societyId = $validatedData['society_id'];
                $message = trans('messages.notification_messages.notice_board.new_announcement', ['announcement_type' => $announcementTypeLabel]);

                $this->sendNotifications($user, $title, $message, $societyId);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $announcement,
                    'message' => trans('messages.created_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        abort_if(Gate::denies('announcement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = auth()->user();
            $announcement = Announcement::where('uuid', $uuid)->first();
            $society = Society::whereStatus(1)->latest()->get();
            $pollOptions = $announcement->announcement_type == 2 ? $announcement->options : [];
            return view('backend.announcement.edit', compact('announcement', 'society', 'user', 'pollOptions'));
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('announcement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                // $validatedData['posted_by'] = Auth::user()->id;
                $announcement = Announcement::where('uuid', $uuid)->first();
                $announcement->update($validatedData);

                if (!empty($announcement) && $request['announcement_type'] == 2) {
                    $options = $request['options'];
                    $existingOptions = $announcement->options()->pluck('option', 'id')->toArray();
                    foreach ($options as $key => $value) {
                        if ($existingOptionId = array_search($value, $existingOptions)) {
                            $announcement->options()->where('id', $existingOptionId)->update([
                                'option' => $value,
                            ]);
                        } else {
                            $announcement->options()->create([
                                'notice_board_id' => $announcement->id,
                                'option' => $value,
                            ]);
                        }
                    }
                    // Remove options that are no longer in the request
                    $newOptions = array_values($options);
                    $announcement->options()->whereNotIn('option', $newOptions)->delete();
                }

                if ($announcement && $request->has('announcement_image')) {
                    $files = $request->file('announcement_image');
                    if (is_array($files)) {
                        array_map(function ($file) use ($announcement) {
                            if ($file->isValid()) {
                                uploadImage($announcement, $file, 'announcement/announcement-images', 'announcement_image', 'original', 'save', null);
                            }
                        }, $files);
                    }
                }

                if (isset($request->announcement_managementIds)) {
                    $documentIds = explode(',', $request->announcement_managementIds);
                    foreach ($documentIds as $documentId) {
                        deleteFile($documentId);
                    }
                }

                $user = Auth::user();
                $title = $validatedData['title'];
                $societyId = $validatedData['society_id'];
                $message = trans('messages.notification_messages.notice_board.announcement_updated');

                $this->sendNotifications($user, $title, $message, $societyId);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $announcement,
                    'message' =>  trans('messages.updated_successfully'),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        abort_if(Gate::denies('announcement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $announcement = Announcement::where('uuid', $uuid)->first();
            DB::beginTransaction();
            try {
                if ($announcement) {
                    $announcement->options()->delete();
                    $announcement->delete();
                }

                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.deleted_successfully'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }

    protected function sendNotifications($user, $title, $message, $societyId)
    {
        $title = ucwords(str_replace('_', ' ', $title));

        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'message'   => $message,
            'module'    => 'announcement'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
            'guard' => config('constant.roles.guard')
        ];

        $societyResidents = User::whereStatus(1)->where('society_id', $societyId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['resident']]);
            })
            ->get();

        $societyGuards = User::whereStatus(1)->where('society_id', $societyId)->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', [$roles['guard']]);
            })
            ->get();


        // Send notifications
        if ($societyResidents->isNotEmpty()) {
            Notification::send($societyResidents, new UserActivityNotification($notificationData));
            $societyResidentIds = $societyResidents->pluck('id');
            SendPushNotification($societyResidentIds, $title, $message, 'user');
        }

        if ($societyGuards->isNotEmpty()) {
            Notification::send($societyGuards, new UserActivityNotification($notificationData));

            $societyGuardIds = $societyGuards->pluck('id');
            SendPushNotification($societyGuardIds, $title, $message, 'guard');
        }
    }

    public function viewImage(Request $request)
    {
        if ($request->ajax()) {
            try {
                $announcement = Announcement::find($request->id);
                $imageArrays = [];
                if ($announcement) {
                    if (!empty($announcement->announcement_image_urls) && is_array($announcement->announcement_image_urls)) {
                        foreach ($announcement->announcement_image_urls as $image) {
                            $imageArrays[] = asset($image);
                        }
                    }
                }
                $viewHTML = view('backend.announcement.show_image', compact('imageArrays'))->render();
                return response()->json(array('success' => true, 'htmlView' => $viewHTML));
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    }
}
