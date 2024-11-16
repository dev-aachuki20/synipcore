<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Post;
use App\Models\PostView;
use App\Models\SavedPost;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PostController extends APIController
{
    public function index()
    {
        try {
            $user = auth()->user();

            $posts = Post::whereStatus('publish')
                ->whereHas('user')
                ->select('id', 'title', 'slug', 'post_type', 'content', 'created_by', 'created_at', 'video_url')
                ->latest()
                ->paginate(config('constant.api_page_limit.post'));

            $postsData = $posts->map(function ($post) use ($user) {
                $remainingTime = $post->created_at->diffForHumans(now());
                return [
                    'id' => $post->id,
                    'title' => $post->title ?? '',
                    'slug' => $post->slug ?? '',
                    'content'   => $post->content ?? '',
                    'post_type'   => $post->post_type ?? '',
                    'user_name' => $post->user ? $post->user->name : '',
                    'user_profile_image' => $post->user ? $post->user->profile_image_url : '',
                    'unit'      => $post->user && $post->user->unit ? $post->user->unit->title : '',
                    'post_time' => str_replace(['before'], ['ago'], $remainingTime),

                    'post_images' => $post->post_image_urls,
                    'post_video' => $post->post_video_url,
                    'video_url' => $post->video_url,
                    'like_count' => $post->total_likes,
                    'view_count' => $post->total_views,
                    'comment_count' => $post->total_approved_comments,
                    'is_liked' => $post->likes()->where('user_id', $user->id)->count() > 0 ? true : false,
                    'is_saved' => $user->savedPosts()->where('post_id', $post->id)->count() > 0 ? true : false,

                    'created_date' => $post->created_at->format(config('constant.date_format.date_time'))
                ];
            });

            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'next_page_url' => $posts->nextPageUrl(),
                    'posts' => $postsData,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.post_status')))],
            'title' => ['nullable', 'string'],
            'content' => ['nullable', 'string', 'required_if:post_image,[]'],
            'post_image*' => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'post_image' => ['array', 'max:' . config('constant.post_image_max_file_count'), 'required_if:content,null'],
        ]);
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $input = $request->all();
            $input['status'] = 'publish';

            $post = Post::create($input);

            if ($post && $request->hasFile('post_image') && count($request->file('post_image')) > 0) {
                $files = $request->file('post_image');
                foreach ($files as $file) {
                    uploadImage($post, $file, 'post/post-images', 'post_image', 'original', 'save');
                }
            }

            // Send Notification to User
            $residents = getUserForNotification(['resident'], $user->society, false, $user->id);
            if ($residents->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.post.create_post_message', ['user_name' => $user->name]),
                    'module'    => 'post'
                ];
                Notification::send($residents, new UserActivityNotification($notificationData));

                // Send Push Notification
                $notificationTitle = trans('messages.notification_messages.post.create_post_title');
                SendPushNotification($residents->pluck('id'), $notificationTitle, $notificationData['message'], 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.post.store_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        try {
            $post = Post::whereId($id)->select('id', 'content', 'status')->first();

            $postImages = $post->postImages()->get()
                ->map(function ($postImage) {
                    return [
                        'id' => $postImage->id,
                        'url' => ($postImage->file_path && Storage::disk('public')->exists($postImage->file_path)) ? asset('storage/' . $postImage->file_path) : '',
                    ];
                });
            $post['post_images'] = $postImages;

            return $this->respondOk([
                'status'   => true,
                'data'   => $post
            ]);
        } catch (\Exception $e) {
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['nullable', 'string'],
            'status'            => ['required', 'in:' . implode(',', array_keys(config('constant.status_type.post_status')))],
            'content' => ['nullable', 'string', 'required_if:post_image,[]'],
            'post_image*' => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'post_image' => ['array', 'max:' . config('constant.post_image_max_file_count'), 'required_if:content,null'],
        ]);
        DB::beginTransaction();
        try {
            $post = Post::find($id);

            $input = $request->all();

            $post->update($input);

            // add post images
            if ($post && $request->hasFile('post_image') && count($request->file('post_image')) > 0) {
                $files = $request->file('post_image');
                foreach ($files as $file) {
                    uploadImage($post, $file, 'post/post-images', 'post_image', 'original', 'save');
                }
            }

            // remove post images
            if (isset($request->deleted_post_image_ids) && !empty($request->deleted_post_image_ids)) {
                $postImageIds = explode(',', $request->deleted_post_image_ids);
                foreach ($postImageIds as $postImageId) {
                    deleteFile($postImageId);
                }
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.post.update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $post = Post::find($id);
            if ($post) {
                if ($post->postImages) {
                    foreach ($post->postImages()->get() as $postImage) {
                        deleteFile($postImage->id);
                    }
                }
                $post->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.post.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }

    public function postDetail($postId)
    {
        try {
            $user = auth()->user();
            $post = Post::where('id', $postId)
                ->select('id', 'title', 'slug', 'content', 'created_by', 'created_at')
                ->first();

            if ($post->views()->count() == 0) {
                // add views
                PostView::create(['post_id' => $postId, 'user_id' => $user->id]);
            }

            $remainingTime = $post->created_at->diffForHumans(now());
            $postDetail = [
                'id' => $post->id,
                'title' => $post->title ?? '',
                'slug' => $post->slug ?? '',
                'content'   => $post->content ?? '',
                'user_name' => $post->user->name,
                'user_profile_image' => $post->user->profile_image_url,
                'unit'      => $post->user && $post->user->unit ? $post->user->unit->title : '',
                'post_time' => str_replace(['before'], ['ago'], $remainingTime),

                'post_images' => $post->post_image_urls,
                'like_count' => $post->total_likes,
                'view_count' => $post->total_views,
                'comment_count' => $post->total_approved_comments,
                'is_liked' => $post->likes()->where('user_id', $user->id)->count() > 0 ? true : false,
                'is_saved' => $user->savedPosts()->where('post_id', $post->id)->count() > 0 ? true : false,

                'created_date' => $post->created_at->format(config('constant.date_format.date_time'))
            ];

            $comments = $post->approvedComments()->latest()->paginate(config('constant.api_page_limit.comment'));

            $commentData = $comments->map(function ($comment) {
                $remainingTime = $comment->created_at->diffForHumans(now());
                return [
                    'user_image'    => $comment->user->profile_image_url,
                    'user_name'     => $comment->user->name,
                    'unit_name'     => isset($comment->user->unit->title) ? $comment->user->unit->title : '',
                    'comment'       => $comment->comment,
                    'comment_time'  => str_replace(['before'], ['ago'], $remainingTime),
                ];
            });
            $postDetail['comment_data'] = [
                'next_page_url' => $comments->nextPageUrl(),
                'comments'      => $commentData
            ];

            return $this->respondOk([
                'status'   => true,
                'data'   => $postDetail
            ]);
        } catch (\Exception $e) {
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function sendComment(Request $request)
    {
        $request->validate([
            'post_id'  => ['required', 'exists:posts,id'],
            'comment'       => ['required']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $post = Post::find($request->post_id);

            uploadComment($post, $user->id, $request->comment);

            $receiverUser = $post->user ?? '';
            // Send Notification to User
            if ($receiverUser && $user->id != $receiverUser->id) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.post.comment_post_message', ['user_name' => $user->name]),
                    'module'    => 'post'
                ];
                Notification::send($receiverUser, new UserActivityNotification($notificationData));

                // Send Push Notification
                $notificationTitle = trans('messages.notification_messages.post.comment_post_title');
                SendPushNotification([$receiverUser->id], $notificationTitle, $notificationData['message'], 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.post.comment_send')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function likePost(Request $request)
    {
        $request->validate([
            'post_id'  => ['required', 'exists:posts,id']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $post = Post::find($request->post_id);

            updateLikeDislike($post, $user->id, 'like');

            $receiverUser = $post->user ?? '';
            // Send Notification to User
            if ($receiverUser && $user->id != $receiverUser->id) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.post.like_post_message', ['user_name' => $user->name]),
                    'module'    => 'post'
                ];
                Notification::send($receiverUser, new UserActivityNotification($notificationData));

                // Send Push Notification
                $notificationTitle = trans('messages.notification_messages.post.like_post_title');
                SendPushNotification([$receiverUser->id], $notificationTitle, $notificationData['message'], 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function savePost(Request $request)
    {
        $request->validate([
            'post_id'  => ['required', 'exists:posts,id']
        ]);

        DB::beginTransaction();
        try {
            $post = Post::find($request->post_id);
            $user = auth()->user();

            $savedPost = $user->savedPosts()->where('post_id', $post->id)->first();
            if (!$savedPost) {
                SavedPost::create(['post_id' => $post->id, 'user_id' => $user->id]);
                $message = trans('messages.post.save_post');
                $type = 'save';
            } else {
                $savedPost->delete();
                $message = trans('messages.post.unsave_post');
                $type = 'unsave';
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'type' => $type,
                'message'   => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function getPostComments($id)
    {
        try {
            $post = Post::find($id);

            $comments = $post->approvedComments()->latest()->paginate(config('constant.api_page_limit.comment'));

            $commentData = $comments->map(function ($comment) {
                $remainingTime = $comment->created_at->diffForHumans(now());
                return [
                    'user_image'    => $comment->user->profile_image_url,
                    'user_name'     => $comment->user->name,
                    'unit_name'     => isset($comment->user->unit->title) ? $comment->user->unit->title : '',
                    'comment'       => $comment->comment,
                    'comment_time'  => str_replace(['before'], ['ago'], $remainingTime),
                ];
            });
            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'next_page_url' => $comments->nextPageUrl(),
                    'comments'      => $commentData
                ]
            ]);
        } catch (\Exception $e) {
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function residentsList()
    {
        try {
            $user = auth()->user();

            $currentUserSociety = $user->society;

            if ($currentUserSociety) {
                $units = $currentUserSociety->units()->select('id', 'title')->orderBy('title', 'asc')->get()
                    ->map(function ($unit) {
                        $residents = $unit->residents()->whereStatus(1)->where('is_verified', 1)->where('id', '<>', auth()->user()->id)->whereHas('roles', function ($query) {
                            $query->where('id', config('constant.roles.resident'));
                        })
                            ->select('id', 'name', 'resident_type', 'mobile_number', 'unit_id')
                            ->get()
                            ->map(function ($resident) {
                                return [
                                    'id' => $resident->id,
                                    'name' => $resident->name ?? '',
                                    'profile_image' => $resident->profile_image_url ?? '',
                                    'resident_type' => $resident->resident_type ? config('constant.resident_types')[$resident->resident_type] : '',
                                    'mobile_number' => $resident->mobile_number ?? '',
                                ];
                            });
                        return [
                            'title' => $unit->title ?? '',
                            'residents' => $residents
                        ];
                    });

                return $this->respondOk([
                    'status'   => true,
                    'data'   => $units
                ]);
            } else {
                return $this->respondOk([
                    'status'   => true,
                    'data'   => []
                ]);
            }
        } catch (\Exception $e) {
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
