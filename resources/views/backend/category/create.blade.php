@extends('layouts.admin')
@section('title', trans('cruds.category.title_singular'))

@section('custom_css')
@endsection

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-20">
                <div class="card-header">
                    <h4 class="mb-0">{{ trans('global.add') }} {{ trans('global.new') }}
                        {{ trans('cruds.category.title_singular') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="msg-form" id="categoryAddForm" data-url="{{ route('admin.categories.store') }}">
                        @csrf
                        @include('backend.category._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_js')
@parent
    @include('backend.category.partials.script')
@endsection
