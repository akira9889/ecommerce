<?php

namespace App\Traits;

use Carbon\Carbon;

trait ReportTrait
{
    private function getFromToDate()
    {
        $d = \request()->get('d');
        $array = [
            'today' => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            '1d' => [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()],
            '1w' => [Carbon::now()->subDays(7), Carbon::now()],
            '2w' => [Carbon::now()->subDays(14), Carbon::now()],
            '1m' => [Carbon::now()->subDays(30), Carbon::now()],
            '3m' => [Carbon::now()->subDays(60), Carbon::now()],
            '6m' => [Carbon::now()->subDays(180), Carbon::now()],
            '1y' => [Carbon::now()->subDays(365), Carbon::now()],
        ];

        return $array[$d] ?? null;
    }
}
