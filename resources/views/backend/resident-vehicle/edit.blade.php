@extends('layouts.admin')
@section('title', trans('cruds.resident_vehicle.title_singular'))

@section('custom_css')
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">@lang('global.edit') @lang('global.new') @lang('cruds.resident_vehicle.title_singular')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" id="vehicleEditForm" class="msg-form" data-url="{{ route('admin.resident-vehicles.update', [$vehicle->uuid]) }}">
                        @method('PUT')
                        @csrf
                        @include('backend.resident-vehicle._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
@parent
    @include('backend.resident-vehicle.partials.script')
@endsection
