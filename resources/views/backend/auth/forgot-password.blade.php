@extends('layouts.admin_auth')
@section('title', trans('global.forgot_password_title'))
@section('main-content')

<div class="l_content">
    <div class="container px-0">
        <div class="row items-center">
            <div class="col-lg-7 d-none d-lg-block">
                <img src="{{ asset('backend/images/forgot-password.svg') }}" alt="" class="img-fluid">
            </div>
            <div class="col-lg-5">
                <div class="log-register-block">
                    <div class="text-center mb-4">
                    <a href="#" title="logo" class="header-logo">
                            <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="logo">
                        </a>
                   </div>
                    <h2 class="text-center">Forgot Password?</h2>
                    <p class="text-center">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                    <form method="POST" action="{{route("admin.password_mail_link")}}">
                        @csrf
                        <div class="form-group">
                            <label for="emailaddress" class="form-label">@lang('global.login_email')</label>
                            <input class="form-control" type="email" name="email"  placeholder="Enter your email" value="{{ old('email') }}" tabindex="1"   autofocus>
                            @error('email')
                            <span class="invalid-feedback d-block">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>
                        <div class="btn-block">
                            <button class="btn btn-soft-primary w-100" type="submit">@lang('global.reset_password')</button>
                        </div>
                    </form>
                    <p class="bottom-para">Already have an Account? <a href="{{ route('login') }}" class="text-decoration-underline">@lang('global.login')</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 text-center">
        
    </div>
</div>

@endsection
