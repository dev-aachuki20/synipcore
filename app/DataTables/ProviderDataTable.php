<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class ProviderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['societies', 'society', 'building', 'unit'])->select('users.*')))
            ->addIndexColumn()

            ->editColumn('user.societies', function ($record) {
                $societyHtml = '';
                if ($record->societies()->count() > 0) {
                    foreach ($record->societies as $society) {
                        $societyHtml .= '<span class="badge badge-outline-primary" style="margin: 4px 4px 2px;">' . $society->name . '</span>';
                    }
                }
                return $societyHtml;
            })

            // ->editColumn('society.name', function ($record) {
            //     return $record->society ? ucfirst($record->society->name) : '';
            // })

            ->editColumn('name', function ($record) {
                return $record ? ucfirst($record->name) : '';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('provider_edit')) {
                    $actionHtml .= '<a href="' . route('admin.providers.edit', $record->uuid) . '" data-href="' . route('admin.providers.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditProvider" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('provider_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.providers.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteProvider" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('users.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->filterColumn('mobile_number', function ($query, $keyword) {
                $query->where('mobile_number', 'like', "%$keyword%");
            })

            ->filterColumn('user.societies', function ($query, $keyword) {
                $keywords = explode('|', $keyword);

                $query->where(function ($query) use ($keywords) {
                    foreach ($keywords as $key => $society) {
                        if ($key == 0) {
                            $query->whereHas('societies', function ($q) use ($society) {
                                $q->where('name', 'like', "%$society%");
                            });
                        } else {
                            $query->orWhereHas('societies', function ($q) use ($society) {
                                $q->where('name', 'like', "%$society%");
                            });
                        }
                    }
                });
            })
            ->rawColumns(['action', 'user.societies']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model, Request $request): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->newQuery()
                ->whereHas('roles', function ($query) {
                    $query->where('id', config('constant.roles.provider'));
                })->where('users.society_id', $user->society_id);
        } else {
            return $model->newQuery()
                ->whereHas('roles', function ($query) {
                    $query->where('id', config('constant.roles.provider'));
                });
        }


        // if (isset($request->order[0]['column']) && $request->order[0]['column'] > 0) {
        //     return $model->newQuery()
        //         ->whereHas('roles', function ($query) {
        //             $query->where('id', config('constant.roles.provider'));
        //         });
        // } else {
        //     return $model->newQuery()
        //         ->whereHas('roles', function ($query) {
        //             $query->where('id', config('constant.roles.provider'));
        //         })->orderByDesc('id');
        // }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('provider-table')
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
        $columns[] = Column::make('name')->title(trans('cruds.provider.fields.name'));
        $columns[] = Column::make('mobile_number')->title(trans('cruds.provider.fields.mobile'));
        // $columns[] = Column::make('society.name')->title(trans('cruds.provider.fields.society'));
        $columns[] = Column::make('user.societies')->title(trans('cruds.menus.societies'))->orderable(false);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Provider_' . date('YmdHis');
    }
}
