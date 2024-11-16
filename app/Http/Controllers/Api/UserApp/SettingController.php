<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingController extends APIController
{
    public function currentUserPosts(){
        try {
            $user = Auth::user();

            $posts = Post::whereStatus('publish')->where('created_by', $user->id)
            ->select('id', 'title', 'slug', 'content', 'created_by', 'created_at')
            ->latest()
            ->paginate(config('constant.api_page_limit.post'));

            $postsData = $posts->map(function($post) use($user) {
                $remainingTime = $post->created_at->diffForHumans(now());
                return [
                    'id' => $post->id,
                    'title' => $post->title ?? '',
                    'slug' => $post->slug ?? '',
                    'content'   => $post->content ?? '',
                    'user_name' => $post->user->name,
                    'user_profile_image' => $post->user->profile_image_url,
                    'unit'      => $post->user && $post->user->unit ? $post->user->unit->title : '',
                    'post_time' => str_replace(['before'],['ago'], $remainingTime),

                    'post_images' => $post->post_image_urls,
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
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function household(){
        try {
            $user = Auth::user();

            // Get Resident Family Member
            $familyMembers = getUsersFamilyMembers();

            // $get Resident Daily Helps
            $dailyHelps = getUsersDailyHelps();

            // Get Resident Vehicles
            $vehicles = getUsersVehicles();

            // $get Resident frequest Entries
            $frequestEntries = getUsersFrequestEntries();

            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'family_members' => $familyMembers,
                    'daily_helps' => $dailyHelps,
                    'vehicles' => $vehicles,
                    'frequest_entries' => $frequestEntries,
                ]
            ]);
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}