<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Order;
use App\Traits\ReportTrait;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ReportTrait;

    public function orders()
    {
        $dataset = $this->generateReport(Order::query(), '注文数');
        return $dataset;
    }

    public function customers()
    {
        $dataset = $this->generateReport(User::query(), '顧客数');
        return $dataset;
    }

    private function generateReport($query, $label)
    {
        $fromToDate = $this->getFromToDate();

        $query
            ->selectRaw('CAST(created_at as DATE) AS day, COUNT(*) as count')
            ->groupBy(DB::raw('CAST(created_at as DATE)'));

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $query->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate);
        }

        $records = $query->orderBy(DB::raw('CAST(created_at as DATE)'), 'asc')->get()->keyBy('day')->toArray();

        if (!$fromToDate) {
            $keys = array_keys($records);
            $fromDate = reset($keys);
            $toDate = end($keys);
        }

        $start = new DateTime($fromDate);
        $end = (new DateTime($toDate));
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        $dates = [];
        foreach ($period as $date) {
            $dates[$date->format('Y-m-d')] = ['day' => $date->format('Y-m-d'), 'count' => 0];
        }

        $records = array_replace($dates, $records);

        $labels = array_keys($records);
        $counts = array_column($records, 'count');

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => $label,
                'backgroundColor' => 'rgb(220, 229, 70)',
                'data' => $counts
            ]]
        ];
    }
}
