@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold text-erp-text">Create New Inventory Item</h1>
    <p class="text-erp-textMuted mt-1">Add a new item to your inventory</p>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border max-w-2xl">
    <form method="POST" action="{{ route('inventory.store') }}">
      @csrf
      
      <div class="space-y-4">
        <div>
          <label class="block text-erp-text font-semibold mb-2">Item Name</label>
          <input type="text" name="name" class="w-full px-4 py-2 border border-erp-border rounded-lg focus:outline-none focus:border-erp-darkGreen" required>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">SKU</label>
          <input type="text" name="sku" class="w-full px-4 py-2 border border-erp-border rounded-lg focus:outline-none focus:border-erp-darkGreen" required>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">Warehouse</label>
          <select name="warehouse" class="w-full px-4 py-2 border border-erp-border rounded-lg focus:outline-none focus:border-erp-darkGreen" required>
            <option value="">Select a warehouse</option>
            @foreach ($warehouses as $wh)
              <option value="{{ $wh->name }}">{{ $wh->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">Quantity</label>
          <input type="number" name="qty" class="w-full px-4 py-2 border border-erp-border rounded-lg focus:outline-none focus:border-erp-darkGreen" required min="0">
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" class="bg-erp-darkGreen text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
            Create Item
          </button>
          <a href="{{ route('inventory.index') }}" class="bg-gray-200 text-erp-text px-6 py-2 rounded-lg hover:opacity-90 transition">
            Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
