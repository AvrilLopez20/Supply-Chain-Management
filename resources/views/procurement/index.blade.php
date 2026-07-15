@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-bold text-erp-text">Procurement & Vendors</h1>
      <p class="text-erp-textMuted mt-1">Manage purchase orders, suppliers, and procurement costs.</p>
    </div>
    <a href="{{ route('procurement.create') }}" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg hover:opacity-90 transition">
      + New Purchase Order
    </a>
  </div>

  <!-- Purchase Orders -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border overflow-x-auto">
    <h2 class="text-lg font-bold text-erp-text mb-4">Purchase Orders</h2>
    <table class="w-full">
      <thead class="border-b border-erp-border">
        <tr>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">PO ID</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Supplier</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Total</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Status</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($poIndex as $order)
        <tr class="border-b border-erp-border hover:bg-erp-bg transition">
          <td class="py-3 px-4 text-erp-text font-semibold">{{ $order['id'] }}</td>
          <td class="py-3 px-4 text-erp-text">{{ $order['supplier'] }}</td>
          <td class="py-3 px-4 text-erp-text">{{ $order['total'] }}</td>
          <td class="py-3 px-4">
            <span class="px-3 py-1 rounded-full bg-erp-lightGreen/20 text-erp-darkGreen text-sm">
              {{ $order['status'] }}
            </span>
          </td>
          <td class="py-3 px-4">
            <div class="flex gap-2">
              <a href="#" class="text-erp-darkGreen hover:underline text-sm">View</a>
              <a href="#" class="text-erp-darkGreen hover:underline text-sm">Edit</a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Suppliers -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border overflow-x-auto">
    <h2 class="text-lg font-bold text-erp-text mb-4">Active Suppliers</h2>
    <table class="w-full">
      <thead class="border-b border-erp-border">
        <tr>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Supplier Name</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Rating</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Orders</th>
          <th class="text-left py-3 px-4 text-erp-text font-semibold">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($suppliers as $supplier)
        <tr class="border-b border-erp-border hover:bg-erp-bg transition">
          <td class="py-3 px-4 text-erp-text">{{ $supplier['name'] }}</td>
          <td class="py-3 px-4 text-erp-text">⭐ {{ $supplier['rating'] }}</td>
          <td class="py-3 px-4 text-erp-text">{{ $supplier['orders'] }}</td>
          <td class="py-3 px-4">
            <span class="px-3 py-1 rounded-full bg-erp-lightGreen/20 text-erp-darkGreen text-sm">
              {{ $supplier['status'] }}
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
