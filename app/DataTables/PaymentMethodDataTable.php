<?php

namespace App\DataTables;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class PaymentMethodDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->select('payment_methods.*')))
            ->addIndexColumn()

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->editColumn('method_type', function ($record) {
                $types = config('constant.status_type.payment_method_type');
                return isset($types[$record->method_type]) ? ucfirst($types[$record->method_type]) : '';
            })

            ->editColumn('status', function ($record) {
                $types = config('constant.status_type.payment_method_status');
                return isset($types[$record->status]) ? ucfirst($types[$record->status]) : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('payment_method_edit')) {
                    $actionHtml .= '<a href="' . route('admin.payment-methods.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditPaymentMethod" id="btnEditPaymentMethod" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('payment_method_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.payment-methods.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeletePaymentMethod" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('title', function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            })

            ->filterColumn('method_type', function ($query, $keyword) {
                $types = config('constant.status_type.payment_method_type');
                $filteredKeys = array_keys(array_filter($types, function ($value) use ($keyword) {
                    return stripos($value, $keyword) !== false; // Case-insensitive partial match
                }));
                if (!empty($filteredKeys)) {
                    $query->whereIn('method_type', $filteredKeys);
                }
            })

            ->filterColumn('status', function ($query, $keyword) {
                $statuses = config('constant.status_type.payment_method_status');
                $filteredKeys = array_keys(array_filter($statuses, function ($value) use ($keyword) {
                    return stripos($value, $keyword) !== false; // Case-insensitive partial match
                }));
                if (!empty($filteredKeys)) {
                    $query->whereIn('status', $filteredKeys);
                }
            })

            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PaymentMethod $model, Request $request): QueryBuilder
    {
        if (isset($request->order[0]['column']) && $request->order[0]['column'] > 0) {
            return $model->newQuery();
        } else {
            return $model->newQuery()->orderByDesc('id');
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('payment-method-table')
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
        $columns[] = Column::make('title')->title(trans('cruds.payment_method.fields.title'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('method_type')->title(trans('cruds.payment_method.fields.type'))->addClass('dt-type');
        $columns[] = Column::make('status')->title(trans('cruds.payment_method.fields.status'));
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PaymentMethod_' . date('YmdHis');
    }
}
