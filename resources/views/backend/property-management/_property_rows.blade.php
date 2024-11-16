@if(isset($propertyManagement))
@forelse($propertyManagement as $key => $property)
<tr>
    <td>{{ $key + 1 }}</td>
    <td>{{ ucfirst($property->property_item) }}</td>
    <td>
        @if(!empty($property->property_management_images_image_urls) && is_array($property->property_management_images_image_urls))
        <a href="javascript:void(0);" data-id="{{ $property->id }}" data-href="{{ route('admin.property-managements.viewImage', $property->id) }}" class="btnPropetyReportAllImageView" title="Property Report All Images">
            <img src="{{ $property->property_management_images_image_urls[0] }}" alt="Property Image" style="max-width: 100px; max-height: 100px;" />
        </a>
        @else
        {{trans('global.no_images_found')}}
        @endif
    </td>
    <td>{{ ucfirst($property->location) ?? '' }}</td>
    <td>{{ $property->amount }}</td>
    <td class="dt-comment"><p class="full_data">{!! ucfirst(strip_tags($property->description)) !!}</p><span class="more_data_btn" style="display: none;"> Read More</span></td>
    <td>{{ \Carbon\Carbon::parse($property->purchase_date)->format(config('constant.date_format.date')) }}</td>
    <td>{{ $property->unit_price }}</td>
    <td>{{ ucfirst($property->allocation) }}</td>
    <td>{{ ucfirst($property->propertyType->title) }}</td>
    <td>{{ ucfirst($property->property_code) }}</td>
</tr>
@empty
<tr>
    <td colspan="11" class="text-center">{{trans('cruds.datatable.data_not_found')}}</td>
</tr>
@endforelse
@endif