<?php

namespace App\DataTables;

use App\Models\ComplaintType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class ComplaintTypeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->select('complaint_types.*')))
            ->addIndexColumn()

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('complaint_type_edit')) {
                    $actionHtml .= '<a href="' . route('admin.complaint-types.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditComplaintType" id="btnEditComplaintType" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('complaint_type_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.complaint-types.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteComplaintType" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')
            ->filterColumn('title', function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ComplaintType $model): QueryBuilder
    {
        return $model->newQuery()->orderByDesc('sort_order');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('complaint-type-table')
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
                        .on('keyup change', function() {
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
        $columns[] = Column::make('title')->title(trans('cruds.complaint_type.fields.title'))->addClass('dt-username')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ComplaintType_' . date('YmdHis');
    }
}
