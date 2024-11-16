<div class="modal fade show" id="ViewSupport" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.support.title_singular') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.support.fields.name') }} </th>
                                    <td> {{ ucfirst($support->user->name) ?? 'N/A' }} </td>
                                </tr> 
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.support.fields.email') }} </th>
                                    <td> {{ ucfirst($support->user->email) ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.support.fields.topic') }} </th>
                                    <td> {{ ucfirst($support->topic) ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.support.fields.message') }} </th>
                                    <td> {!! ucfirst($support->message) ?? 'N/A' !!} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.support.fields.created_at') }} </th>
                                    <td> {{ $support->created_at->format(config('constant.date_format.date')) }}
                                    </td>
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
