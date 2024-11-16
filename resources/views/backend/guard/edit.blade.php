@extends('layouts.admin')
@section('title', trans('cruds.guard.title_singular'))

@section('custom_css')
@endsection

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.guard.title_singular')</h4>
            </div>
            <div class="card-body">
                <form class="msg-form" id="guardEditForm" enctype="multipart/form-data" data-url="{{ route('admin.guards.update', [$guard->uuid]) }}">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="id" value="{{$guard->uuid}}">
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