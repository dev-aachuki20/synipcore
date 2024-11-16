@extends('layouts.admin')
@section('title', trans('cruds.announcement.title_singular'))

@section('custom_css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link rel="stylesheet" href="{{ asset('backend/vendor/flatpickr/flatpickr.min.css')}}">
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.announcement.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="announcementEditForm" data-url="{{ route('admin.announcements.update', [$announcement->uuid]) }}">
                        @method('PUT')
                        @csrf
                        @include('backend.announcement._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
@parent
    @include('backend.announcement.partials.script')
@endsection
