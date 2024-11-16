<?php

namespace App\DataTables;

use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class ServiceBookingDataTable extends DataTable
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
            'service',
            'user.society',
            'user.building',
            'user.unit'
        ])->select('service_bookings.*')))
            ->addIndexColumn()

            ->editColumn('description', function ($record) {
                return $record->description ? ucfirst($record->description) : '';
            })
            ->editColumn('booking_date', function ($record) {
                return $record->booking_date ? dateFormat($record->booking_date, config('constant.date_format.date')) : '';
            })
            ->editColumn('booking_time', function ($record) {
                return $record->booking_time ? dateFormat($record->booking_time, config('constant.date_format.time')) : '';
            })

            ->editColumn('service.title', function ($record) {
                return ucfirst($record->service->title ?? null);
            })
            ->editColumn('user.society.name', function ($record) {
                return ucfirst($record->user->society->name ?? null);
            })
            ->editColumn('user.unit.title', function ($record) {
                return ucfirst($record->user->unit->title ?? null);
            })

            ->editColumn('status', function ($record) {
                $options = config('constant.status_type.service_status');
                $selected = isset($record) && !empty($record->status) ? $record->status : '';

                $html = '<div class="TableSelect">
                                <select class="statusUpdate" data-record-id="' . $record->uuid . '">';

                foreach ($options as $value => $label) {
                    $isSelected = ($value == $selected) ? ' selected' : '';
                    $html .= '<option value="' . $value . '"' . $isSelected . ' data-service_booking_id="' . $record->uuid . '" data-service_booking_status="' . $value . '">' . $label . '</option>';
                }

                $html .= '</select></div>';

                return $html;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                /* if (Gate::check('service_booking_view')) {
                        $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.service-bookings.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewServiceBooking" title="Show"> <i class="ri-eye-line"></i> </a>';
                    } */

                /* if (Gate::check('service_booking_edit')) {
                        $actionHtml .= '<a href="' . route('admin.service-bookings.edit', $record->uuid) . '" data-href="' . route('admin.service-bookings.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditServiceBooking" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                    } */

                if (Gate::check('service_booking_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.service-bookings.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteServiceBooking" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })

            // Adding search filters for all columns
            ->filterColumn('description', function ($query, $keyword) {
                $query->where('description', 'like', "%$keyword%");
            })
            ->filterColumn('booking_date', function ($query, $keyword) {
                $formattedKeyword = dateFormat($keyword, config('constant.date_format.date'));
                $query->whereDate('booking_date', 'like', "%$formattedKeyword%");
            })
            ->filterColumn('booking_time', function ($query, $keyword) {
                $formattedKeyword = dateFormat($keyword, config('constant.date_format.time'));
                $query->whereTime('booking_time', 'like', "%$formattedKeyword%");
            })
            ->filterColumn('service.title', function ($query, $keyword) {
                $query->whereHas('service', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })
            ->filterColumn('user.society.name', function ($query, $keyword) {
                $query->whereHas('user.society', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('user.unit.title', function ($query, $keyword) {
                $query->whereHas('user.unit', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%$keyword%");
            })
            ->setRowId('id')
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ServiceBooking $model): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->whereHas('service.user.society', function ($query) use ($user) {
                $query->where('id', $user->society_id);
            })->with('service', 'service.user.society')->newQuery();
        } else {
            return $model->with('service', 'service.user.society')->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('servicebooking-table')
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
        $columns[] = Column::make('description')->title(trans('cruds.service_booking.fields.detail'))->addClass('dt-description')->searchable(true);
        $columns[] = Column::make('booking_date')->title(trans('cruds.service_booking.fields.booking_date'))->addClass('dt-booking_date')->searchable(true);
        $columns[] = Column::make('booking_time')->title(trans('cruds.service_booking.fields.booking_time'))->addClass('dt-booking_time')->searchable(true);

        $columns[] = Column::make('service.title')->title(trans('cruds.service_booking.fields.service'))->addClass('dt-service')->searchable(true);
        $columns[] = Column::make('user.society.name')->title(trans('cruds.service_booking.fields.society'))->addClass('dt-society')->searchable(true);
        $columns[] = Column::make('user.unit.title')->title(trans('cruds.service_booking.fields.unit'))->addClass('dt-unit')->searchable(true);

        $columns[] = Column::make('status')->title(trans('cruds.service_booking.fields.status'))->addClass('dt-status')->searchable(true);
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ServiceBooking_' . date('YmdHis');
    }
}
