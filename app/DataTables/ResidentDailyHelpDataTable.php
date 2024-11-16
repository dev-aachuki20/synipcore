<?php

namespace App\DataTables;

use App\Models\ResidentDailyHelp;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResidentDailyHelpDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['resident', 'society', 'building', 'unit'])->select('resident_daily_helps.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('name', function ($record) {
                return isset($record) && !empty($record->name) ? ucwords($record->name) : '';
            })

            ->editColumn('help_type', function ($record) {
                return isset($record) && !empty($record->help_type) ? ucfirst($record->help_type) : '';
            })

            ->editColumn('society.name', function ($record) {
                return isset($record) && !empty($record->society) ? ucfirst($record->society->name) : '';
            })

            ->editColumn('building.title', function ($record) {
                return isset($record) && !empty($record->building) ? ucfirst($record->building->title) : '';
            })

            ->editColumn('unit.title', function ($record) {
                return isset($record) && !empty($record->unit) ? ucfirst($record->unit->title) : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('resident_daily_help_edit')) {
                    $actionHtml .= '<a href="' . route('admin.resident-daily-helps.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditDailyHelp" id="btnEditDailyHelp" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('resident_daily_help_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.resident-daily-helps.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteDailyHelp" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('resident_daily_helps.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ResidentDailyHelp $model, Request $request): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->newQuery()->where('resident_daily_helps.society_id', $user->society_id);
        } else {
            return $model->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('resident-daily-help-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
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
                        
                        if (columnName === 'created_at') {
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
        $columns[] = Column::make('name')->title(trans('cruds.resident_daily_help.fields.name'))->addClass('dt-name')->searchable(true);
        $columns[] = Column::make('phone_number')->title(trans('cruds.resident_daily_help.fields.contact'))->addClass('dt-contact')->searchable(true);
        $columns[] = Column::make('help_type')->title(trans('cruds.resident_daily_help.fields.help_type'))->addClass('dt-help-type')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.resident_daily_help.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('building.title')->title(trans('cruds.resident_daily_help.fields.building'))->addClass('dt-building')->searchable(true);
        $columns[] = Column::make('unit.title')->title(trans('cruds.resident_daily_help.fields.unit'))->addClass('dt-unit')->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.resident_daily_help.fields.created_at'))->addClass('dt-created_at')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ResidentDailyHelp_' . date('YmdHis');
    }
}
