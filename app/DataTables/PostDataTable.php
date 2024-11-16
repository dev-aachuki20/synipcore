<?php

namespace App\DataTables;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with([
            'user',
        ])->select('posts.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            // ->editColumn('title', function ($record) {
            //     return $record->title ? ucfirst($record->title) : null;
            // })
            ->editColumn('created_by', function ($record) {
                return $record->user ? ucfirst($record->user->name) : null;
            })

            ->editColumn('post_type', function ($record) {
                return $record->post_type ? ucfirst($record->post_type) : null;
            })

            ->editColumn('status', function ($record) {
                $options = config('constant.status_type.post_status');

                $selected = isset($record) && !empty($record->status) ? $record->status : '';

                $html = '<div class="TableSelect">
                            <select class="statusUpdate" data-record-id="' . $record->uuid . '">';

                foreach ($options as $value => $label) {
                    $isSelected = ($value == $selected) ? ' selected' : '';
                    $html .= '<option value="' . $value . '"' . $isSelected . ' data-post_id="' . $record->uuid . '" data-post_status="' . $value . '">' . $label . '</option>';
                }

                $html .= '</select></div>';

                return $html;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('post_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.posts.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewPost" title="Show"> <i class="ri-eye-line"></i> </a>';
                }

                if (Gate::check('post_comment_access')) {
                    $actionHtml .= '<a href="' . route('admin.postCommentDetail', $record->uuid) . '" class="btn btn-outline-success btn-sm btnPostCommentDetail" title="Post Comments Details"> <i class="ri-discuss-line"></i> </a>';
                }

                if (Gate::check('post_edit')) {
                    $actionHtml .= '<a href="' . route('admin.posts.edit', $record->uuid) . '" data-href="' . route('admin.posts.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditPost" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('post_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.posts.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeletePost" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })

            // ->filterColumn('content', function ($query, $keyword) {
            //     $query->where('content', 'like', "%$keyword%");
            // })

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%$keyword%");
            })

            ->setRowId('id')
            ->rawColumns(['action', 'status' /* , 'content' */]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Post $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery();
        if (!isset($request->order[0]['column']) || $request->order[0]['column'] == 5) {
            $query->orderByDesc('created_at');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('post-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy(5)
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
        // $columns[] = Column::make('title')->title(trans('cruds.post.fields.title'))->addClass('dt-comment')->searchable(true);
        $columns[] = Column::make('created_by')->title(trans('cruds.post.fields.user'))->addClass('dt-user-name')->searchable(true);
        $columns[] = Column::make('status')->title(trans('cruds.post.fields.status'))->addClass('dt-status')->searchable(true);
        $columns[] = Column::make('post_type')->title(trans('cruds.post.fields.post_type'))->addClass('')->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.post.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Post_' . date('YmdHis');
    }
}
