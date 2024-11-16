<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, $timePeriod = 'week')
    {
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : Carbon::now();
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
        $timePeriod = $request->has('timePeriod') ? $request->timePeriod : 'week';

        // Initialize variables for first graph new registration.
        $labels = [];
        $data = [];
        $datasets = [];

        // Initialize variables for second graph recent activity.
        $activityLabels = [];
        $activityData = [];
        $activityDatasets = [];

        // User count logic
        $userCount = User::whereHas('roles', function ($query) {
            $query->where('id', '!=', config('constant.roles.superadmin'));
        })->count();

        if (!in_array($timePeriod, ['day', 'week', 'month', 'custom range'])) {
            return response()->json(['error' => 'Invalid range'], 400);
        }

        $interval = 'day';
        if ($timePeriod == 'day') {
            $startDate->startOfDay();
            $endDate->endOfDay();
            $interval = 'hour';
        } elseif ($timePeriod == 'week') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $interval = 'day';
        } elseif ($timePeriod == 'month') {
            $startDate->startOfMonth()->startOfDay();
            $endDate->endOfDay();
            $interval = 'day';
        }

        // elseif ($timePeriod == 'custom range') {
        //     $startDate->startOfDay();
        //     $endDate->endOfDay();
        //     $interval = $startDate->diffInDays($endDate) == 0 ? 'hour' : 'day';
        // }

        // Generate data for the first graph
        $users = User::whereHas('roles', function ($query) {
            $query->where('id', '!=', config('constant.roles.superadmin'));
        })->whereBetween('created_at', [$startDate, $endDate])->get()->groupBy(function ($user) use ($interval) {
            return $interval === 'hour' ? $user->created_at->format('h a') : $user->created_at->format('d-m-Y');
        });

        $startDateCopy = $startDate->copy();
        while ($startDateCopy->lte($endDate)) {
            $date = $interval === 'hour' ? $startDateCopy->format('h a') : $startDateCopy->format('d-m-Y');
            $count = isset($users[$date]) ? $users[$date]->count() : 0;
            $labels[] = $date;
            $data[] = $count;
            $startDateCopy->add(1, $interval);
        }

        // if ($interval == 'hour') {
        //     $totalDays = 1;
        //     $total = array_sum($data);
        // } else {
        //     $totalDays = count($labels);
        //     $total = array_sum($data);
        // }

        // $total = array_sum($data);

        $datasets[] = [
            'label' => trans('cruds.dashboard.fields.count'),
            'data' => $data,
            'borderColor' => '#2861f0',
            'pointBorderColor' => "#2861f0",
            'pointBackgroundColor' => "#2861f0",
            'pointHoverBackgroundColor' => "#000000",
            'pointHoverBorderColor' => "#000000",
            // 'xAxisText' => $interval === "day" ? trans('cruds.dashboard.labels.day') : trans('cruds.dashboard.labels.hour'),
            'yAxisText' => trans('cruds.dashboard.fields.count'),
            // 'pluginText' => trans('cruds.dashboard.fields.alert_graph'),
        ];



        // Fetch recent activities for the second graph
        $activities = Visitor::whereBetween('created_at', [$startDate, $endDate])->get()->groupBy(function ($activity) use ($interval) {
            return $interval === 'hour' ? $activity->created_at->format('h a') : $activity->created_at->format('d-m-Y');
        });

        $startDateCopy = $startDate->copy();
        while ($startDateCopy->lte($endDate)) {
            $date = $interval === 'hour' ? $startDateCopy->format('h a') : $startDateCopy->format('d-m-Y');
            $count = isset($activities[$date]) ? $activities[$date]->count() : 0;
            $activityLabels[] = $date;
            $activityData[] = $count;
            $startDateCopy->add(1, $interval);
        }

        $activityDatasets[] = [
            'label' => trans('cruds.dashboard.fields.activity_count'),
            'data' => $activityData,
            'borderColor' => '#2861f0',
            'pointBorderColor' => "#2861f0",
            'pointBackgroundColor' => "#2861f0",
            'pointHoverBackgroundColor' => "#000000",
            'pointHoverBorderColor' => "#000000",
            // 'xAxisText' => $interval === "day" ? trans('cruds.dashboard.labels.day') : trans('cruds.dashboard.labels.hour'),
            'yAxisText' => trans('cruds.dashboard.fields.activity_count'),
            // 'pluginText' => trans('cruds.dashboard.fields.activity_alert_graph'),
        ];

        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'labels' => $labels,
                'datasets' => $datasets,
                'activityLabels' => $activityLabels,
                'activityDatasets' => $activityDatasets,
            ]);
        }

        // Return view with all necessary data
        return view('backend.dashboard', compact('userCount', 'labels', 'datasets', 'activityLabels', 'activityDatasets'));
    }
}
