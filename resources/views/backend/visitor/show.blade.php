<div class="modal fade show" id="ViewVisitor" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.visitor.title_singular') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.visitor.fields.type') }} </th>
                                    <td> {{ isset($visitor) ? ucfirst($visitor->visitor_type) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.visitor_info') }} </th>
                                    <td> {{ isset($visitor) ? ucfirst($visitor->name) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.user') }} </th>
                                    <td> {{ isset($visitor) && isset($visitor->user) ? ucfirst($visitor->user->name) : 'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.society') }} </th>
                                    <td> {{ isset($visitor) && isset($visitor->user->society) ? ucfirst($visitor->user->society->name) :  'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.building') }} </th>
                                    <td> {{ isset($visitor) && isset($visitor->user->building) ? ucfirst($visitor->user->building->title) :  'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.unit') }} </th>
                                    <td> {{ isset($visitor) && isset($visitor->user->unit) ? ucfirst($visitor->user->unit->title) :  'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.visit_date') }} </th>
                                    <td> {{ isset($visitor) ? $visitor->visit_date : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.gatepass_code') }} </th>
                                    <td>{{ isset($visitor) ? $visitor->gatepass_code : 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.contact') }} </th>
                                    <td>{{ isset($visitor) ? $visitor->phone_number : 'N/A' }}</td>
                                </tr>

                                @if(isset($visitor) && $visitor->cab_number != null)
                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.cab_number') }} </th>
                                    <td>{{ isset($visitor) ? ucfirst($visitor->cab_number) : 'N/A' }}</td>
                                </tr>
                                @endif

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.notes') }} </th>
                                    <td>{{ isset($visitor) ? ucfirst($visitor->visitor_note) : 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.other_info') }} </th>
                                    <td>{{ isset($visitor) ? ucfirst($visitor->other_info) : 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.visitor.fields.status') }} </th>
                                    <td>{{ isset($visitor) ? ucfirst($visitor->status) : 'N/A' }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>