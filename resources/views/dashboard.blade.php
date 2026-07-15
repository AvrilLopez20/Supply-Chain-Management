@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-erp-text">Dashboard Overview</h1>
      <p class="text-erp-textMuted mt-1">Welcome back! Here's what's happening in your supply chain today.</p>
    </div>

  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach ($summaryData as $item)
    <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $item['bg'] }} {{ $item['color'] }}">
        <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6"></i>
      </div>
      <div>
        <p class="text-sm text-erp-textMuted font-medium">{{ $item['title'] }}</p>
        <h3 class="text-2xl font-bold text-erp-text">{{ $item['value'] }}</h3>
      </div>
    </div>
    @endforeach
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-erp-text">Sales vs Forecast</h2>
        <span class="rounded-full bg-erp-darkGreen/10 px-2.5 py-1 text-xs font-semibold text-erp-darkGreen">+12.4%</span>
      </div>
      <div class="h-64 rounded-lg border border-erp-border bg-erp-bg p-4">
        <canvas id="salesChart" class="h-full w-full"></canvas>
      </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-erp-text">Inventory Levels by Category</h2>
        <span class="text-sm text-erp-textMuted">Healthy</span>
      </div>
      <div class="space-y-3">
        @foreach ($inventoryData as $item)
          <div>
            <div class="mb-1 flex items-center justify-between text-sm">
              <span class="text-erp-text">{{ $item['name'] }}</span>
              <span class="text-erp-textMuted">{{ $item['level'] }}%</span>
            </div>
            <div class="h-2 rounded-full bg-erp-bg">
              <div class="h-2 rounded-full bg-erp-lightGreen" style="width: {{ $item['level'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const salesData = @json($salesData);
  const ctx = document.getElementById('salesChart');

  if (!ctx || !window.Chart) return;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: salesData.map((point) => point.name),
      datasets: [
        {
          label: 'Actual',
          data: salesData.map((point) => point.actual),
          borderColor: '#468C32',
          backgroundColor: 'rgba(70, 140, 50, 0.15)',
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 6
        },
        {
          label: 'Forecast',
          data: salesData.map((point) => point.forecast),
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.12)',
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(226, 232, 240, 0.8)' }
        },
        x: {
          grid: { display: false }
        }
      }
    }
  });
});
</script>
@endsection
