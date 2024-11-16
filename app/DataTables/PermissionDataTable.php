<?php

namespace App\DataTables;

use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class PermissionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn();

        // ->addColumn('action', function ($record) {
        //     $actionHtml = '';

        //     if (Gate::check('permission_edit')) {
        //         $actionHtml .= '<a href="' . route('admin.permissions.edit', $record->id) . '" class="btn btn-outline-primary btn-sm btnEditPermission" id="btnEditPermission" title="Edit"> <i class="ri-pencil-line"></i> </a>';
        //     }

        //     if (Gate::check('permission_delete')) {
        //         $actionHtml .= '<a href="javascript:void(0);" class="btn btn-outline-danger btn-sm deletePermissionBtn" data-href="' . route('admin.permissions.destroy', $record->id) . '" title="Delete"><i class="ri-delete-bin-line"></i></a>';
        //     }
        //     return $actionHtml;
        // })
        // ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */

    public function query(Permission $model, Request $request): QueryBuilder
    {
        if(isset($request->order[0]['column']) && $request->order[0]['column'] > 0){
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
        $orderByColumn = 1;
        return $this->builder()
            ->setTableId('permission-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy($orderByColumn)
            ->selectStyleSingle()
            ->lengthMenu([
                [10, 25, 50, 100, /*-1*/],
                [10, 25, 50, 100, /*'All'*/]
            ])
            ->parameters([
                'searching' => true,
                'initComplete' => "function() {
                    var api = this.api();
                    var searchRow = $('<tr>').addClass('search-row');
    
                    api.columns().every(function() {
                        var column = this;
                        var header = $(column.header());
                        var columnIndex = column.index();
    
                        if (header.hasClass('dt-title')) {
                            var input = $('<input type=\"search\" placeholder=\"" . __('cruds.datatable.search') . "\" style=\"width: 100%;\" />')
                                .on('input', function() {
                                    column.search($(this).val()).draw();
                                });
    
                            input.val(column.search());
    
                            $('<td>').append(input).appendTo(searchRow);
                        } else {
                            $('<td>').appendTo(searchRow);
                        }
                    });
    
                    $(api.table().header()).append(searchRow);
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
        $columns[] = Column::make('title')->title(trans('cruds.permission.fields.permission'))->addClass('dt-title border-bnone')->searchable(true);
        // $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Permissions_' . date('YmdHis');
    }
}
