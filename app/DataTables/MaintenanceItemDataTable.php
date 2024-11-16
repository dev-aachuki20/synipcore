<?php

namespace App\DataTables;

use App\Models\MaintenanceItem;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class MaintenanceItemDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->select('maintenance_items.*')))
            ->addIndexColumn()

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->editColumn('category.title', function ($record) {
                return $record->category ? ucfirst($record->category->title) : '';
            })

            ->editColumn('description', function ($record) {
                return '<div class="two-line-dt-description">' . ($record->description ?? '') . '</div>';
            })

            ->editColumn('duration', function ($record) {
                return config('constant.durations')[$record->duration] ?? '';
            })

            ->editColumn('budget', function ($record) {
                return '$' . $record->budget ?? 0;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('maintenance_item_edit')) {
                    $actionHtml .= '<a href="' . route('admin.maintenance-items.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditMaintenanceItem" id="btnEditMaintenanceItem" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('maintenance_item_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.maintenance-items.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteMaintenanceItem" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('title', function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            })

            ->filterColumn('category.title', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })
            ->rawColumns(['action', 'description']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(MaintenanceItem $model, Request $request): QueryBuilder
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
            ->setTableId('maintenance-item-table')
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
        $columns[] = Column::make('title')->title(trans('cruds.maintenance_item.fields.title'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('category.title')->title(trans('cruds.category.title_singular'))->addClass('dt-category')->searchable(true);
        $columns[] = Column::make('description')->title(trans('cruds.maintenance_item.fields.description'))->addClass('dt-description')->searchable(true);
        $columns[] = Column::make('duration')->title(trans('cruds.maintenance_item.fields.duration'))->addClass('dt-duration')->searchable(true);
        $columns[] = Column::make('budget')->title(trans('cruds.maintenance_item.fields.budget'))->addClass('dt-budget')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'MaintenanceItem_' . date('YmdHis');
    }
}
