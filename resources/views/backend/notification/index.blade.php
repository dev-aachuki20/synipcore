@extends('layouts.admin')
@section('title', trans('cruds.menus.notifications'))

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">@lang('cruds.menus.notifications')</h4>
                @php

                $allRead = isset($notifications) && count($notifications) > 0 && collect($notifications)->every(function($notification) {
                return is_array($notification) && isset($notification['is_read']) ? $notification['is_read'] : false;
                });
                @endphp
                <button class="all_read readAllNotification" {{ $allRead ? 'disabled' : '' }}>{{ trans('global.mark_all_read') }}</button>
            </div>
            <div class="card-body">
                <div class="inner_notification">
                    @if(isset($notifications) && count($notifications) > 0)
                    <ul>
                        @foreach($notifications as $notification)
                        <li class="{{ $notification['is_read'] ? 'read' : '' }}">
                            <div class="noti-icon">
                                <i class="ri-notification-3-line fs-22"></i>
                            </div>
                            @if($notification['module'] == 'complaint')
                            <a href="javaScript:void(0);" data-complaint-href="{{route('admin.complaints.index')}}" class="notification_redirection" data-href="{{ url('/admin/complaints/' . $notification['module_id']) }}"
                                data-id="{{$notification['module_id']}}" id="{{$notification['module_id']}}">
                                <div class="noti-content">
                                    <h5 class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{{ ucfirst($notification['sender_name'] ?? trans('global.unknown_sender')) }}</h5>
                                    <p class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{!! ucfirst($notification['message'] ?? trans('global.no_message_available')) !!}</p>
                                    @php
                                    $sender = \App\Models\User::find($notification['sender_id']);
                                    $societies = $sender ? $sender->societies : collect();
                                    $societyNames = $societies->pluck('name')->implode(', ') ?: 'N/A';
                                    @endphp
                                    <span><i class="ri-home-7-line"></i> {{ $societyNames ?? '' }}</span>
                                    <span><i class="ri-time-line"></i> {{ $notification['notification_time'] ?? '' }}</span>
                                </div>
                            </a>
                            @elseif($notification['module'] == 'announcement')
                            <a href="{{ route('admin.announcements.index') }}">
                                <div class="noti-content">
                                    <h5 class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{{ ucfirst($notification['sender_name'] ?? trans('global.unknown_sender')) }}</h5>
                                    <p class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{!! ucfirst($notification['message'] ?? trans('global.no_message_available')) !!}</p>
                                    @php
                                    $sender = \App\Models\User::find($notification['sender_id']);
                                    $societies = $sender ? $sender->societies : collect();
                                    $societyNames = $societies->pluck('name')->implode(', ') ?: 'N/A';
                                    @endphp
                                    <span><i class="ri-home-7-line"></i> {{ $societyNames ?? '' }}</span>
                                    <span><i class="ri-time-line"></i> {{ $notification['notification_time'] ?? '' }}</span>
                                </div>
                            </a>
                            @else
                            <div class="noti-content">
                                <h5 class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{{ ucfirst($notification['sender_name'] ?? trans('global.unknown_sender')) }}</h5>
                                <p class="{{ $notification['is_read'] ? '' : 'fw-bold' }}">{!! ucfirst($notification['message'] ?? trans('global.no_message_available')) !!}</p>
                                @php
                                $sender = \App\Models\User::find($notification['sender_id']);
                                $societies = $sender ? $sender->societies : collect();
                                $societyNames = $societies->pluck('name')->implode(', ') ?: 'N/A';
                                @endphp
                                <span><i class="ri-home-7-line"></i> {{ $societyNames ?? '' }}</span>
                                <span><i class="ri-time-line"></i> {{ $notification['notification_time'] ?? '' }}</span>
                            </div>
                            @endif

                            <div class="noti-drop">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-2-fill fs-22"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item readNotification" type="button" data-id="{{ $notification['id'] }}" {{ $notification['is_read'] ? 'disabled' : '' }}>
                                                {{ trans('global.mark_as_read') }}
                                            </button>
                                        </li>
                                        @if($notification['module'] == 'delivery_management')
                                        @if(isset($notification['delivery_status']))
                                        @if($notification['delivery_status']['status'] == 'new')
                                        <li>
                                            <input type="hidden" class="processing" value="processing">
                                            <button class="dropdown-item del-mang-process" id="del-mang-process" data-delivery-id="{{$notification['module_id'] ?? ''}}" data-status="processing" type="button" {{ $notification['is_read'] ? 'disabled' : '' }}>
                                                {{ trans('global.processing') }}
                                            </button>
                                        </li>
                                        <li>
                                            <input type="hidden" class="resolve" value="delivered">
                                            <button class="dropdown-item del-mang-resolve" id="del-mang-resolve" data-delivery-id="{{$notification['module_id'] ?? ''}}" data-status="delivered" type="button">
                                                {{ trans('global.resolve') }}
                                            </button>
                                        </li>
                                        @elseif($notification['delivery_status']['status'] == 'processing' && $notification['delivery_status']['respondant_id'] == Auth::user()->id)
                                        <li>
                                            <input type="hidden" class="resolve" value="delivered">
                                            <button class="dropdown-item del-mang-resolve" id="del-mang-resolve" data-delivery-id="{{$notification['module_id'] ?? ''}}" data-status="delivered" type="button">
                                                Resolve
                                            </button>
                                        </li>
                                        @endif
                                        @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if(isset($notification['delivery_status']['status']) && $notification['delivery_status']['status'] == 'delivered')
                            <div class="deliverd_msg">
                                <span class="">
                                    {{ucfirst($notification['delivery_status']['status'])}}
                                </span>
                            </div>
                            @elseif(isset($notification['delivery_status']['status']) && $notification['delivery_status']['status'] == 'processing')
                            <div class="deliverd_msg">
                                <span class="">
                                    {{ucfirst($notification['delivery_status']['status'])}}
                                </span>
                            </div>
                            @endif
                            @endforeach
                        </li>
                    </ul>
                    <div class="mt-3">
                        {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                    </div>
                    @else
                    <p class="text-center mb-0">{{ trans('global.no_new_notifications') }}</p>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
    $(document).ready(function() {
        // Handle single notification read action
        $(document).on("click", ".readNotification", function(e) {
            e.preventDefault();
            let url = "{{ route('admin.read.notifications') }}";
            let notificationId = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: notificationId // Send the notification ID to mark it as read
                },
                success: function(response) {
                    if (response.success) {
                        toasterAlert('success', response.message);
                        // Remove the notification item or update its read status dynamically
                        $(`button[data-id="${notificationId}"]`).closest('li').find('.noti-drop').remove();
                        window.location.reload();
                    } else {
                        toasterAlert('error', response.error);
                    }
                },
                error: function(xhr) {
                    toasterAlert('error', xhr.responseJSON.error);
                }
            });
        });

        // Handle mark all notifications as read
        $(document).on("click", ".readAllNotification", function(e) {
            e.preventDefault();
            let url = "{{ route('admin.read.allNotifications') }}";

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toasterAlert('success', response.message);
                        window.location.reload();

                    } else {
                        toasterAlert('error', response.error);
                    }
                },
                error: function(xhr) {
                    toasterAlert('error', xhr.responseJSON.error);
                }
            });
        });

        // Update the status of notifications which is relevent to the delivery management.
        $(document).on("click", ".del-mang-process", function(e) {
            e.preventDefault();
            let url = "{{ route('admin.updateStatus') }}";
            var status = $(this).data('status');
            var hidden_status = $('.processing').val();
            var id = $(this).data('delivery-id');


            updateStatus(url, status, hidden_status, id);

        });

        $(document).on("click", ".del-mang-resolve", function(e) {
            e.preventDefault();
            let url = "{{ route('admin.updateStatus') }}";
            var status = $(this).data('status');
            var hidden_status = $('.resolve').val();
            var id = $(this).data('delivery-id');
            updateStatus(url, status, hidden_status, id);

        });

        function updateStatus(url, status, hidden_status, id) {
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status,
                    hidden_status: hidden_status,
                    id: id,
                },
                success: function(response) {
                    if (response.success) {
                        toasterAlert('success', response.message);
                        window.location.reload();
                    } else {
                        toasterAlert('error', response.error);
                    }
                },
                error: function(xhr) {
                    toasterAlert('error', xhr.responseJSON.error);
                }
            });
        }

        $(document).on("click", ".notification_redirection", function(e) {
            e.preventDefault();
            // Set flag and URL in sessionStorage
            sessionStorage.setItem('triggerAjax', 'true');
            sessionStorage.setItem('complaintUrl', $(this).data('href'));
            // Redirect to the complaints page
            window.location.href = $(this).data('complaint-href');
        });       

    });


    
</script>
@endsection