<?php

namespace App\DataTables;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class AnnouncementDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'user'])->select('announcements.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('user.name', function ($record) {
                return ucfirst($record->user->name ?? '');
            })

            ->editColumn('announcement_type', function ($record) {
                $types = config('constant.annuncement_types');
                return isset($types[$record->announcement_type]) ? ucfirst($types[$record->announcement_type]) : '';
            })

            ->editColumn('title', function ($record) {
                return $record->title ? ucfirst($record->title) : '';
            })

            ->editColumn('society.name', function ($record) {
                return ucfirst($record->society->name ?? 'N/A');
            })

            ->addColumn('image', function ($record) {
                $imageHtml = '';

                if (!empty($record->announcement_image_urls) && is_array($record->announcement_image_urls)) {
                    $imageHtml .= ' <a href="javascript:void(0);" data-id=" ' . $record->id . '" data-href=" ' . route('admin.announcements.viewImage', $record->id) . '" class="btnAnnouncementAllImageView" title="Announcement All Images">
                    <img src=" ' . $record->announcement_image_urls[0] . ' " alt="Announcement Image" style="max-width: 100px; max-height: 100px;" />
                </a>';
                } else {
                    trans('global.no_images_found');
                }



                return $imageHtml;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';

                if (Gate::check('announcement_edit')) {
                    $actionHtml .= '<a href="' . route('admin.announcements.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditAnnouncement" id="btnEditAnnouncement" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('announcement_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.announcements.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteAnnouncement" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
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

            ->filterColumn('title', function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            })
            ->filterColumn('society.name', function ($query, $keyword) {
                $query->whereHas('society', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })

            ->filterColumn('announcement_type', function ($query, $keyword) {
                $statusSearch  = null;
                if (Str::contains('Announcement', strtolower($keyword))) {
                    $statusSearch = 1;
                } else if (Str::contains('Poll', strtolower($keyword))) {
                    $statusSearch = 2;
                } else if (Str::contains('Event', strtolower($keyword))) {
                    $statusSearch = 3;
                }
                $query->where('announcement_type', $statusSearch);
            })

            ->filterColumn('user.name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->rawColumns(['action', 'announcement_type', 'image']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Announcement $model, Request $request): QueryBuilder
    {
        $user = auth()->user();
        if ($user->is_sub_admin) {
            return $model->newQuery()->where('announcements.society_id', $user->society_id);
        } else {
            return $model->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('announcement-table')
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
        $columns[] = Column::make('title')->title(trans('cruds.announcement.fields.title'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.announcement.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('user.name')->title(trans('cruds.announcement.fields.posted_by'))->addClass('dt-posted_by')->searchable(true);
        $columns[] = Column::make('announcement_type')->title(trans('cruds.announcement.fields.type'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::computed('image')->title(trans('global.image'))->addClass('dt-image');
        $columns[] = Column::make('created_at')->title(trans('cruds.announcement.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Announcement_' . date('YmdHis');
    }
}
