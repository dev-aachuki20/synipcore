<?php

namespace App\DataTables;

use App\Models\PropertyManagement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class PropertyManagementDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'building', 'unit', 'propertyType', 'maintenanceItem'])->select('property_management.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('property_code', function ($record) {
                return ucfirst($record->property_code);
            })

            // ->editColumn('purchase_date', function ($record) {
            //     return $record->purchase_date->format(config('constant.date_format.date'));
            // })

            ->editColumn('society.name', function ($record) {
                if ($record->society) {
                    $societyId = $record->society->id;
                    $societyName = ucfirst($record->society->name);

                    return $record->society ? '<a href="' . route('admin.reports', ['society_id' => $societyId]) . '" class="table_inner_link">' . ucfirst($societyName) . '</a>' : '';
                }
            })

            ->editColumn('building.title', function ($record) {
                return $record->building ? ucfirst($record->building->title) : '';
            })

            ->editColumn('unit.title', function ($record) {
                return $record->unit ? ucfirst($record->unit->title) : '';
            })

            ->editColumn('propertyType.title', function ($record) {
                return $record->propertyType ? ucfirst($record->propertyType->title) : '';
            })



            // ->editColumn('maintenanceItem.title', function ($record) {
            //     return $record->maintenanceItem ? ucfirst($record->maintenanceItem->title) : '';
            // })

            ->editColumn('property_item', function ($record) {
                return ucfirst($record->property_item);
            })

            ->editColumn('description', function ($record) {
                return $record->description ? '<p>' . ucfirst($record->description) . '</p>' : '';
            })

            ->editColumn('allocation', function ($record) {
                return $record->allocation ? '<p>' . ucfirst($record->allocation) . '</p>' : '';
            })

            ->editColumn('status', function ($record) {
                return $record->status ? ucfirst($record->status) : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                if (Gate::check('property_management_edit')) {
                    $actionHtml .= '<a href="' . route('admin.property-managements.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('property_management_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.property-managements.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeletePropertyManagement" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
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

            ->rawColumns(['action', 'society.name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PropertyManagement $model): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->newQuery()->where('property_management.society_id', $user->society_id);
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
            ->setTableId('property-management-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy(7)
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
        $columns[] = Column::make('property_item')->title(trans('cruds.property_management.fields.item_name'))->addClass('dt-item')->searchable(true);
        $columns[] = Column::make('propertyType.title')->title(trans('cruds.property_management.fields.property_type'))->addClass('dt-property-type')->searchable(true);
        $columns[] = Column::make('property_code')->title(trans('cruds.property_management.fields.property_code'))->addClass('dt-property-code dt-comment')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.property_management.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.property_management.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Property_Management_' . date('YmdHis');
    }
}
