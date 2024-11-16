@extends('layouts.admin')

@section('title', trans('cruds.menus.transaction_reports'))

@section('custom_css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('main-content')
<div class="row mt-3">
    <div class="col-xxl-3 col-sm-6">
        <div class="card widget-flat">
            <div class="card-body">
                <a class="dashboard-card" href="{{ route('admin.transactions.index') }}">
                    <div class="dashboard-card-icon">
                        <i class="ri-group-line widget-icon"></i>
                    </div>
                    <h2 class="blue-shade">{{ isset($transactionCount) ? $transactionCount : 0 }}</h2>
                    <h6 title="Customers">@lang('cruds.menus.transaction')</h6>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xxl-12 static-inputs date-range-container">
        <div id="filterContainer-daterange" class="GraphRange">
            <div>
                <h3>{{ trans('cruds.dashboard.fields.transaction_alert_graph') }}</h3>
            </div>
            <div>
                <label for="dateRangePicker">{{ trans('cruds.dashboard.fields.filter_by_date') }}</label>
                <input class="form-control" type="text" id="dateRangePicker" name="dateRangePicker" />
            </div>
        </div>
        <div id="transactionChartContainer">
            <canvas id="transactionChart" width="200" height="70"></canvas>
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
        var ctx1 = document.getElementById('transactionChart').getContext('2d');
        var transactionChart = new Chart(ctx1, {
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
                            min: 0,
                            callback: function(value, index, values) {
                                // Show only integer values on the Y-axis
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: ''
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

        // For new registration.
        // Initialize date range picker with predefined ranges
        $('#dateRangePicker').daterangepicker({
            startDate: moment().subtract(6, 'days').startOf('day'),
            endDate: moment().endOf('day'),
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
                url: "{{ route('admin.transaction-reports') }}",
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    timePeriod: timePeriod,
                },
                success: function (response) {
                    transactionChart.data.labels = response.labels;
                    transactionChart.data.datasets[0].data = response.datasets[0].data;
                    transactionChart.update();
                }
            });
        }).on('cancel.daterangepicker', function (ev, picker) {            
            $(this).val('');
        }).val(moment().startOf('day').subtract(6, 'day').format('DD-MM-YYYY') + ' - ' + moment().endOf('day').format('DD-MM-YYYY'));


        
    });
</script>
@endsection