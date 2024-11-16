@extends('layouts.admin')

@section('title', trans('global.dashboard'))

@section('custom_css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('main-content')
<div class="page-title-box">
    <h4 class="page-title">{{ trans('global.welcome') }}</h4>
</div>
<div class="row">
    <div class="col-xxl-3 col-sm-6">
        <div class="card widget-flat">
            <div class="card-body">
                <a class="dashboard-card" href="{{ route('admin.users.index') }}">
                    <div class="dashboard-card-icon">
                        <i class="ri-group-line widget-icon"></i>
                    </div>
                    <h2 class="blue-shade">{{ isset($userCount) ? $userCount : 0 }}</h2>
                    <h6 title="Customers">@lang('cruds.user.title')</h6>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xxl-6 static-inputs date-range-container">
        <div id="filterContainer-daterange" class="GraphRange">
            <div>
                <h3>{{ trans('cruds.dashboard.fields.alert_graph') }}</h3>
            </div>
            <div>
                <label for="dateRangePicker">{{ trans('cruds.dashboard.fields.filter_by_date') }}</label>
                <input class="form-control" type="text" id="dateRangePicker" name="dateRangePicker" />
            </div>
        </div>
        <div id="newRegistrationsChartContainer">
            <canvas id="newRegistrationsChart" width="200" height="70"></canvas>
        </div>
    </div>
    <div class="col-lg-12 col-xxl-6 static-inputs date-range-container">
        <div id="filterContainer-daterange" class="GraphRange">
            <div>
                <h3>{{ trans('cruds.dashboard.fields.activity_alert_graph') }}</h3>
            </div>
            <div>
                <label for="dateRangePicker2">{{ trans('cruds.dashboard.fields.filter_by_date') }}</label>
                <input class="form-control" type="text" id="dateRangePicker2" name="dateRangePicker2" />
            </div>
        </div>
        <div id="recentActivityChartContainer">
            <canvas id="recentActivityChart" width="200" height="70"></canvas>
        </div>
    </div>
</div>


@endsection

@section('custom_js')
<script src="{{ asset('backend/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('backend/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('backend/vendor/daterangepicker/daterangepicker.js') }}"></script>


<script>
    $(document).ready(function () {
        // new RegistrationsChart Chart Setup
        var ctx1 = document.getElementById('newRegistrationsChart').getContext('2d');
        var newRegistrationsChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    data: {!! json_encode($datasets[0]['data'] ?? []) !!},
                    borderColor: "{{ $datasets[0]['borderColor'] ?? '#000'}}",
                    borderWidth: 3,
                    fill: false,
                    tension: 0.5,
                    pointBorderColor: "{{ $datasets[0]['pointBorderColor'] ?? '#000'}}",
                    pointBackgroundColor: "{{ $datasets[0]['pointBackgroundColor'] ?? '#000'}}",
                    pointHoverBackgroundColor: "{{ $datasets[0]['pointHoverBackgroundColor'] ?? '#000'}}",
                    pointBorderWidth: 6,
                    pointHoverRadius: 6,
                    pointHoverBorderColor: "{{ $datasets[0]['pointHoverBorderColor'] ?? '#000'}}",
                    pointHoverBorderWidth: 3,
                    pointRadius: 1,
                    pointHitRadius: 30
                }]
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        title: {
                            display: true,
                            text: '{{ $datasets[0]['yAxisText'] ?? '' }}'
                        },
                        ticks: {
                            beginAtZero: true,
                            //color: '#b7b7b7',
                            callback: function(value, index, values) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        // grid: {
                        //     color: '#b7b7b7'
                        // }
                    },
                    x: {
                        title: {
                            display: true,
                            text: ''
                        },
                        // grid: {
                        //     color: '#b7b7b7'
                        // }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const datasetLabel = tooltipItem.dataset.label || '';
                                const value = tooltipItem.raw;

                                return `${datasetLabel}  ${value}`;
                            }
                        }
                    },
                }
            }
        });

        // For new registration.
        // Initialize date range picker with predefined ranges
        $('#dateRangePicker').daterangepicker({
            startDate: moment().subtract(6, 'days').startOf('day'),
            endDate: moment().endOf('day'),
            opens: 'left',
            ranges: {
                '{{ __("cruds.dashboard.labels.day") }}': [moment(), moment()],
                '{{ __("cruds.dashboard.labels.week") }}': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                '{{ __("cruds.dashboard.labels.month") }}': [moment().startOf('month'), moment().endOf('month')],
            },
            autoUpdateInput: true,
            alwaysShowCalendars: true,
            // autoApply: true,
            locale: {
              cancelLabel: '{{ __("cruds.dashboard.clear") }}',
              applyLabel: '{{ __("cruds.dashboard.apply") }}',
              daysOfWeek: [
                '{{ __("cruds.dashboard.days_of_week.sunday") }}',
                '{{ __("cruds.dashboard.days_of_week.monday") }}',
                '{{ __("cruds.dashboard.days_of_week.tuesday") }}',
                '{{ __("cruds.dashboard.days_of_week.wednesday") }}',
                '{{ __("cruds.dashboard.days_of_week.thursday") }}',
                '{{ __("cruds.dashboard.days_of_week.friday") }}',
                '{{ __("cruds.dashboard.days_of_week.saturday") }}'
              ],
              monthNames: [
                '{{ __("cruds.dashboard.month_names.january") }}',
                '{{ __("cruds.dashboard.month_names.february") }}',
                '{{ __("cruds.dashboard.month_names.march") }}',
                '{{ __("cruds.dashboard.month_names.april") }}',
                '{{ __("cruds.dashboard.month_names.may") }}',
                '{{ __("cruds.dashboard.month_names.june") }}',
                '{{ __("cruds.dashboard.month_names.july") }}',
                '{{ __("cruds.dashboard.month_names.august") }}',
                '{{ __("cruds.dashboard.month_names.september") }}',
                '{{ __("cruds.dashboard.month_names.october") }}',
                '{{ __("cruds.dashboard.month_names.november") }}',
                '{{ __("cruds.dashboard.month_names.december") }}'
              ],
              firstDay: 0,
              customRangeLabel: ''
            }
        });
        
        // Apply event: Ensure the input field displays the correct date range after selection
        $('#dateRangePicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            // Get selected date range
            const startDate = picker.startDate.format('DD-MM-YYYY');
            const endDate = picker.endDate.format('DD-MM-YYYY');
            // Determine time period
            const timePeriod = startDate === endDate ? 'day' : 'custom range';
            // Fetch updated data via AJAX
            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    timePeriod: timePeriod,
                },
                success: function (response) {
                    // Update the chart with the new data
                    newRegistrationsChart.data.labels = response.labels;
                    newRegistrationsChart.data.datasets[0].data = response.datasets[0].data;
                    newRegistrationsChart.update();
                    // var totalTextLabel = @json(trans('cruds.dashboard.fields.total'));
                    // var totalLabelLabel = @json(trans('cruds.dashboard.fields.count'));
                    // $('.totalCount').html('<b>' + totalTextLabel + ' ' + totalLabelLabel + ':</b> ' + (data.total || 0));
                }
            });
        }).on('cancel.daterangepicker', function (ev, picker) {            
            $(this).val('');
        }).val(moment().startOf('day').subtract(6, 'day').format('DD-MM-YYYY') + ' - ' + moment().endOf('day').format('DD-MM-YYYY'));




        


        // Fetch and update Recent Activity Chart Setup
        var ctx2 = document.getElementById('recentActivityChart').getContext('2d');
        var recentActivityChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: {!! json_encode($activityLabels) !!},
                datasets: [{
                    data: {!! json_encode($activityDatasets[0]['data'] ?? []) !!},
                    borderColor: "{{ $activityDatasets[0]['borderColor'] ?? '#000' }}",
                    borderWidth: 3,
                    fill: false,
                    tension: 0.5,
                    pointBorderColor: "{{ $activityDatasets[0]['pointBorderColor'] ?? '#000' }}",
                    pointBackgroundColor: "{{ $activityDatasets[0]['pointBackgroundColor'] ?? '#000' }}",
                    pointHoverBackgroundColor: "{{ $activityDatasets[0]['pointHoverBackgroundColor'] ?? '#000' }}",
                    pointBorderWidth: 6,
                    pointHoverRadius: 6,
                    pointHoverBorderColor: "{{ $activityDatasets[0]['pointHoverBorderColor'] ?? '#000' }}",
                    pointHoverBorderWidth: 3,
                    pointRadius: 1,
                    pointHitRadius: 30
                }]
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        title: {
                            display: true,
                            text: '{{ $activityDatasets[0]['yAxisText'] ?? '' }}'
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '',
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const datasetLabel = tooltipItem.dataset.label || '';
                                const value = tooltipItem.raw;

                                return `${datasetLabel}  ${value}`;
                            }
                        }
                    },
                }
            }
        });

        // For recent activity.
        // Initialize date range picker with predefined ranges
        $('#dateRangePicker2').daterangepicker({
            startDate: moment().subtract(6, 'days').startOf('day'),
            endDate: moment().endOf('day'),
            opens: 'left',
            ranges: {
                '{{ __("cruds.dashboard.labels.day") }}': [moment(), moment()],
                '{{ __("cruds.dashboard.labels.week") }}': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                '{{ __("cruds.dashboard.labels.month") }}': [moment().startOf('month'), moment().endOf('month')],
            },
            autoUpdateInput: true,
            alwaysShowCalendars: true,
            // autoApply: true,
            locale: {
              cancelLabel: '{{ __("cruds.dashboard.clear") }}',
              applyLabel: '{{ __("cruds.dashboard.apply") }}',
              daysOfWeek: [
                '{{ __("cruds.dashboard.days_of_week.sunday") }}',
                '{{ __("cruds.dashboard.days_of_week.monday") }}',
                '{{ __("cruds.dashboard.days_of_week.tuesday") }}',
                '{{ __("cruds.dashboard.days_of_week.wednesday") }}',
                '{{ __("cruds.dashboard.days_of_week.thursday") }}',
                '{{ __("cruds.dashboard.days_of_week.friday") }}',
                '{{ __("cruds.dashboard.days_of_week.saturday") }}'
              ],
              monthNames: [
                '{{ __("cruds.dashboard.month_names.january") }}',
                '{{ __("cruds.dashboard.month_names.february") }}',
                '{{ __("cruds.dashboard.month_names.march") }}',
                '{{ __("cruds.dashboard.month_names.april") }}',
                '{{ __("cruds.dashboard.month_names.may") }}',
                '{{ __("cruds.dashboard.month_names.june") }}',
                '{{ __("cruds.dashboard.month_names.july") }}',
                '{{ __("cruds.dashboard.month_names.august") }}',
                '{{ __("cruds.dashboard.month_names.september") }}',
                '{{ __("cruds.dashboard.month_names.october") }}',
                '{{ __("cruds.dashboard.month_names.november") }}',
                '{{ __("cruds.dashboard.month_names.december") }}'
              ],
              firstDay: 0,
              customRangeLabel: ''
            }
        });

        // Apply event: Ensure the input field displays the correct date range after selection
        $('#dateRangePicker2').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));

            // Get selected date range
            const startDate = picker.startDate.format('DD-MM-YYYY');
            const endDate = picker.endDate.format('DD-MM-YYYY');

            // Determine time period
            const timePeriod = startDate === endDate ? 'day' : 'custom range';

            // Fetch updated data via AJAX
            

            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    timePeriod: timePeriod
                },
                success: function (response) {
                    // Check if response contains necessary data
                    if (response.activityLabels && response.activityDatasets && response.activityDatasets.length > 0) {
                        recentActivityChart.data.labels = response.activityLabels;
                        recentActivityChart.data.datasets[0].data = response.activityDatasets[0].data;
                        recentActivityChart.update();
                    } else {
                        console.error('Unexpected response format:', response);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                }
            });

        }).on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        }).val(moment().startOf('day').subtract(6, 'day').format('DD-MM-YYYY') + ' - ' + moment().endOf('day').format('DD-MM-YYYY'));
        
    });
</script>
@endsection