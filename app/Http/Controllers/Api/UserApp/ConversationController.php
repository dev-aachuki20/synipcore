<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\UserActivityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ConversationController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $currentUserId = $user->id;
            $conversations = $user->conversations()
                ->where('is_blocked', 0)
                ->with(['participants', 'messages' => function ($query) {
                    $query->latest('created_at')->first(); // Get the latest message in each conversation
                }])
                ->whereHas('messages')
                ->whereHas('participants')
                ->paginate(config('constant.api_page_limit.conversation'));

            // Process the conversation data
            $conversationData = $conversations->map(function ($conversation) use ($currentUserId) {
                $participanet = $conversation->participants()->where('user_id', '!=', $currentUserId)->first();

                $lastMessage = $conversation->messages()->first();
                $messageType = optional($lastMessage)->content_type;
                $content = (($messageType == 'image') ? "Image" : (($messageType == 'video') ? 'Video' : (($messageType == 'document') ? 'Document' : Str::limit(optional($lastMessage)->content, 50))));

                $today = Carbon::now();
                $sevenDaysAgo = $today->copy()->subDays(6);
                $messageDate = optional($lastMessage)->created_at;
                $isBetween = $messageDate->between($today, $sevenDaysAgo);
                if ($isBetween) {
                    $lastMessageTime = $messageDate->format('D');
                    if ($messageDate->isToday()) {
                        $lastMessageTime = $messageDate->format(config('constant.date_format.time'));
                    }
                } else {
                    $lastMessageTime = $messageDate->format(config('constant.date_format.date'));
                }

                return [
                    'conversation_id'           => $conversation->id,
                    'resident_id'               => optional($participanet)->id,
                    'resident_name'             => optional($participanet)->name,
                    'resident_profile_image'    => optional($participanet)->profile_image_url,

                    'last_message'              => $content, // Latest message content
                    'last_message_time'         => $lastMessageTime, // Latest message Time
                    'message_created_at'        => $messageDate->format(config('constant.date_format.date')), // Latest message Time
                ];
            });
            return $this->respondOk([
                'success'   => true,
                'data'   => [
                    'conversation_next_page_url' => $conversations->nextPageUrl(),
                    'conversations' => $conversationData,
                ],
            ]);
        } catch (\Exception $e) {
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function blockConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
        ]);
        DB::beginTransaction();
        try {
            $conversation = Conversation::find($request->conversation_id);

            $conversation->update(['is_blocked' => 1]);

            DB::commit();
            return $this->respondOk([
                'status'   => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function deleteConversation($id)
    {
        DB::beginTransaction();
        try {
            $conversation = Conversation::find($id);

            if (!$conversation) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }
            if ($conversation) {
                $conversation->delete();
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function singleConversationMessages(Request $request)
    {
        $request->validate([
            'resident_id' => ['required', 'exists:users,id']
        ]);
        DB::beginTransaction();
        try {
            $currentUserId = Auth::user()->id;
            $reqUserId = $request->resident_id;
            $conversation = $this->getPrivateConversation($currentUserId, $reqUserId);
            $participanet = $conversation->participants()->where('user_id', '!=', $currentUserId)->first();

            $messages = $conversation->messages()->orderBy('created_at', 'desc')->paginate(config('constant.api_page_limit.message'));
            $messageData = $this->getConversationMessagesWithPagination($messages);

            $residentName = $participanet->name;
            $residentProfileImage = $participanet->profile_image_url;
            $residentUnitName = $participanet->unit->title;

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'conversation_id' => $conversation->id ?? null,
                    'resident_name' => $residentName ?? null,
                    'resident_profile_image' => $residentProfileImage ?? null,
                    'resident_unit_name' => $residentUnitName ?? null,

                    'message_next_page_url' => $messages->nextPageUrl(),
                    'messages' => $messageData,
                ]
            ]);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }


    public function SendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $videoExtensions = ['mp4', 'mkv', 'webm', 'flv', 'avi', 'mov', 'wmv', 'mpeg', 'mpg', 'm4v', '3gp', '3g2', 'f4v', 'f4p', 'f4a', 'f4b'];
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'tif', 'ico', 'heic', 'heif'];

            $conversationId = $request->conversation_id;
            if ($request->has('message') && !empty($request->message)) {
                Message::create([
                    'sender_id' => $user->id,
                    'conversation_id' => $conversationId,
                    'content' => $request->message,
                    'content_type' => 'text',
                ]);
            }

            if ($request->has('files') && !empty($request->files)) {
                foreach ($request->file('files') as $file) {
                    $fileExtention = $file->getClientOriginalExtension();
                    // Check Image
                    if (in_array($fileExtention, $imageExtensions)) {
                        $fileType = 'message_image';
                        $content_type = 'image';
                    } else if (in_array($fileExtention, $videoExtensions)) { // Check Video
                        $fileType = 'message_video';
                        $content_type = 'video';
                    } else { // Check File
                        $fileType = 'message_document';
                        $content_type = 'document';
                    }
                    $message = Message::create([
                        'sender_id' => $user->id,
                        'conversation_id' => $conversationId,
                        'content_type' => $content_type
                    ]);
                    if ($message) {
                        uploadImage($message, $file,  'conversation/' . $conversationId, $fileType, 'original', 'save');
                    }
                }
            }

            $receiverUser = $post->user ?? '' ;
            $conversation = Conversation::find($conversationId);
            $receiverUser = $conversation->participants()->where('user_id', '!=', $user->id)->first();
            // Send Notification to User
            if($receiverUser && $user->id != $receiverUser->id){
                $notificationData = [
                    'sender_id' => $user->id,
                    'message'   => trans('messages.notification_messages.conversation.new_message_message', ['user_name' => $user->name]),
                    'module'    => 'conversation'
                ];
                Notification::send($receiverUser, new UserActivityNotification($notificationData));

                // Send Push Notification
                $notificationTitle = trans('messages.notification_messages.conversation.new_message_title');
                SendPushNotification([$receiverUser->id], $notificationTitle, $notificationData['message'], 'user');
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function deleteMessage($id)
    {
        DB::beginTransaction();
        try {
            $message = Message::find($id);
            if (!$message) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }
            if ($message) {
                $message->delete();
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    protected function getConversationMessagesWithPagination($messages)
    {
        $reverseMessages = $messages->reverse();

        $groupedMessages  = $reverseMessages->groupBy(function ($message) {
            $today = Carbon::now();
            $sevenDaysAgo = $today->copy()->subDays(6);

            $messageDate = $message->created_at;
            $isBetween = $messageDate->between($today, $sevenDaysAgo);
            if ($isBetween) {
                $dayOfWeek = $messageDate->format('l');
                if ($messageDate->isToday()) {
                    return 'Today';
                }
                if ($messageDate->isYesterday()) {
                    return 'Yesterday';
                }
                return $dayOfWeek;
            }
            return $message->created_at->format(config('constant.date_format.date'));
        });

        $messageData = $groupedMessages->map(function ($group) {
            return $group->map(function ($message) {
                $remainingTime = $message->created_at->diffForHumans(now());

                $messageType = $message->content_type;
                if ($messageType == 'image') {
                    $content = $message->message_image_urls;
                } else if ($messageType == 'video') {
                    $content = $message->message_video_urls;
                } else if ($messageType == 'document') {
                    $content = $message->message_document_urls;
                } else {
                    $content = $message->content;
                }

                $isSender = false;
                if($message->sender_id == auth()->user()->id){
                    $isSender = true;
                }

                return [
                    'id'            => $message->id,
                    'message_type'  => $messageType ?? '',
                    'content'       => $content ?? '',
                    'message_time'  => str_replace(['before'], ['ago'], $remainingTime),
                    'created_date'  => $message->created_at->format(config('constant.date_format.date')),
                    'created_time'  => $message->created_at->format(config('constant.date_format.time')),

                    'is_sender'     => $isSender
                ];
            });
        });

        return $messageData;
    }

    protected function getPrivateConversation($userId, $otherUserId)
    {
        $conversation = Conversation::where('conversation_type', 'private')
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('participants', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['created_by' => $userId]);
            if ($conversation) {
                $conversation->participants()->sync([$userId, $otherUserId]);
            }
        } else {
            // Mark all messages read
            $conversation->messages()->whereNull('read_at')->update(['read_at' => now()]);
        }

        return $conversation;
    }
}
