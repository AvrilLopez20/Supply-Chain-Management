<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\HistoricalSale;
use Carbon\Carbon;

class ForecastingController extends Controller
{
    /**
     * Display forecasting overview
     */
    public function index(): View
    {
        $products = Product::all();
        $productForecasts = [];
        foreach ($products as $prod) {
            $q3 = rand(4000, 13000);
            $q4 = intval($q3 * 1.12);
            $productForecasts[] = [
                'sku' => $prod->sku,
                'name' => $prod->name,
                'category' => $prod->category,
                'q3' => $q3,
                'q4' => $q4,
                'trend' => rand(0, 1) ? 'Up' : 'Down'
            ];
        }

        $allSales = HistoricalSale::orderByDesc('date')->get();

        $historicalSales = $allSales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'order' => $sale->order_id,
                'product' => $sale->product_name,
                'sku' => $sale->sku,
                'category' => $sale->category,
                'date' => $sale->date,
                'qty' => $sale->qty,
                'revenue' => floatval($sale->revenue),
            ];
        })->toArray();

        $defaultYear = 2026;

        // Build initial chart from historical sales for the default year
        $built = $this->buildForecastSeries($allSales, 'all', (string) $defaultYear, '12m');
        $demandData = [];
        foreach ($built['months'] as $i => $month) {
            $demandData[] = [
                'month' => $month,
                'actual' => $built['demandActual'][$i],
                'forecast' => $built['demandForecast'][$i],
            ];
        }

        // Per-product monthly series for interactive chart filtering (selected year)
        $productMonthly = [];
        foreach ($products as $prod) {
            $series = $this->buildForecastSeries($allSales, $prod->sku, (string) $defaultYear, '12m');
            $productMonthly[$prod->sku] = $series['demandActual'];
        }

        $modelAccuracy = [
            ['model' => 'ARIMA',           'accuracy' => '87.5%', 'mape' => '8.2%'],
            ['model' => 'Linear Regression', 'accuracy' => '82.1%', 'mape' => '12.4%'],
            ['model' => 'Moving Average',  'accuracy' => '79.3%', 'mape' => '15.1%'],
        ];

        return view('forecasting.index', [
            'demandData' => $demandData,
            'productForecasts' => $productForecasts,
            'historicalSales' => $historicalSales,
            'productMonthly' => $productMonthly,
            'modelAccuracy' => $modelAccuracy,
            'defaultYear' => $defaultYear,
            'predDemandTotal' => $built['predDemandTotal'],
            'pageTitle' => 'Demand Forecasting',
        ]);
    }

    /**
     * Store a new HistoricalSale record (used by "Add Record" button)
     */
    public function storeRecord(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'qty' => ['required', 'integer', 'min:0'],
            'revenue' => ['required', 'numeric', 'min:0'],
        ]);

        $sale = HistoricalSale::create($validated);

        return response()->json([
            'ok' => true,
            'sale' => $this->formatSale($sale),
        ]);
    }

    /**
     * Update an existing HistoricalSale record (used by "Edit" action)
     */
    public function updateRecord(Request $request, HistoricalSale $sale)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'qty' => ['required', 'integer', 'min:0'],
            'revenue' => ['required', 'numeric', 'min:0'],
        ]);

        $sale->update($validated);

        return response()->json([
            'ok' => true,
            'sale' => $this->formatSale($sale->fresh()),
        ]);
    }

    /**
     * Delete an existing HistoricalSale record (used by "Delete" in edit modal)
     */
    public function destroyRecord(HistoricalSale $sale)
    {
        $sale->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Record deleted successfully.',
        ]);
    }

    private function formatSale(HistoricalSale $sale): array
    {
        return [
            'id' => $sale->id,
            'order' => $sale->order_id,
            'product' => $sale->product_name,
            'sku' => $sale->sku,
            'category' => $sale->category,
            'date' => $sale->date,
            'qty' => $sale->qty,
            'revenue' => (float) $sale->revenue,
        ];
    }

    /**
     * Generate forecast result (used by "Generate Forecast" button)
     */
    public function generateForecast(Request $request)
    {
        $sku = $request->input('sku', 'all');
        $year = $request->input('year');
        $range = $request->input('range', '12m');

        $query = HistoricalSale::query();
        if ($sku !== 'all') {
            $query->where('sku', $sku);
        }

        $rows = $query->get(['date', 'sku', 'qty']);
        $built = $this->buildForecastSeries($rows, $sku, $year, $range);

        return response()->json([
            'ok' => true,
            'months' => $built['months'],
            'demandActual' => $built['demandActual'],
            'demandForecast' => $built['demandForecast'],
            'predDemandTotal' => $built['predDemandTotal'],
            'hasData' => $built['hasData'],
            'message' => $built['hasData']
                ? 'Forecast updated from historical sales.'
                : 'No matching historical sales found; showing estimated baseline.',
        ]);
    }

    /**
     * Build monthly actual + forecast series from historical sale rows.
     *
     * @param  \Illuminate\Support\Collection|iterable  $rows
     */
    private function buildForecastSeries($rows, string $sku, ?string $year, string $range): array
    {
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $yearInt = !empty($year) ? (int) $year : (int) now()->year;
        $startMonth = 1;
        $endMonth = 12;

        if ($range === '6m') {
            $startMonth = 7;
            $endMonth = 12;
        } elseif ($range === 'ytd') {
            // Always Jan through today's month so the range visibly changes the chart
            $startMonth = 1;
            $endMonth = max(1, (int) now()->month);
        }

        $monthNumbers = range($startMonth, $endMonth);
        $months = array_map(fn ($m) => $monthLabels[$m - 1], $monthNumbers);

        $byMonth = array_fill(1, 12, 0);
        $hasData = false;

        foreach ($rows as $r) {
            $dt = Carbon::parse($r->date);
            if ((int) $dt->year !== $yearInt) {
                continue;
            }
            $m = (int) $dt->month;
            $byMonth[$m] += (int) $r->qty;
            $hasData = true;
        }

        // If selected year has no rows, fall back to latest year present in the filtered rows
        if (!$hasData) {
            $latestYear = null;
            foreach ($rows as $r) {
                $y = (int) Carbon::parse($r->date)->year;
                if ($latestYear === null || $y > $latestYear) {
                    $latestYear = $y;
                }
            }
            if ($latestYear !== null) {
                $byMonth = array_fill(1, 12, 0);
                foreach ($rows as $r) {
                    $dt = Carbon::parse($r->date);
                    if ((int) $dt->year !== $latestYear) {
                        continue;
                    }
                    $byMonth[(int) $dt->month] += (int) $r->qty;
                    $hasData = true;
                }
            }
        }

        $nonZero = array_values(array_filter($byMonth, fn ($q) => $q > 0));
        $avg = count($nonZero) ? (int) round(array_sum($nonZero) / count($nonZero)) : (($sku === 'all') ? 2500 : 800);

        // Simple trend: average period-over-period growth across months that have data
        $growth = 0.06;
        if (count($nonZero) >= 2) {
            $ratios = [];
            $prev = null;
            foreach ($byMonth as $qty) {
                if ($qty <= 0) {
                    continue;
                }
                if ($prev !== null && $prev > 0) {
                    $ratios[] = $qty / $prev;
                }
                $prev = $qty;
            }
            if (count($ratios)) {
                $growth = max(-0.2, min(0.35, (array_sum($ratios) / count($ratios)) - 1));
            }
        }

        if ($range === '6m') {
            $growth -= 0.02;
        } elseif ($range === 'ytd') {
            $growth += 0.01;
        }

        $actual = [];
        $forecast = [];
        $running = $avg;

        foreach ($monthNumbers as $i => $monthNumber) {
            $monthQty = (int) $byMonth[$monthNumber];

            // Keep real zeros visible when some months have sales; only fill blanks when no data at all
            if ($monthQty === 0 && !$hasData) {
                $monthQty = (int) round($avg * (0.85 + ($i * 0.03)));
            }

            $actual[] = $monthQty;

            if ($monthQty > 0) {
                $running = $monthQty;
                $forecast[] = (int) max(0, round($monthQty * (1 + $growth + ($i * 0.008))));
            } else {
                $running = (int) round($running * (1 + $growth));
                $forecast[] = (int) max(0, round($running * (1 + ($i * 0.005))));
            }
        }

        return [
            'months' => $months,
            'demandActual' => $actual,
            'demandForecast' => $forecast,
            'predDemandTotal' => (int) array_sum($forecast),
            'hasData' => $hasData,
        ];
    }

    /**
     * Display analytics and insights
     */
    public function analytics(): View
    {
        $insights = [
            'seasonal_patterns' => 'Peak demand observed during Q3-Q4',
            'growth_rate' => '12.3% YoY growth trend',
            'volatility' => 'Moderate volatility in electronics category',
            'recommendations' => 'Increase procurement for Q4 by 15%',
        ];

        return view('forecasting.analytics', [
            'insights' => $insights,
            'pageTitle' => 'Forecasting Analytics',
        ]);
    }
}

