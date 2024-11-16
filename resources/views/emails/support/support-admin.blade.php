@extends('emails.layouts.admin')

@section('email-content')

    <p>Hi {{config('constant.support.name')}},</p>

    <p>This email notifies you of a new support request has been submitted on {{$support->created_at->format(config('constant.date_format.date'))}} at {{$support->created_at->format(config('constant.date_format.time'))}}.</p>

    <b>User Information:</b>

    <ul>
        <li>Name: {{ $support->user ? $support->user->name : '' }}</li>
        <li>Email: {{ $support->user ? $support->user->email : '' }}</li>
    </ul>

    <p><b>Topic:</b></p> 
    
    <p>{{ $support->topic ? $support->topic : '' }}</p>

    <p><b>Message:</b></p>

    <p>{{ $support->message ? $support->message : '' }}</p>

    <p><b>How to Respond:</b></p>

    <p>You can easily reply directly to this email to reach the user.</p>

    <p>Thank you,</p>

    <p>The {{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} Team</p>
@endsection
