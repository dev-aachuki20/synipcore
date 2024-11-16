<?php

namespace App\DataTables;

use App\Models\MaintenancePlan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenancePlanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society' /*, 'maintenance_item.category' , 'maintenance_item' */])->select('maintenance_plans.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('society.name', function ($record) {
                return ucfirst($record->society->name);
            })

            // ->editColumn('maintenance_item.category.title', function ($record) {
            //     return ucfirst($record->maintenance_item->category->title);
            // })

            ->editColumn('total_budget', function ($record) {
                return '$' . number_format($record->total_budget, 2);
            })

            // ->editColumn('maintenance_item.title', function ($record) {
            //     return ucfirst($record->maintenance_item->title);
            // })

            // ->editColumn('month', function ($record) {
            //     if (is_array($record->month)) {
            //         $months = array_map('ucwords', $record->month);
            //         return implode(', ', $months);
            //     }
            //     return '';
            // })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                if (Gate::check('maintenance_plan_view')) {
                    $actionHtml .= '<a href="' . route('admin.maintenance-plans.show', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnShowMaintenancePlan" id="btnShowMaintenancePlan" title="Show"> <i class="ri-eye-line"></i> </a>';
                }

                if (Gate::check('maintenance_plan_edit')) {
                    $actionHtml .= '<a href="' . route('admin.maintenance-plans.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditMaintenancePlan" id="btnEditMaintenancePlan" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('maintenance_plan_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.maintenance-plans.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteMaintenancePlan" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('maintenance_plans.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MaintenancePlan $model, Request $request): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->newQuery()->where('maintenance_plans.society_id', $user->society_id);
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
            ->setTableId('maintenance-plan-table')
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
                        
                        if (columnName === 'society.name') {
                            var select = $('<select multiple=\"multiple\" class=\"form-control dt-multiselect\" style=\"width: 100%;\"><option value=\"\">selectSocietyPlaceholder</option></select>')
                                .appendTo(header)
                                .on('change', function() {
                                    var selectedOptions = $(this).val();
                                    var searchValue = selectedOptions ? selectedOptions.join('|') : '';
                                    column.search(searchValue, true, false).draw();
                                });

                            // Initialize Select2
                            select.select2({
                                placeholder: selectSocietyPlaceholder,
                                width: 'resolve'
                            });

                            // Fetch all societies via AJAX
                            $.ajax({
                                url: '/admin/societies/all', 
                                method: 'GET',
                                success: function(response) {
                                    // Assume `response` contains an array of societies
                                    $.each(response, function(index, society) {
                                        select.append('<option value=\"' + society + '\">' + society + '</option>');
                                    });
                                }
                            });
                        } else if (columnName === 'created_at') {
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
        $columns[] = Column::make('society.name')->title(trans('cruds.maintenance_plan.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('year_of')->title(trans('cruds.maintenance_plan.fields.year'))->addClass('dt-year_of')->searchable(true);
        // $columns[] = Column::make('maintenance_item.category.title')->title(trans('cruds.maintenance_plan.fields.category'))->addClass('dt-category')->searchable(true);
        // $columns[] = Column::make('maintenance_item.title')->title(trans('cruds.maintenance_plan.fields.item'))->addClass('dt-item')->searchable(true);
        // $columns[] = Column::make('month')->title(trans('cruds.maintenance_plan.fields.month'))->addClass('dt-month')->searchable(true);
        $columns[] = Column::make('total_budget')->title(trans('cruds.maintenance_plan.fields.total_budget'))->addClass('dt-budget')->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.maintenance_plan.fields.created_at'))->addClass('dt-created_at')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'MaintenancePlan_' . date('YmdHis');
    }
}
