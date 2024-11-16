<?php

namespace App\DataTables;

use App\Models\DeliveryManagement;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;


class DeliveryManagementDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'building', 'unit', 'type', 'user'])->select('delivery_management.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('subject', function ($record) {
                return $record->subject ? ucfirst($record->subject) : '';
            })

            ->editColumn('society.name', function ($record) {
                return $record->society ? ucfirst($record->society->name) : '';
            })

            ->editColumn('delivery_type_id', function ($record) {
                return $record->type ? ucfirst($record->type->title) : '';
            })

            ->editColumn('user.name', function ($record) {
                return $record->user ? ucfirst($record->user->name) : '';
            })

            ->editColumn('status', function ($record) {
                // $options = config('constant.status_type.delivery_status');
                return isset($record) && !empty($record->status) ? config('constant.status_type.delivery_status')[$record->status] : '';
            })

            ->editColumn('message', function ($record) {
                return '<div class="two-line-dt-description">' . (ucfirst($record->message) ?? '') . '</div>';
            })

            ->editColumn('notes', function ($record) {
                return $record->notes ? ucfirst($record->notes) : '';
            })

            // ->editColumn('status', function ($record) {
            //     $options = config('constant.status_type.delivery_status');

            //     $selected = isset($record) && !empty($record->status) ? $record->status : '';

            //     $html = '<div class="TableSelect">
            //                 <select class="statusUpdate" data-record-id="' . $record->uuid . '">';

            //     foreach ($options as $value => $label) {
            //         $isSelected = ($value == $selected) ? ' selected' : '';
            //         $html .= '<option value="' . $value . '"' . $isSelected . ' data-delivery_mang_id="' . $record->uuid . '" data-delivery_mang_status="' . $value . '">' . $label . '</option>';
            //     }

            //     $html .= '</select></div>';

            //     return $html;
            // })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('delivery_management_edit')) {
                    $actionHtml .= '<a href="' . route('admin.delivery-managements.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditDeliveryManagement" id="btnEditDeliveryManagement" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('delivery_management_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.delivery-managements.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteDeliveryManagement" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })

            ->filterColumn('delivery_type_id', function ($query, $keyword) {
                $query->whereHas('type', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%$keyword%");
            })
            ->setRowId('id')


            ->rawColumns(['action', 'message' /*, 'status' */]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DeliveryManagement $model, Request $request): QueryBuilder
    {
        // dd($request->order[0]['column']);
        $user = auth()->user();
        $query = $model->newQuery();
        if (!isset($request->order[0]['column']) || $request->order[0]['column'] == 9) {
            if ($user->is_sub_admin) {
                return $query->orderByDesc('created_at')->where('delivery_management.society_id', $user->society_id);
            } else {
                return $query->orderByDesc('created_at');
            }
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('delivery-management-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy(9)
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
        $columns[] = Column::make('subject')->title(trans('cruds.delivery_management.fields.subject'))->addClass('dt-subject')->searchable(true);
        $columns[] = Column::make('delivery_type_id')->title(trans('cruds.delivery_management.fields.delivery_type'))->addClass('dt-delivery_type')->searchable(true);
        $columns[] = Column::make('user.name')->title(trans('cruds.delivery_management.fields.actor_name'))->addClass('dt-actor_name')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.delivery_management.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('message')->title(trans('cruds.delivery_management.fields.message'))->addClass('dt-message')->searchable(true);
        $columns[] = Column::make('notes')->title(trans('cruds.delivery_management.fields.note'))->addClass('dt-notes')->searchable(true);
        $columns[] = Column::make('status')->title(trans('cruds.delivery_management.fields.status'));
        $columns[] = Column::make('created_at')->title(trans('cruds.delivery_management.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DeliveryManagement_' . date('YmdHis');
    }
}
