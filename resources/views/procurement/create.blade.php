@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-bold text-erp-text">Create Purchase Order</h1>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border max-w-2xl">
    <form method="POST" action="{{ route('procurement.store') }}">
      @csrf
      
      <div class="space-y-4">
        <div>
          <label class="block text-erp-text font-semibold mb-2">Select Supplier</label>
          <select name="supplier_id" class="w-full px-4 py-2 border border-erp-border rounded-lg" required>
            <option value="">Choose a supplier...</option>
            @foreach ($suppliers as $supplier)
              <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-erp-text font-semibold mb-2">Items</label>
          <div id="items-container" class="space-y-2">
            <div class="flex gap-2">
              <input type="text" name="items[0][sku]" placeholder="SKU" class="flex-1 px-4 py-2 border border-erp-border rounded-lg">
              <input type="number" name="items[0][qty]" placeholder="Qty" class="w-24 px-4 py-2 border border-erp-border rounded-lg">
            </div>
          </div>
          <button type="button" class="mt-2 text-erp-darkGreen font-semibold">+ Add Item</button>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" class="bg-erp-darkGreen text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
            Create Order
          </button>
          <a href="{{ route('procurement.index') }}" class="bg-gray-200 text-erp-text px-6 py-2 rounded-lg hover:opacity-90 transition">
            Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
