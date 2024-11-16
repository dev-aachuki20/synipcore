<?php

namespace App\DataTables;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CommentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['user', 'commentable'])->select('comments.*')))
            ->addIndexColumn()

            ->editColumn('comment', function ($record) {
                return $record->comment ? '<p>' . ucfirst($record->comment) . '</p>' : '';
            })

            ->editColumn('commentable_type', function ($record) {
                return class_basename($record->commentable_type);
            })


            ->editColumn('user.name', function ($record) {
                return $record->user ? ucfirst($record->user->name) : '';
            })

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('is_approve', function ($record) {
                $checkedStatus = '';
                if ($record->is_approve == 1) {
                    $checkedStatus = 'checked';
                }
                return '<div class="checkbox switch comments_switch">
                    <label>
                        <input type="checkbox" class="switch-control comment_status_cb" ' . $checkedStatus . ' data-comment_id="' . ($record->id) . '" />
                        <span class="switch-label"></span>
                    </label>
                </div>';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('comment_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.comments.show', $record->id) . '" class="btn btn-outline-info btn-sm btnViewComment" title="Show"> <i class="ri-eye-line"></i> </a>';
                }

                if (Gate::check('comment_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.comments.destroy', $record->id) . '" class="btn btn-outline-danger btn-sm btnDeleteComment" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
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

            ->filterColumn('is_approve', function ($query, $keyword) {
                $statusSearch  = null;
                if (Str::contains('approved', strtolower($keyword))) {
                    $statusSearch = 1;
                } else if (Str::contains('deny', strtolower($keyword))) {
                    $statusSearch = 0;
                }
                $query->where('is_approve', $statusSearch);
            })
            ->rawColumns(['action', 'is_approve', 'comment']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Comment $model): QueryBuilder
    {

        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->whereHas('user.society', function ($query) use ($user) {
                $query->where('id', $user->society_id);
            })->with('user', 'user.society')->newQuery();
        } else {
            return $model->with('user', 'user.society')->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('complaint-table')
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
        $columns[] = Column::make('user.name')->title(trans('cruds.comment.fields.user'))->addClass('dt-user')->searchable(true);
        $columns[] = Column::make('commentable_type')->title(trans('cruds.comment.fields.type'))->addClass('dt-type')->searchable(true);
        $columns[] = Column::make('comment')->title(trans('cruds.comment.fields.comment'))->addClass('dt-comment')->searchable(true);
        $columns[] = Column::make('is_approve')->title(trans('cruds.user.fields.status'));
        $columns[] = Column::make('created_at')->title(trans('cruds.comment.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Comment_' . date('YmdHis');
    }
}
