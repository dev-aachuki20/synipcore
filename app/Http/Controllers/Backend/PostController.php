<?php

namespace App\Http\Controllers\Backend;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\DataTables\PostDataTable;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Notifications\UserActivityNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PostDataTable $dataTable)
    {
        abort_if(Gate::denies('post_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.post.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('post_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return view('backend.post.create');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('post_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $validatedData = $request->validated();
                $post = Post::create($validatedData);

                if ($post && $post->post_type == 'image' && $request->has('post_image')) {
                    $files = $request->file('post_image');
                    if (is_array($files)) {
                        array_map(function ($file) use ($post) {
                            if ($file->isValid()) {
                                uploadImage($post, $file, 'post/post-images', 'post_image', 'original', 'save', null);
                            }
                        }, $files);
                    }
                }

                if ($post && $post->post_type == 'video' && empty($request->video_url) && $request->has('post_video')) {
                    $videoFile = $request->file('post_video');
                    uploadImage($post, $videoFile, 'post/post-videos', 'post_video', 'original', 'save', null);
                }

                $user = Auth::user();
                // $title = $validatedData['title'];
                $title = '';
                $message = trans('messages.notification_messages.post.new_post');

                $this->sendNotifications($user, $title, $message);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.created_successfully'),
                ], 201);
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
    public function show(Request $request, string $uuid)
    {
        abort_if(Gate::denies('post_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                $post = Post::where('uuid', $uuid)->first();
                $viewHTML = view('backend.post.show', compact('post'))->render();
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
        abort_if(Gate::denies('post_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $post = Post::where('uuid', $uuid)->first();
            if ($post) {
                // $existingImages = $post->uploads()->where('type', 'post_image')->pluck('file_path');
                return view('backend.post.edit', compact('post'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $uuid)
    {
        abort_if(Gate::denies('post_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $validatedData = $request->validated();
                $post = Post::where('uuid', $uuid)->first();
                if ($post) {
                    $post->update($validatedData);

                    // update content according to post type
                    $this->deletePostAccordingToPostType($post);

                    if ($post && $post->post_type == 'image') {
                        if (isset($request->postDocIds)) {
                            $documentIds = explode(',', $request->postDocIds);
                            foreach ($documentIds as $documentId) {
                                deleteFile($documentId);
                            }
                        }
                        if ($request->has('post_image')) {

                            $files = $request->file('post_image');
                            if (is_array($files)) {
                                array_map(function ($file) use ($post) {
                                    if ($file->isValid()) {
                                        uploadImage($post, $file, 'post/post-images', 'post_image', 'original', 'save', null);
                                    }
                                }, $files);
                            }
                        }
                    }

                    if ($post && $post->post_type == 'video') {
                        if (!empty($request->postVideoRemoved)) {
                            $postVideo = $post->postVideo;
                            if ($postVideo) {
                                deleteFile($postVideo->id);
                            }
                        }
                        if (empty($request->video_url) && $request->has('post_video')) {
                            $videoFile = $request->file('post_video');
                            uploadImage($post, $videoFile, 'post/post-videos', 'post_video', 'original', 'save', null);
                        }
                    }

                    $user = Auth::user();
                    // $title = $validatedData['title'];
                    $title = '';
                    $message = trans('messages.notification_messages.post.post_updated');

                    $this->sendNotifications($user, $title, $message);
                } else {
                    DB::rollBack();
                    return response()->json(['success' => false, 'something_error', 'error' => trans('messages.error_message')], 404);
                }
                DB::commit();
                return response()->json(['success' => true, 'message' => trans('messages.updated_successfully'),], 200);
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
        abort_if(Gate::denies('post_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $post = Post::where('uuid', $uuid)->first();
                if ($post) {
                    $this->removePostVideo($post);
                    $this->removePostImages($post);
                    $post->delete();
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

    public function postStatusChange(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:posts,uuid',
                'status' => 'required|in:publish,unpublish,draft',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->toArray(), 'message' => 'Error Occurred!',], 400);
            }

            DB::beginTransaction();
            try {
                $post = Post::where('uuid', $request->id)->update(['status' => $request->status]);

                DB::commit();
                return response()->json(['success'    => true, 'message'   => trans('messages.status_update_successfully'),]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
            }
        }
    }

    public function postCommentDetail($uuid)
    {
        abort_if(Gate::denies('post_comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $post = Post::where('uuid', $uuid)->first();
            if ($post) {
                return view('backend.post.comment-detail', compact('post'));
            } else {
                return response()->json(['success' => false, 'error_type' => 'not_found', 'error' => trans('messages.not_found')], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    // public function createPostSlug(Request $request)
    // {
    //     if ($request->ajax()) {
    //         try {
    //             if ($request->has('title') && !empty($request->input('title'))) {
    //                 $slug = convertTitleToSlug($request->input('title'));
    //                 $checkSlug = Post::where('slug', $slug)->first();

    //                 if ($checkSlug) {
    //                     $response['success']    = false;
    //                     $response['data_type']  = 'already_exist';
    //                     $response['data']       = $slug;
    //                     $response['message']    = trans('messages.location.slug');
    //                 } else {
    //                     $response['success']    = true;
    //                     $response['data_type']  = 'not_exist';
    //                     $response['data']       = $slug;
    //                     $response['message']    = 'Slug Created!';
    //                 }
    //             }
    //             return response()->json($response);
    //         } catch (\Exception $e) {
    //             return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
    //         }
    //     }
    // }

    protected function sendNotifications($user, $title, $message)
    {
        $titleFormatted = ucwords(str_replace('_', ' ', $title));

        // Prepare notification data
        $notificationData = [
            'sender_id' => $user->id,
            'message'   => $message,
            'module'    => 'post'
        ];

        // Notify residents and guards based on user role
        $roles = [
            'resident' => config('constant.roles.resident'),
        ];

        if ($user->is_admin) {
            $societyResidents = User::whereStatus(1)->where('id', '!=', $user->id)
                ->whereHas('roles', function ($q) use ($roles) {
                    $q->whereIn('id', [$roles['resident']]);
                })
                ->get();
        } elseif ($user->is_sub_admin) {
            $targetSocietyId = $user->society_id;

            $societyResidents = User::whereStatus(1)->where('society_id', $targetSocietyId)->where('id', '!=', $user->id)
                ->whereHas('roles', function ($q) use ($roles) {
                    $q->whereIn('id', [$roles['resident']]);
                })
                ->get();
        }

        // Send notifications
        if ($societyResidents->isNotEmpty()) {
            Notification::send($societyResidents, new UserActivityNotification($notificationData));
            $societyResidentIds = $societyResidents->pluck('id');
            SendPushNotification($societyResidentIds, $titleFormatted, $message, 'user');
        }
    }

    private function deletePostAccordingToPostType($post)
    {
        if ($post->post_type == 'text') {
            $this->removePostVideo($post);
            $this->removePostImages($post);
        } else if ($post->post_type == 'image') {
            $this->removePostVideo($post);
            $post->update(['content' => null]);
        } else if ($post->post_type == 'image') {
            $this->removePostImages($post);
            $post->update(['content' => null]);
        }
    }

    // Remove Post Video
    private function removePostVideo($post)
    {
        if ($post->postVideo) {
            deleteFile($post->postVideo->id);
        }
    }

    // Remove Post Images
    private function removePostImages($post)
    {
        if ($post->postImages) {
            foreach ($post->postImages as $postImage) {
                deleteFile($postImage->id);
            }
        }
    }
}
