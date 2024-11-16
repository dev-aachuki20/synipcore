@extends('emails.layouts.admin')
@section('styles')
@endsection

@section('email-content')
    <tr>
        <td>
            <p class="mail-title" style="font-size:14px;">
                <b>Hello</b> {{ $user->name ?? "" }},
            </p>
            <div class="mail-desc">
                <p style="font-size:14px;">We received a request to reset your password. Please use the following OTP to proceed:</p>
                <p>Your OTP: {{ $token }}</p>

                <p>This OTP will expire in {{ $expiretime}}. If you did not request a password reset, please ignore this email.</p>
            </div>
        </td>
        <tr>
            <td>
                <p style="font-size:14px;">If you did not request a password reset, no further action is required.</p>
            </td>
        </tr>
    </tr>
@endsection
