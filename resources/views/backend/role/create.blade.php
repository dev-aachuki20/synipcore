@extends('layouts.admin')
@section('title', trans('cruds.role.title_singular'))
@section('custom_css')
    <link href="{{ asset('backend/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add_new') }} {{ trans('cruds.role.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="roleForm" data-url="{{ route('admin.roles.store') }}">
                        @csrf
                        @include('backend.role._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('custom_js')
    @parent
    @include('backend.role.partials.script')
@endsection
