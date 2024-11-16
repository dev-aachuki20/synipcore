<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\TransactionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(TransactionDataTable $dataTable)
    {
        abort_if(Gate::denies('transaction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('backend.transaction.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function transactionReport(Request $request, $timePeriod = 'week')
    {
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : Carbon::now();
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
        $timePeriod = $request->has('timePeriod') ? $request->timePeriod : 'week';

        // Initialize variables for first graph new registration.
        $labels = [];
        $data = [];
        $datasets = [];

        $transactionCount = Transaction::get()->count();

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
        // Generate data for the first graph
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->get()->groupBy(function ($transaction) use ($interval) {
            return $interval === 'hour' ? $transaction->created_at->format('h a') : $transaction->created_at->format('d-m-Y');
        });

        $startDateCopy = $startDate->copy();
        while ($startDateCopy->lte($endDate)) {
            $date = $interval === 'hour' ? $startDateCopy->format('h a') : $startDateCopy->format('d-m-Y');
            $count = isset($transactions[$date]) ? $transactions[$date]->count() : 0;
            $labels[] = $date;
            $data[] = $count;
            $startDateCopy->add(1, $interval);
        }

        $datasets[] = [
            'label' => trans('cruds.dashboard.fields.count'),
            'data' => $data,
            'borderColor' => '#2861f0',
            'pointBorderColor' => "#2861f0",
            'pointBackgroundColor' => "#2861f0",
            'pointHoverBackgroundColor' => "#000000",
            'pointHoverBorderColor' => "#000000",
            // 'xAxisText' => $interval === "day" ? trans('cruds.dashboard.labels.day') : trans('cruds.dashboard.labels.hour'),
            'yAxisText' => trans('cruds.dashboard.fields.transaction_count'),
            // 'pluginText' => trans('cruds.dashboard.fields.transaction_alert_graph'),
        ];

        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'labels' => $labels,
                'datasets' => $datasets
            ]);
        }

        // Return view with all necessary data
        return view('backend.transaction.report', compact('transactionCount', 'labels', 'datasets'));
    }
}
