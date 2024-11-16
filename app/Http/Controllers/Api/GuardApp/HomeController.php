<?php

namespace App\Http\Controllers\Api\GuardApp;

use App\Http\Controllers\Api\APIController;
use App\Models\GuardMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Guard;

class HomeController extends APIController
{
    public function recentMessages()
    {
        try {
            $user = Auth::user();

            $user->guardMessages()->whereNull('read_at')->update(['read_at' => now()]);

            $messages = $user->guardMessages()->whereHas('resident')->whereStatus(1)->latest()->paginate(config('constant.api_page_limit.guard_messages'));

            $messageData = $messages->map(function($message){
                $remainingTime = $message->created_at->diffForHumans(now());
                return [
                    'id' => $message->id,
                    'resident_id' => $message->resident_id,

                    'message' => $message->message ?? '',

                    'resident_image' => $message->resident ? $message->resident->profile_image_url : '',
                    'resident_name' => $message->resident ? $message->resident->name : '',
                    'resident_building' => $message->resident->building ? $message->resident->building->title : '',
                    'resident_unit' => $message->resident->unit ? $message->resident->unit->title : '',

                    'resident_phone_number' => $message->resident ? $message->resident->mobile_number : '',

                    'message_time' => str_replace(['before'],['ago'], $remainingTime),
                    'created_at' => $message->created_at->format(config('constant.date_format.date_time')),

                    'is_read' => $message->read_at ? true : false,
                ];
            });

            return $this->respondOk([
                'status'   => true,
                'data'   => [
                    'next_page_url' => $messages->nextPageUrl(),
                    'messages' => $messageData,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
