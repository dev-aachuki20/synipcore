<?php

namespace App\DataTables;

use App\Models\DeliveryType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class DeliveryTypeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->select('delivery_types.*')))
            ->addIndexColumn()

            // ->editColumn('title', function ($record) {
            //     if ($record->title == 0) {
            //         return isset($record->other) ? ucfirst($record->other) : '';
            //     } else {
            //         $titles = config('constant.delivery_type');
            //         return isset($titles[$record->title]) ? ucfirst($titles[$record->title]) : '';
            //     }
            // })

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->editColumn('notify_user', function ($record) {
                return config('constant.notify_user')[$record->notify_user] ?? '';
            })

            ->editColumn('description', function ($record) {
                return '<div class="two-line-dt-description">' . ($record->description ?? '') . '</div>';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('delivery_type_edit')) {
                    $actionHtml .= '<a href="' . route('admin.delivery-types.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditDeliveryType" id="btnEditDeliveryType" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('delivery_type_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.delivery-types.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteDeliveryType" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            // ->filterColumn('title', function ($query, $keyword) {
            //     $titles = config('constant.delivery_type');
            //     $query->where(function ($query) use ($keyword, $titles) {
            //         $query->where(function ($query) use ($keyword, $titles) {
            //             foreach ($titles as $key => $value) {
            //                 if (stripos($value, $keyword) !== false) {
            //                     $query->orWhere('title', $key);
            //                 }
            //             }
            //         })
            //             ->orWhere('other', 'like', "%$keyword%");
            //     });
            // })


            ->rawColumns(['action', 'description']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DeliveryType $model, Request $request): QueryBuilder
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
            ->setTableId('delivery-type-table')
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
                        var header = $(column.header());
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
        $columns[] = Column::make('title')->title(trans('cruds.delivery_type.fields.title'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('notify_user')->title(trans('cruds.delivery_type.fields.notify_user'))->addClass('dt-notify_user')->searchable(true);
        $columns[] = Column::make('description')->title(trans('cruds.delivery_type.fields.description'))->addClass('dt-description')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DeliveryType_' . date('YmdHis');
    }
}
