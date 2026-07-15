@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold text-erp-text">Logistics Report</h1>
    <p class="text-erp-textMuted mt-1">Generated: {{ $report['generated'] }}</p>
  </div>

  <!-- Summary Stats -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border">
      <p class="text-erp-textMuted text-sm">Total Shipments</p>
      <p class="text-3xl font-bold text-erp-darkGreen mt-2">{{ number_format($report['summary']['total_shipments']) }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border">
      <p class="text-erp-textMuted text-sm">Delivered</p>
      <p class="text-3xl font-bold text-erp-darkGreen mt-2">{{ number_format($report['summary']['delivered']) }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border">
      <p class="text-erp-textMuted text-sm">In Transit</p>
      <p class="text-3xl font-bold text-erp-warmOrange mt-2">{{ $report['summary']['in_transit'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border">
      <p class="text-erp-textMuted text-sm">Avg Delivery Time</p>
      <p class="text-3xl font-bold text-erp-darkGreen mt-2">{{ $report['summary']['avg_delivery_time'] }}</p>
    </div>
  </div>

  <!-- Export -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
    <a href="{{ route('reports.export', 'pdf') }}" class="bg-erp-darkGreen text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
      📄 Export as PDF
    </a>
  </div>
</div>
@endsection
