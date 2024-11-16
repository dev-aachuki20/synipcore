<?php

namespace App\DataTables;

use App\Models\AiBoxAlert;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AiBoxNotificationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'camera'])->select('ai_box_alerts.*')))
            ->addIndexColumn()

            ->editColumn('event_id', function ($record) {
                return isset($record->notification_data['Event_ID']) ? ucfirst($record->notification_data['Event_ID']) : '';
            })


            ->editColumn('event_code', function ($record) {
                return isset($record->notification_data['Evenet_Code']) ? ucfirst($record->notification_data['Evenet_Code']) : '';
            })

            ->editColumn('event_name', function ($record) {
                $eventCode = $record->notification_data['Evenet_Code'] ?? '';
                $events = config('constant.aibox_notification.api_code');
                return isset($events[$eventCode]) ? ucfirst($events[$eventCode]['event_name']) : '';
            })

            ->editColumn('society.name', function ($record) {
                return $record->society ? ucfirst($record->society->name) : '';
            })

            ->editColumn('camera.lacated_at', function ($record) {
                return $record->camera ? $record->camera->lacated_at : '';
            })

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('aibox_notification_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.aibox.show', $record->id) . '" class="btn btn-outline-info btn-sm btnViewAiboxDetails" title="Show Details"> <i class="ri-eye-line"></i> </a>';
                }

                if (Gate::check('aibox_notification_view_image')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-id="' . $record->id . '" data-href="' . route('admin.aibox.viewImage', $record->id) . '" class="btn btn-outline-info btn-sm btnViewAiboxImageDetails" title="Event Image"> <i class="ri-image-line"></i> </a>';
                }

                if (Gate::check('aibox_notification_view_video')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-id="' . $record->id . '" data-href="' . route('admin.aibox.viewVideo', $record->id) . '" class="btn btn-outline-danger btn-sm btnViewAiboxVideoDetails" title="Event Video"> <i class="ri-video-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })

            ->filterColumn('event_id', function ($query, $keyword) {
                $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(notification_data, '$.Event_ID'))) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })

            ->filterColumn('event_code', function ($query, $keyword) {
                $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(notification_data, '$.Evenet_Code'))) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })

            ->filterColumn('event_name', function ($query, $keyword) {
                // Get the event codes and names from the configuration
                $events = config('constant.aibox_notification.api_code');

                // Search through event codes to find matching codes for the provided keyword
                $matchedCodes = array_keys(array_filter($events, function ($event) use ($keyword) {
                    return stripos($event['event_name'], $keyword) !== false;
                }));

                // Modify the query to filter using the matched event codes
                $query->where(function ($query) use ($matchedCodes) {
                    foreach ($matchedCodes as $code) {
                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(notification_data, '$.Evenet_Code')) = ?", [$code]);
                    }
                });
            })

            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(AiBoxAlert $model): QueryBuilder
    {
        return $model->newQuery();
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('aibox-notification-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->lengthMenu([
                [10, 25, 50, 100, /*-1*/],
                [10, 25, 50, 100, /*'All'*/]
            ])->parameters([
                'initComplete' => "function() {
                    var api = this.api();
                    api.columns().every(function() {
                        var column = this;
                        var columnName = column.dataSrc();
                        var header = $(column.header());
                        if (columnName === 'created_at') { // Replace 'created_at' with your date column name
                            var input = $('<input type=\"text\" placeholder=\"" . __('cruds.datatable.select_date') . "\" class=\"dt-daterange\" readonly/>')
                                .appendTo(header)
                                .daterangepicker({
                                    autoUpdateInput: false,
                                    maxDate: moment().endOf('day'), 
                                    locale: {
                                        cancelLabel: 'Clear'
                                    }
                                });

                            input.on('apply.daterangepicker', function(ev, picker) {
                                var startDate = picker.startDate.format('YYYY-MM-DD');
                                var endDate = picker.endDate.format('YYYY-MM-DD');
                                $(this).val(picker.startDate.format('DD MMM YYYY') + ' - ' + picker.endDate.format('DD MMM YYYY'));
                                column.search(startDate + ' - ' + endDate).draw();

                            });

                            input.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                                column.search('').draw();
                            });
                            // Prevent sorting on the searched column
                            input.on('click', function(e) {
                                e.stopPropagation();
                            });
                        } else {
                            var input = $('<input type=\"search\" placeholder=\"" . __('cruds.datatable.search') . "\" />')
                            .appendTo(header)
                            .on('input', function() {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });

                            // Prevent sorting on the searched column
                            input.on('click', function(e) {
                                e.stopPropagation();
                            });
                        }
                    });
                }",
                "responsive" => true,
                "scrollCollapse" => true,
                'autoWidth' => true,
                'language' => [
                    "sZeroRecords" => __('cruds.datatable.data_not_found'),
                    "sProcessing" => __('cruds.datatable.processing'),
                    "sLengthMenu" => __('cruds.datatable.show') . " _MENU_ " . __('cruds.datatable.entries'),
                    "sInfo" =>  config('app.locale') == 'en' ?
                        __('message.showing') . " _START_ " . __('message.to') . " _END_ " . __('message.of') . " _TOTAL_ " . __('message.records') :
                        __('message.showing') . "_TOTAL_" . __('message.to') . __('message.of') . "_START_-_END_" . __('message.records'),
                    "sInfoEmpty" => __('message.showing') . " 0 " . __('message.to') . " 0 " . __('message.of') . " 0 " . __('message.records'),
                    "search" => __('cruds.datatable.search'),
                    "sEmptyTable" => __('cruds.datatable.data_not_found'),
                    "paginate" => [
                        "first" => __('message.first'),
                        "last" => __('message.last'),
                        "next" =>  __('cruds.datatable.next'),
                        "previous" =>  __('cruds.datatable.previous'),
                    ],
                    "autoFill" => [
                        "cancel" => __('message.cancel'),
                    ],

                ],
                'pagingType' => 'simple_numbers',
                'columnDefs' => [
                    ['targets' => '_all', 'orderable' => false]
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [];

        $columns[] = Column::make('DT_RowIndex')->title(trans('global.sno'))->orderable(false)->searchable(false);
        $columns[] = Column::computed('event_id')->title(trans('cruds.aibox_notification.fields.event_id'))->searchable(true);
        $columns[] = Column::computed('event_code')->title(trans('cruds.aibox_notification.fields.event_code'))->searchable(true);
        $columns[] = Column::computed('event_name')->title(trans('cruds.aibox_notification.fields.event_name'))->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.aibox_notification.fields.society'))->searchable(true);
        $columns[] = Column::make('camera.lacated_at')->title(trans('cruds.aibox_notification.fields.location'))->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.aibox_notification.fields.created_at'));
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col ai_action_row');
        return $columns;
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Aibox_' . date('YmdHis');
    }
}
