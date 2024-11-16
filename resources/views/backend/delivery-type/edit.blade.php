@extends('layouts.admin')
@section('title', trans('cruds.delivery_type.title_singular'))
@section('custom_css')
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.delivery_type.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="deliveryTypeEditForm"
                        data-url="{{ route('admin.delivery-types.update', [$deliveryType->uuid]) }}">
                        @method('PUT')
                        @csrf
                        @include('backend.delivery-type._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    @parent
    @include('backend.delivery-type.partials.script')
@endsection
