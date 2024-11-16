@extends('layouts.admin')
@section('title', trans('cruds.property_management.title_singular'))

@section('custom_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">


<style>
    .select2-container {
        width: auto !important;
    }
</style>

@endsection

@section('main-content')

<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">@lang('cruds.property_management.title') @lang('global.report')</h4>
            </div>
            <div class="card-body">
                <div class="inner_block">
                    <div class="society_area">
                        <div class="society_select">
                            <select id="select-society" class="select-society form-control">
                                <option value="">{{trans('global.select')}}</option>
                                @forelse($societies as $society)
                                <option value="{{$society->id}}" {{ request()->get('society_id') == $society->id ? 'selected' : '' }}>{{ucfirst($society->name)}}</option>
                                @empty
                                <option value="">{{trans('global.data_not_found')}}</option>
                                @endforelse
                            </select>
                            <a class="btn btn-primary" id="reset-report-button" href="{{route('admin.reports')}}">
                                <i class="ri-restart-line"></i> {{trans('global.reset')}}
                            </a>
                        </div>
                        <div class="society_date">
                            <span id="update-date"><strong>{{trans('global.update_date')}} :</strong> {{$lastSocietyUpdateDate}}</span>
                        </div>
                    </div>
                    <div class="table-responsive custom-table nth_child2_table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{trans('cruds.property_managment_report.fields.no')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.item')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.image')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.location')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.amount')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.specifications')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.purchased_date')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.unit_price')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.allocation')}}</th>
                                    <th>{{trans('cruds.property_management.fields.property_type')}}</th>
                                    <th>{{trans('cruds.property_managment_report.fields.code')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($propertyManagement))
                                @include('backend.property-management._property_rows', ['propertyManagement' => $propertyManagement])
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

<script>
    $(document).ready(function() {

        $('.select-society').select2({
            // placeholder: "Select Society",
        });

        // Handle change event of the society dropdown
        $('#select-society').change(function() {
            var societyId = $(this).val();
            $('.loader-div').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('admin.filterBySociety') }}",
                type: 'GET',
                data: {
                    society_id: societyId,
                },
                success: function(response) {
                    $('.loader-div').hide();
                    $('table tbody').html(response.html);
                    $('#update-date').html(response.lastUpdateDate);
                    function countWords(str) {
                        return str.trim().split(/\s+/).length;
                    }
                    function truncateContent(str, wordLimit) {
                        return str.split(/\s+/).slice(0, wordLimit).join(" ") + "...";
                    }
                    $(".full_data").each(function () {
                        const fullText = $(this).text();
                        const wordCount = countWords(fullText);
                        if (wordCount > 50) {
                            const truncatedText = truncateContent(fullText, 50);
                            $(this).text(truncatedText);
                            $(this).next(".more_data_btn").show();
                            $(this).next(".more_data_btn").on("click", function () {
                                const $content = $(this).prev(".full_data");
                                if ($(this).text() === "{{__('global.read_more')}}") {
                                    $content.text(fullText);
                                    $(this).text("{{__('global.read_less')}}");
                                } else {
                                    $content.text(truncatedText);
                                    $(this).text("{{__('global.read_more')}}");
                                }
                            });
                        }
                    });
                },
                error: function() {
                    console.log('Error retrieving data.');
                }
            });
            
        });

        var initialSocietyId = $('#select-society').val();
        if (initialSocietyId) {
            $('#select-society').trigger('change');
        }

        /* View all property Images Functionality */

        @can('property_management_view_images')
        
            $(document).on("click", ".btnPropetyReportAllImageView", function(e) {
                e.preventDefault();
                $('.loader-div').show();

                var url = $(this).data('href');
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        'id': id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('.popup_render_div').html(response.htmlView);
                            $('.popup_render_div .ai_images a').magnificPopup({
                                type: 'image',
                                closeBtnInside: true,
                                // closeOnContentClick: false,
                                // closeOnBgClick: false, 
                                gallery: {
                                    enabled: true
                                }
                            });
                            $('.popup_render_div .ai_images a').first().trigger('click');
                        } else {
                            toasterAlert('error', response.error);
                        }
                    },
                    error: function(res) {
                        toasterAlert('error', res.responseJSON.error);
                    },
                    complete: function(xhr) {
                        $('.loader-div').hide();
                    }
                });
            });
        
        @endcan
    });

    $(document).ready(function () {
        
    });
</script>
@endsection