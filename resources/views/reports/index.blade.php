@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold text-erp-text">Reports & Analytics</h1>
    <p class="text-erp-textMuted mt-1">Generate and review comprehensive supply chain reports.</p>
  </div>

  <!-- Report Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach ($reportTypes as $report)
    <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border hover:shadow-md transition cursor-pointer">
      <h3 class="text-lg font-bold text-erp-text">{{ $report['title'] }}</h3>
      <p class="text-erp-textMuted text-sm mt-2">{{ $report['description'] }}</p>
      <div class="mt-4 flex gap-2">
        @if ($report['id'] === 'inventory')
          <a href="{{ route('reports.inventory') }}" class="text-erp-darkGreen font-semibold hover:underline">View Report →</a>
        @elseif ($report['id'] === 'procurement')
          <a href="{{ route('reports.procurement') }}" class="text-erp-darkGreen font-semibold hover:underline">View Report →</a>
        @elseif ($report['id'] === 'logistics')
          <a href="{{ route('reports.logistics') }}" class="text-erp-darkGreen font-semibold hover:underline">View Report →</a>
        @else
          <a href="#" class="text-erp-darkGreen font-semibold hover:underline">View Report →</a>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  <!-- Export Options -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
    <h2 class="text-lg font-bold text-erp-text mb-4">Export Reports</h2>
    <div class="flex flex-wrap gap-3">
      <a href="{{ route('reports.export', 'pdf') }}" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg hover:opacity-90 transition">
        📄 Export as PDF
      </a>
      <a href="{{ route('reports.export', 'csv') }}" class="bg-erp-lightGreen text-erp-text px-4 py-2 rounded-lg hover:opacity-90 transition">
        📊 Export as CSV
      </a>
      <a href="{{ route('reports.export', 'excel') }}" class="bg-erp-softYellow text-erp-text px-4 py-2 rounded-lg hover:opacity-90 transition">
        📈 Export as Excel
      </a>
    </div>
  </div>
</div>
@endsection
