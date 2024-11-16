<?php

namespace App\DataTables;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class LocationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['parentLocation'])->select('locations.*')))
            ->addIndexColumn()

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->editColumn('parentLocation.title', function ($record) {
                return $record->parentLocation ? ucfirst($record->parentLocation->title) : 'Root';
            })

            ->editColumn('scope_id', function ($record) {
                return $record->scope_id ? config('constant.location_scope')[$record->scope_id] : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                /* if (Gate::check('location_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.locations.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewLocation" title="Show"> <i class="ri-eye-line"></i> </a>';
                } */

                if (Gate::check('location_edit')) {
                    $actionHtml .= '<a href="' . route('admin.locations.edit', $record->uuid) . '" data-href="' . route('admin.locations.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditLocation" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('location_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.locations.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteLocation" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('title', function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            })

            ->filterColumn('parentLocation.title', function ($query, $keyword) {
                $isRoot  = false;
                if (Str::contains('root', strtolower($keyword))) {
                    $isRoot  = true;
                }
                $query->whereHas('parentLocation', function($q) use($keyword, $isRoot ){
                    $q->where('title', 'like', "%$keyword%");
                });
                if($isRoot ){
                    $query->orWhereNull('parent_id');
                }
            })

            ->filterColumn('scope_id', function ($query, $keyword) {
                $scopeIdSearch  = null;
                if (Str::contains('default', strtolower($keyword))) {
                    $scopeIdSearch = 1;
                } else if (Str::contains('country', strtolower($keyword))) {
                    $scopeIdSearch = 2;
                } else if (Str::contains('city', strtolower($keyword))) {
                    $scopeIdSearch = 3;
                } else if (Str::contains('service', strtolower($keyword))) {
                    $scopeIdSearch = 4;
                }
                $query->where('scope_id', $scopeIdSearch);
            })
            /* ->filterColumn('metafieldKeysValue.value', function ($query, $keyword) {
                $query->whereHas('metafieldKeysValue', function ($subQuery) use ($keyword) {
                    $subQuery->where('value', 'like', "%$keyword%");
                });
            }) */
            ->rawColumns(['action', 'scope_id']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Location $model, Request $request): QueryBuilder
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
        $orderByColumn = 10;
        return $this->builder()
            ->setTableId('location-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy($orderByColumn)
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
        $columns[] = Column::make('title')->title(trans('cruds.location.fields.title'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('scope_id')->title(trans('cruds.location.fields.scope'))->addClass('dt-scope')->searchable(true);
        $columns[] = Column::make('parentLocation.title')->title(trans('cruds.location.fields.parent'))->orderable(false)->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Location_' . date('YmdHis');
    }
}
