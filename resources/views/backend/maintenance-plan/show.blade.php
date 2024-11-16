@extends('layouts.admin')
@section('title', trans('cruds.maintenance_plan.title_singular'))

@section('custom_css')

@endsection

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="card mt-20">
            <div class="card-header">
                <h4 class="mb-0">
                    {{ trans('cruds.maintenance_plan.title_singular') }}
                </h4>
            </div>
            <div class="card-body">
                <div class="inner_block">
                    <div class="society_area">
                        <div class="society_select view_maintenance_plan">
                            <h5>{{ucfirst($planItems->society->name)}} - {{$planItems->year_of}}</h5>
                        </div>
                    </div>
                    <div class="table-responsive custom-table footer-table nth_child2_table">
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-center">{{trans('cruds.category.title_singular')}}</th>
                                    <th class="text-center">{{trans('cruds.maintenance_plan.fields.item')}}</th>
                                    <th class="text-center px-0 pb-0">{{trans('cruds.maintenance_plan.fields.month')}}
                                        <table>
                                            <tr>
                                                @foreach(trans('cruds.plan_months') as $month)
                                                <th class="text-center">{{ $month }}</th>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </th>
                                    <th class="text-center">{{trans('cruds.maintenance_plan.fields.comments')}}</th>
                                    <th class="text-center">{{trans('cruds.maintenance_plan.fields.budget')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalBudget = 0;
                                $previousCategory = null;
                                @endphp
                                
                                @foreach($groupedItems as $category => $items)
                                    @php
                                        $rowCount = count($items);
                                        $totalBudgetForCategory = 0;
                                    @endphp
                                @foreach($items as $index =>  $itemVal)
                                    @php
                                        $itemTitle = $itemVal->maintenanceItem ? $itemVal->maintenanceItem->title : 'Unknown';
                                        $itemCost = (float) $itemVal->budget;
                                        $totalBudget += $itemCost;
                                        $totalBudgetForCategory += $itemCost;
                                    @endphp
                                <tr>
                                    @if ($index === 0)
                                        <td class="text-center" rowspan="{{ $rowCount }}">
                                            {{ ucfirst($category) }}
                                        </td>
                                    @endif
                                    <td class="text-center">{{ $itemTitle }}</td>
                                    <td class="text-center p-0">
                                        <table class="month_checkbox">
                                            <tr>
                                                @foreach(config('constant.plan_months') as $month)
                                                <td>
                                                    <input type="checkbox" value="{{ $month }}"
                                                        {{ in_array($month, json_decode($itemVal->month, true)) ? 'checked' : '' }} disabled>
                                                    <span></span>
                                                </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="text-center">{{ ucfirst($itemVal->comments) }}</td>
                                    <td class="text-center">${{ $itemCost }}</td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">{{trans('global.total_budget')}}</th>
                                    <th class="text-center">${{$totalBudget}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom_js')
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        $('.select-society').select2({
            // placeholder: "Select Society",
        });


    });
</script> -->
@endsection