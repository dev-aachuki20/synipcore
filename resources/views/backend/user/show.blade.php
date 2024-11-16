<div class="modal fade show" id="ViewUser" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.user.title_singular') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.user.fields.name') }} </th>
                                    <td> {{ $user->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.email') }} </th>
                                    <td> {{ $user->email ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.mobile') }} </th>
                                    <td> {{ $user->mobile_number ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.mobile_verified') }} </th>
                                    <td> {{ $user->mobile_verified == 1 ? trans('global.yes') : trans('global.no') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.roles') }} </th>
                                    <td>
                                        @if ($user->roles->isNotEmpty())
                                            @foreach ($user->roles as $role)
                                                {{ $role->name }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.language') }} </th>
                                    <td> {{ $user->language ? ucfirst($user->language) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.status') }} </th>
                                    <td> {{ $user->status == 1 ? config('constant.status')[$user->status] : config('constant.status')[0] }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.user.fields.created_at') }} </th>
                                    <td> {{ $user->created_at->format(config('constant.date_format.date')) }}
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
