@extends('layouts.admin')
@section('title', trans('cruds.guard.title_singular'))

@section('custom_css')
@endsection

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('global.add') @lang('global.new') @lang('cruds.guard.title_singular')</h4>
            </div>
            <div class="card-body">
                <form class="msg-form" id="guardAddForm" enctype="multipart/form-data" data-url="{{ route('admin.guards.store') }}">
                    @csrf
                    @include('backend.guard._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
@include('backend.guard.partials.script')
@endsection