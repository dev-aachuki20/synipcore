<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResidentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['society', 'building', 'unit'])->select('users.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('name', function ($record) {
                return $record->name ? ucfirst($record->name) : '';
            })

            ->editColumn('society.name', function ($record) {
                return $record->society ? ucfirst($record->society->name) : '';
            })

            ->editColumn('unit.title', function ($record) {
                return ucfirst($record->unit->title ?? null);
            })
            ->editColumn('building.title', function ($record) {
                return ucfirst($record->building->title ?? null);
            })

            ->editColumn('resident_type', function ($record) {
                return $record->resident_type ? ucfirst(config('constant.resident_types')[$record->resident_type]) : '';
            })

            ->editColumn('is_verified', function ($record) {
                $checkedStatus = '';
                if ($record->is_verified == 1) {
                    $checkedStatus = 'checked';
                }
                return '<div class="checkbox switch">
                    <label>
                        <input type="checkbox" class="switch-control user_is_verified_cb" ' . $checkedStatus . ' data-user_id="' . ($record->uuid) . '" />
                        <span class="switch-label"></span>
                    </label>
                </div>';
            })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                /* if (Gate::check('resident_view')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.residents.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewResident" title="Show"> <i class="ri-eye-line"></i> </a>';
                } */

                if (Gate::check('resident_edit')) {
                    $actionHtml .= '<a href="' . route('admin.residents.edit', $record->uuid) . '" data-href="' . route('admin.residents.edit', $record->uuid) . '" class="btn btn-outline-success btn-sm btnEditResident" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('resident_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.residents.destroy', $record->uuid) . '" class="btn btn-outline-danger btn-sm btnDeleteResident" title="Delete"> <i class="ri-delete-bin-line"></i> </a>';
                }

                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('mobile_number', function ($query, $keyword) {
                $query->where('mobile_number', 'like', "%$keyword%");
            })
            ->filterColumn('building.name', function ($query, $keyword) {
                $query->whereHas('building', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })
            ->filterColumn('unit.name', function ($query, $keyword) {
                $query->whereHas('unit', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%");
                });
            })
            ->filterColumn('is_verified', function ($query, $keyword) {
                $verifiedSearch  = null;
                if (Str::contains('active', strtolower($keyword))) {
                    $verifiedSearch = 1;
                } else if (Str::contains('inactive', strtolower($keyword))) {
                    $verifiedSearch = 0;
                }
                $query->where('is_verified', $verifiedSearch);
            })
            ->filterColumn('resident_type', function ($query, $keyword) {
                $arr = array();
                foreach (config('constant.resident_types') as $key => $type) {
                    if (Str::contains(strtolower(trim($type)), strtolower($keyword))) {
                        $arr[] = $key;
                    }
                }
                $query->whereIn('resident_type', $arr);
            })

            ->filterColumn('created_at', function ($query, $keyword) {
                $date_range = explode(' - ', $keyword);
                $startDate = Carbon::parse($date_range[0]);
                $endDate   = Carbon::parse($date_range[1]);

                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->rawColumns(['action', 'is_verified']);
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
                    $query->where('id', config('constant.roles.resident'));
                })->where('users.society_id', $user->society_id);
        } else {
            return $model->newQuery()
                ->whereHas('roles', function ($query) {
                    $query->where('id', config('constant.roles.resident'));
                });
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('resident-table')
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
                        if (columnName === 'society.name') {
                            var select = $('<select multiple=\"multiple\" class=\"form-control dt-multiselect\" style=\"width: 100%;\"><option value=\"\">selectSocietyPlaceholder</option></select>')
                                .appendTo(header)
                                .on('change', function() {
                                    var selectedOptions = $(this).val();
                                    var searchValue = selectedOptions ? selectedOptions.join('|') : '';
                                    column.search(searchValue, true, false).draw();
                                });

                            // Initialize Select2
                            select.select2({
                                placeholder: 'selectSocietyPlaceholder',
                                width: 'resolve'
                            });

                            // Fetch all societies via AJAX
                            $.ajax({
                                url: '/admin/societies/all', 
                                method: 'GET',
                                success: function(response) {
                                    // Assume `response` contains an array of societies
                                    $.each(response, function(index, society) {
                                        select.append('<option value=\"' + society + '\">' + society + '</option>');
                                    });
                                }
                            });
                        }else if (columnName === 'created_at') { // Replace 'created_at' with your date column name
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
        $columns[] = Column::make('name')->title(trans('cruds.resident.fields.name'))->addClass('dt-name')->searchable(true);
        $columns[] = Column::make('building.title')->title(trans('cruds.resident.fields.building'))->addClass('dt-building-title')->searchable(true);
        $columns[] = Column::make('unit.title')->title(trans('cruds.resident.fields.unit'))->addClass('dt-unit-title')->searchable(true);
        $columns[] = Column::make('society.name')->title(trans('cruds.resident.fields.society'))->searchable(true);
        $columns[] = Column::make('resident_type')->title(trans('cruds.resident.fields.type'))->searchable(true);
        $columns[] = Column::make('created_at')->title(trans('cruds.resident.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Resident_' . date('YmdHis');
    }
}
