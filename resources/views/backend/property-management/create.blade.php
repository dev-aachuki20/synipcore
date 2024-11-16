@extends('layouts.admin')
@section('title', trans('cruds.property_management.title_singular'))

@section('custom_css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <link rel="stylesheet" href="{{ asset('backend/vendor/flatpickr/flatpickr.min.css') }}">
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.property_management.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="propertyManagementAddForm" data-url="{{ route('admin.property-managements.store') }}"
                    enctype="multipart/form-data">
                        @csrf
                        @include('backend.property-management._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    @include('backend.property-management.partials.script')    
@endsection
