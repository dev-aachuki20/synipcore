@extends('layouts.admin')
@section('title', trans('cruds.permission.title_singular'))
@section('custom_css')
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.edit') }} {{ trans('cruds.permission.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="permissionEditForm"
                        data-url="{{ route('admin.permissions.update', [$permission->id]) }}">
                        @method('PUT')
                        @csrf
                        @include('backend.permission._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    @parent
    @include('backend.permission.partials.script')
@endsection
