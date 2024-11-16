<div class="modal fade show" id="ViewComplaint" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ trans('global.show') }}
                    {{ trans('cruds.complaint.title_singular') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.complaint.fields.user') }} </th>
                                    <td> {{ isset($complaint) && isset($complaint->user) ? ucfirst($complaint->user->name) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.complaint.fields.complaint_type') }} </th>
                                    <td> {{ isset($complaint) && $complaint->complaintType ? ucfirst($complaint->complaintType->title) : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.complaint.fields.description') }} </th>
                                    <td>
                                        <div class="show-fulldescription"> {!! ucfirst($complaint->description) ?? 'N/A' !!} </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.complaint.fields.type') }} </th>
                                    <td> {{ isset($complaint) ? ucfirst($complaint->category) : 'N/A' }} </td>
                                </tr>

                                <tr>
                                    <th style="width:150px;"> {{ trans('cruds.complaint_type.fields.image') }} </th>
                                    <td>
                                        @if($complaint->complaintImages && $complaint->complaintImages->isNotEmpty())
                                        <div class="post-images">
                                            <a href="javascript:void(0);" data-id="{{ $complaint->id }}" data-href="{{ route('admin.complaint.viewImage', $complaint->id) }}" class="complaint_images" title="Complaint Images">
                                                <img src="{{ $complaint->complaint_image_urls[0] }}" alt="Complaint Image" style="max-width: 100px; max-height: 100px;" />
                                            </a>
                                        </div>
                                        @else
                                        {{trans('global.no_images_found')}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.complaint.fields.status') }} </th>
                                    <td> {{ $complaint->status == 1 ? config('constant.status')[$complaint->status] : config('constant.status')[0] }}
                                    </td>
                                </tr>
                                <tr>
                                    <th> {{ trans('cruds.complaint.fields.created_at') }} </th>
                                    <td> {{ $complaint->created_at->format(config('constant.date_format.date')) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>