@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold text-erp-text">Create Shipment</h1>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border max-w-2xl">
    <form method="POST" action="{{ route('logistics.store') }}">
      @csrf
      
      <div class="space-y-4">
        <div>
          <label class="block text-erp-text font-semibold mb-2">From Warehouse</label>
          <select name="from" class="w-full px-4 py-2 border border-erp-border rounded-lg" required>
            <option value="">Select origin...</option>
            <option value="Central Hub (NY)">Central Hub (NY)</option>
            <option value="West Coast Dist (CA)">West Coast Dist (CA)</option>
            <option value="Southern Hub (TX)">Southern Hub (TX)</option>
            <option value="Midwest Storage (IL)">Midwest Storage (IL)</option>
          </select>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">To Destination</label>
          <input type="text" name="to" placeholder="Destination" class="w-full px-4 py-2 border border-erp-border rounded-lg" required>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">Carrier</label>
          <select name="carrier" class="w-full px-4 py-2 border border-erp-border rounded-lg" required>
            <option value="">Select carrier...</option>
            <option value="FastFreight Express">FastFreight Express</option>
            <option value="National Logistics Co.">National Logistics Co.</option>
          </select>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">Items Count</label>
          <input type="number" name="items" class="w-full px-4 py-2 border border-erp-border rounded-lg" required min="1">
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" class="bg-erp-darkGreen text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
            Create Shipment
          </button>
          <a href="{{ route('logistics.index') }}" class="bg-gray-200 text-erp-text px-6 py-2 rounded-lg hover:opacity-90 transition">
            Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
