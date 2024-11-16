<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Support\Str;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['roles', 'society'])->select('users.*')))
            ->addIndexColumn()

            ->editColumn('created_at', function ($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('name', function ($record) {
                return $record->name ? ucwords($record->name) : '';
            })

            ->editColumn('user.roles', function ($record) {
                $roleHtml = '';
                if ($record->roles()->count() > 0) {
                    foreach ($record->roles as $role) {
                        $roleHtml .= '<span class="badge badge-outline-primary" style="margin: 4px 4px 2px;">' . $role->name . '</span>';
                    }
                }
                return $roleHtml;
            })

            // ->editColumn('society.name', function ($record) {
            //     return $record->society ? $record->society->name : '';
            // })

            ->editColumn('user.societies', function ($record) {
                $societyHtml = '';
                if ($record->societies()->count() > 0) {
                    foreach ($record->societies as $society) {
                        $societyHtml .= '<span class="badge badge-outline-primary" style="margin: 4px 4px 2px;">' . $society->name . '</span>';
                    }
                }
                return $societyHtml;
            })

            // ->editColumn('status', function ($record) {
            //     $checkedStatus = '';
            //     if ($record->status == 1) {
            //         $checkedStatus = 'checked';
            //     }
            //     return '<div class="checkbox switch">
            //         <label>
            //             <input type="checkbox" class="switch-control user_status_cb" ' . $checkedStatus . ' data-user_id="' . ($record->uuid) . '" />
            //             <span class="switch-label"></span>
            //         </label>
            //     </div>';
            // })

            ->addColumn('action', function ($record) {
                $actionHtml = '';
                // if (Gate::check('user_access')) {
                //     $actionHtml .= '<a href="javascript:void(0);" data-href="' . route('admin.users.show', $record->uuid) . '" class="btn btn-outline-info btn-sm btnViewUser" title="Show"> <i class="ri-eye-line"></i> </a>';
                // }

                if (Gate::check('user_edit')) {
                    $actionHtml .= '<a href="' . route('admin.users.edit', $record->uuid) . '" class="btn btn-outline-primary btn-sm btnEditUser" id="btnEditUser" title="Edit"> <i class="ri-pencil-line"></i> </a>';
                }

                if (Gate::check('user_delete')) {
                    $actionHtml .= '<a href="javascript:void(0);" class="btn btn-outline-danger btn-sm deleteUserBtn" data-href="' . route('admin.users.destroy', $record->uuid) . '" title="Delete"><i class="ri-delete-bin-line"></i></a>';
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
            // ->filterColumn('status', function ($query, $keyword) {
            //     $statusSearch  = null;
            //     if (Str::contains('active', strtolower($keyword))) {
            //         $statusSearch = 1;
            //     } else if (Str::contains('inactive', strtolower($keyword))) {
            //         $statusSearch = 0;
            //     }
            //     $query->where('status', $statusSearch);
            // })

            ->filterColumn('user.roles', function ($query, $keyword) {
                // Split the keywords by the pipe symbol (|) since we are joining selected options with | in JavaScript
                $keywords = explode('|', $keyword);

                // If there's more than one keyword, we need to use `whereHas` with `orWhereHas`
                $query->where(function ($query) use ($keywords) {
                    foreach ($keywords as $key => $role) {
                        if ($key == 0) {
                            $query->whereHas('roles', function ($q) use ($role) {
                                $q->where('name', 'like', "%$role%");
                            });
                        } else {
                            $query->orWhereHas('roles', function ($q) use ($role) {
                                $q->where('name', 'like', "%$role%");
                            });
                        }
                    }
                });
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
            ->rawColumns(['action', 'user.roles', 'user.societies' /*, 'status' */]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        // return $model->newQuery();
        return $model->whereHas('roles', function ($query) {
            $query->where('id', '!=', config('constant.roles.superadmin'));
        })->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $orderByColumn = 7;
        return $this->builder()
            ->setTableId('user-table')
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
                        var columnName = column.dataSrc();
                        var header = $(column.header());
                        
                        // if (columnName === 'society.name') {
                        //     var select = $('<select multiple=\"multiple\" class=\"form-control dt-multiselect\" style=\"width: 100%;\"><option value=\"\">selectSocietyPlaceholder</option></select>')
                        //         .appendTo(header)
                        //         .on('change', function() {
                        //             var selectedOptions = $(this).val();
                        //             var searchValue = selectedOptions ? selectedOptions.join('|') : '';
                        //             column.search(searchValue, true, false).draw();
                        //         });

                        //     // Initialize Select2
                        //     select.select2({
                        //         placeholder: selectSocietyPlaceholder,
                        //         width: 'resolve'
                        //     });

                        //     // Fetch all societies via AJAX
                        //     $.ajax({
                        //         url: '/admin/societies/all', 
                        //         method: 'GET',
                        //         success: function(response) {
                        //             // Assume `response` contains an array of societies
                        //             $.each(response, function(index, society) {
                        //                 select.append('<option value=\"' + society + '\">' + society + '</option>');
                        //             });
                        //         }
                        //     });
                        // } else 
                        if (columnName === 'user.roles') {
                            var select = $('<select multiple=\"multiple\" class=\"form-control dt-multiselect\" style=\"width: 100%;\"><option value=\"\">selectRolePlaceholder</option></select>')
                                .appendTo(header)
                                .on('change', function() {
                                    var selectedOptions = $(this).val();
                                    var searchValue = selectedOptions ? selectedOptions.join('|') : '';
                                    column.search(searchValue, true, false).draw();
                                });

                            // Initialize Select2
                            select.select2({
                                placeholder: selectRolePlaceholder,
                                width: 'resolve'
                            });

                            // Fetch all roles via AJAX
                            $.ajax({
                                url: '/admin/users/all', 
                                method: 'GET',
                                success: function(response) {
                                console.log(response);
                                    // Assume `response` contains an array of societies
                                    $.each(response, function(index, role) {
                                    console.log(index, role);
                                        select.append('<option value=\"' + role + '\">' + role + '</option>');
                                    });
                                }
                            });
                        } else if (columnName === 'created_at') {
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
        $columns[] = Column::make('name')->title(trans('cruds.user.fields.name'));
        $columns[] = Column::make('email')->title(trans('cruds.user.fields.email'));
        $columns[] = Column::make('mobile_number')->title(trans('cruds.user.fields.mobile'));
        $columns[] = Column::make('user.roles')->title(trans('cruds.user.fields.roles'))->orderable(false);
        // $columns[] = Column::make('society.name')->title(trans('cruds.user.fields.society'));
        $columns[] = Column::make('user.societies')->title(trans('cruds.menus.societies'))->orderable(false);

        // $columns[] = Column::make('status')->title(trans('cruds.user.fields.status'));

        $columns[] = Column::make('created_at')->title(trans('cruds.user.fields.created_at'))->addClass('dt-created_at');
        $columns[] = Column::computed('action')->title(trans('global.action'))->orderable(false)->exportable(false)->printable(false)->width(60)->addClass('text-center action-col');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
