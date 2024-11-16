@extends('layouts.admin')
@section('title', trans('cruds.post.title_singular'))
@section('custom_css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <link href="{{ asset('backend/vendor/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.post.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="postAdd" data-url="{{ route('admin.posts.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @include('backend.post._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
@parent
    @include('backend.post.partials.script')
@endsection
