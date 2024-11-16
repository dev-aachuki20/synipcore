<?php

namespace App\DataTables;

use App\Models\AmenityBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AmenityBookingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['amenity', 'user'])->select('amenity_bookings.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at ? $record->created_at->format(config('constant.date_format.date')) : '-';
            })

            ->editColumn('amenity.title', function ($record) {
                return $record->amenity ? ucfirst($record->amenity->title) : '';
            })

            ->editColumn('user.name', function ($record) {
                return $record->user ? ucfirst($record->user->name) : '';
            })

            ->editColumn('date_period', function ($record) {
                return $record->date_period ? $record->date_period : '';
            })

            // ->editColumn('from_date', function ($record) {
            //     return $record->from_date ? $record->from_date->format(config('constant.date_format.date')) : '';
            // })

            // ->editColumn('to_date', function ($record) {
            //     return $record->to_date ? $record->to_date->format(config('constant.date_format.date')) : '';
            // })

            ->editColumn('status', function ($record) {
                $options = config('constant.status_type.amenity_booking_status');
                $selected = isset($record) && !empty($record->status) ? $record->status : '';

                $html = '<div class="TableSelect">
                            <select class="statusUpdate" data-record-id="' . $record->uuid . '">';

                foreach ($options as $value => $label) {
                    $isSelected = ($value == $selected) ? ' selected' : '';
                    $html .= '<option value="' . $value . '"' . $isSelected . ' data-amenity_booking_id="' . $record->uuid . '" data-amenity_booking_status="' . $value . '">' . $label . '</option>';
                }

                $html .= '</select></div>';

                return $html;
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                /* if (Gate::check('unit_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.units.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewUnit" title="Show"> <i class="ri-eye-line"></i> </a>';
                } */

                // if (Gate::check('unit_edit')) {
                //     $actionHtml .= '<a href="' . route('admin.units.edit', $record->uuid) . '" data-href="' . route('admin.units.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditUnit" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                // }

                // if (Gate::check('unit_delete')) {
                //     $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.units.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteUnit" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                // }

                return $actionHtml;
            })
            ->setRowId('id')

            // ->filterColumn('from_date', function ($query, $keyword) {
            //     // dd($keyword);
            //     // $date_range = explode(' - ', $keyword);
            //     // $startDate = Carbon::parse($date_range[0]);
            //     // $endDate   = Carbon::parse($date_range[1]);

            //     // $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

            //     // search for text
            //     $searchDateFormat = config('constant.search_date_format.date');
            //     $query->whereRaw("DATE_FORMAT(from_date,'$searchDateFormat') like ?", ["%$keyword%"]);
            // })

            // ->filterColumn('to_date', function ($query, $keyword) {
            //     $searchDateFormat = config('constant.search_date_format.date');
            //     $query->whereRaw("DATE_FORMAT(to_date,'$searchDateFormat') like ?", ["%$keyword%"]);
            // })

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%$keyword%");
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(AmenityBooking $model): QueryBuilder
    {
        $user = auth()->user();

        if ($user->is_sub_admin) {
            return $model->whereHas('amenity.society', function ($query) use ($user) {
                $query->where('id', $user->society_id);
            })->with('amenity', 'amenity.society')->newQuery();
        } else {
            return $model->with('amenity', 'amenity.society')->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('amenity-booking-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy(6)
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
        $columns[] = Column::make('user.name')->title(trans('cruds.amenity_booking.fields.resident'))->addClass('dt-title')->searchable(true);
        $columns[] = Column::make('amenity.title')->title(trans('cruds.amenity_booking.fields.amenity'))->addClass('dt-amenity')->searchable(true);
        $columns[] = Column::computed('date_period')->title(trans('cruds.amenity_booking.fields.date_period'))->addClass('dt-date_period')->searchable(true);
        // $columns[] = Column::make('from_date')->title(trans('cruds.amenity_booking.fields.from_date'))->addClass('dt-from_date')->searchable(true);
        // $columns[] = Column::make('to_date')->title(trans('cruds.amenity_booking.fields.to_date'))->addClass('dt-to_date')->searchable(true);
        $columns[] = Column::make('amount')->title(trans('cruds.amenity_booking.fields.amount'))->addClass('dt-amount')->searchable(true);
        $columns[] = Column::make('status')->title(trans('cruds.amenity_booking.fields.status'))->addClass('dt-status')->searchable(true);

        $columns[] = Column::make('created_at')->title(trans('cruds.amenity_booking.fields.created_at'))->addClass('dt-created_at');
        // $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AmenityBooking_' . date('YmdHis');
    }
}
