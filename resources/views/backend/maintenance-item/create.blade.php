@extends('layouts.admin')
@section('title', trans('cruds.maintenance_item.title_singular'))

@section('custom_css')
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.maintenance_item.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="maintenanceItemAddForm" data-url="{{ route('admin.maintenance-items.store') }}">
                        @csrf
                        @include('backend.maintenance-item._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
@parent
    @include('backend.maintenance-item.partials.script')
@endsection
