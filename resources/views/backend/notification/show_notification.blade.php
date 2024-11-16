@forelse($notifications as $notification)
<a href="javascript:void(0);" class="dropdown-item notify-item mark_single_read_notification" data-notification-id="{{$notification['id']}}">
    <div class="notify-icon bg-primary-subtle">
        <i class="mdi mdi-comment-account-outline text-primary"></i>
    </div>
    <p class="notify-details">{!! ucfirst($notification['message'] ?? trans('global.no_message_available')) !!}
        <small class="noti-time">{{ $notification['notification_time'] }}</small>
    </p>
</a>
@empty
<p class="dropdown-item text-center notify-item pt-2">{{trans('global.no_new_notifications')}}</p>
@endforelse