<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Announcement;
use App\Models\PollVote;
use App\Notifications\UserActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends APIController
{

    public function index()
    {
        try {
            $user = auth()->user();

            $user->unreadNotifications()->where('data->module', 'announcement')->update(['read_at' => now()]);

            $announcements = Announcement::whereStatus(1)->where('society_id', $user->society_id)
                ->latest()
                ->paginate(config('constant.api_page_limit.announcement'));

            $announcementData = $announcements->map(function ($announcement) {
                $announcementType = $announcement->announcement_type;

                $returnData = [
                    'id' => $announcement->id,
                    'title' => $announcement->title ?? '',
                    'message' => $announcement->message ?? '',

                    'announcement_type' => config('constant.annuncement_types')[$announcementType] ?? '',
                    'announcement_images' => $announcement->announcement_image_urls ?? '',
                    'post_by' => $announcement->postedBy ? $announcement->postedBy->name : '',
                    'post_date' => $announcement->created_at->format(config('constant.date_format.date_time')),
                    'likes' => $announcement->likes()->count(),
                    'dislikes' => $announcement->dislikes()->count(),
                    'comments' => $announcement->comments()->where('is_approve', 1)->count(),
                ];

                // Check poll
                if ($announcementType == 2) {
                    $totalVotes = $announcement->pollVotes()->count();
                    $returnData['poll_type'] = $announcement->poll_type;
                    $returnData['expire_date'] = $announcement->expire_date->format(config('constant.date_format.date_time'));

                    $pollExpired = false;
                    if (now()->gt($announcement->expire_date)) {
                        $pollExpired = true;
                    }

                    $returnData['total_vote'] = $totalVotes;
                    $returnData['is_poll_expired'] = $pollExpired;

                    $pollOptions = $announcement->options()->select('id', 'option')->orderBy('option', 'asc')->get()
                        ->map(function ($pollOption) use ($totalVotes) {
                            $pollVoteCount = $pollOption->pollVotes()->count();

                            $votePercentage = 0;
                            if ($totalVotes > 0) {
                                $votePercentage = round(($pollVoteCount / $totalVotes) * 100, 2);
                            }

                            return [
                                'id' => $pollOption->id,
                                'name' => $pollOption->option,

                                'vote_count' => $pollVoteCount,
                                'vote_percentage' => $votePercentage,
                            ];
                        });

                    $returnData['options'] = $pollOptions;
                }

                return $returnData;
            });

            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'next_page_url' => $announcements->nextPageUrl(),
                    'announcements' => $announcementData,
                ]
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function sendComment(Request $request)
    {
        $request->validate([
            'announcement_id'  => ['required', 'exists:announcements,id'],
            'comment'       => ['required']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $announcement = Announcement::find($request->announcement_id);

            uploadComment($announcement, $user->id, $request->comment);

            // Send Notification to Admin
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.notice_board.comment_notification', ['user_name' => $user->name, 'announcement_title' => $announcement->title]),
                    'module'    => 'announcement',
                    'module_id' => $announcement->id,
                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
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

    public function sendReaction(Request $request)
    {
        $request->validate([
            'announcement_id'  => ['required', 'exists:announcements,id'],
            'type' => ['required', 'in:like,dislike']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $announcement = Announcement::find($request->announcement_id);

            $reaction = updateLikeDislike($announcement, $user->id, $request->type);

            $sendNotification = false;
            if ($reaction && $reaction->reaction_type != $request->type) {
                $sendNotification = true;
            } else {
                $sendNotification = true;
            }

            // Send Notification to Admin
            if ($sendNotification) {
                $admins = getUserForNotification(['admin'], $user->society, true);
                if ($admins->count() > 0) {
                    $notificationData = [
                        'sender_id' => $user->id,
                        'message'   => trans('messages.notification_messages.notice_board.like_notification', ['user_name' => $user->name, 'type' => $request->type, 'announcement_title' => $announcement->title]),
                        'module'    => 'announcement',
                        'module_id' => $announcement->id,

                    ];
                    Notification::send($admins, new UserActivityNotification($notificationData));
                }
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

    public function addPollVote(Request $request)
    {
        $request->validate([
            'notice_board_id'  => ['required', 'exists:announcements,id'],
            'poll_option_id'  => ['required', 'exists:poll_options,id']
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $announcement = Announcement::find($request->notice_board_id);
            $announcementExpireDate = $announcement->expire_date;
            if (now()->gt($announcementExpireDate)) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.announcement.error_expire_date'));
            }

            $pollVote = PollVote::where('poll_option_id', $request->poll_option_id)->where('user_id', $user->id)->first();

            if ($pollVote) {
                $pollVote->delete();
            } else {
                $pollVote = PollVote::create([
                    'user_id' => $user->id,
                    'poll_option_id' => $request->poll_option_id,
                    'notice_board_id' => $request->notice_board_id,
                ]);
            }

            // Send Notification to Admin
            $admins = getUserForNotification(['admin'], $user->society, true);
            if ($admins->count() > 0) {
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.notice_board.vote_poll', ['user_name' => $user->name, 'announcement_title' => $announcement->title]),
                    'module'    => 'announcement',
                    'module_id' => $announcement->id,

                ];
                Notification::send($admins, new UserActivityNotification($notificationData));
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

    public function getAnnouncementComments($id)
    {
        try {
            $announcement = Announcement::find($id);

            $comments = $announcement->approvedComments()->latest()->paginate(config('constant.api_page_limit.comment'));

            $commentData = $comments->map(function ($comment) {
                $remainingTime = $comment->created_at->diffForHumans(now());
                return [
                    'user_image'    => $comment->user->profile_image_url ?? null,
                    'user_name'     => $comment->user->name ?? null,
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
}
