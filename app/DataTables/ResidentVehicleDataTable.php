<?php

namespace App\DataTables;

use App\Models\ResidentVehicle;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResidentVehicleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'building', 'unit'])->select('resident_vehicles.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
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

            ->editColumn('status', function ($record) {
                $options = config('constant.status_type.vehicle_status');

                $selected = isset($record) && !empty($record->status) ? $record->status : '';

                $html = '<div class="TableSelect">
                            <select class="statusUpdate" data-record-id="' . $record->uuid . '">';

                foreach ($options as $value => $label) {
                    $isSelected = ($value == $selected) ? ' selected' : '';
                    $html .= '<option value="' . $value . '"' . $isSelected . ' data-vehicle_id="' . $record->uuid . '" data-vehicle_status="' . $value . '">' . $label . '</option>';
                }

                $html .= '</select></div>';

                return $html;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('resident_vehicle_edit')) {
                    $actionHtml .= '<a href="' . route('admin.resident-vehicles.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditResidentVehicle" id="btnEditResidentVehicle" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('resident_vehicle_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.resident-vehicles.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteResidentVehicle" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('resident_vehicles.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%$keyword%");
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ResidentVehicle $model, Request $request): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->newQuery()->where('resident_vehicles.society_id', $user->society_id);
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
            ->setTableId('resident-vehicle-table')
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
        $columns[] = Column::make('vehicle_number')->title(trans('cruds.resident_vehicle.fields.vehicle_number'))->addClass('dt-name')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.resident_vehicle.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('building.title')->title(trans('cruds.resident_vehicle.fields.building'))->addClass('dt-building')->searchable(true);
        $columns[] = Column::make('unit.title')->title(trans('cruds.resident_vehicle.fields.unit'))->addClass('dt-unit')->searchable(true);
        $columns[] = Column::make('parking_slot_no')->title(trans('cruds.resident_vehicle.fields.parking_slot'))->addClass('dt-parking-slote')->searchable(true);
        $columns[] = Column::make('status')->title(trans('cruds.resident_vehicle.fields.status'))->addClass('dt-status')->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.resident_vehicle.fields.created_at'))->addClass('dt-created_at')->searchable(true);
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
