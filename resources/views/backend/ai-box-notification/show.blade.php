<div class="modal fade show" id="ViewAiboxDetails" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.aibox_notification.title_singular') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.event_id') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->notification_data['Event_ID'])  ? ucfirst($aiboxAlert->notification_data['Event_ID']) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.aibox_notification.fields.event_code') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->notification_data['Evenet_Code']) ? $aiboxAlert->notification_data['Evenet_Code'] : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.event_name') }} </th>
                                    <td>
                                        @php
                                        $eventCode = $aiboxAlert->notification_data['Evenet_Code'] ?? null;
                                        $events = config('constant.aibox_notification.api_code');
                                        $eventName = $eventCode && isset($events[$eventCode]) ? ucfirst($events[$eventCode]['event_name']) : 'N/A';
                                        @endphp
                                        {{ $eventName }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.event_desc') }} </th>
                                    <td>
                                        @php
                                        $location = ucfirst($aiboxAlert->camera->lacated_at) ?? 'the specified location';
                                        $eventCode = $aiboxAlert->notification_data['Evenet_Code'] ?? null;
                                        $eventDesc = $eventCode && isset($events[$eventCode]) ? ucfirst($events[$eventCode]['event_desc']) : 'Description not available.';

                                        $eventDesc = str_replace('{location_data_key}', $location, $eventDesc);
                                        @endphp
                                        {{ $eventDesc }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.society') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->society) ? ucfirst($aiboxAlert->society->name) :  'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.building') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->building) ? ucfirst($aiboxAlert->building->title) :  'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.unit') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->unit) ? ucfirst($aiboxAlert->unit->title) :  'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.location') }} </th>
                                    <td> {{ isset($aiboxAlert) && isset($aiboxAlert->camera) ? ucfirst($aiboxAlert->camera->lacated_at) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.aibox_notification.fields.camera_id') }} </th>
                                    <td>{{ isset($aiboxAlert) && isset($aiboxAlert->camera) ? $aiboxAlert->camera->camera_id : 'N/A' }}</td>
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