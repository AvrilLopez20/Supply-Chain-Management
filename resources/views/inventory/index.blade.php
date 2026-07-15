@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-erp-text">Inventory & Warehouses</h1>
      <p class="text-erp-textMuted mt-1">Manage stock levels, allocations, and warehouse capacities.</p>
    </div>
    <div class="flex items-center gap-3">
      <button class="px-3 py-2 rounded-lg border border-erp-border text-sm">Transfer Stocks</button>
      <a href="{{ route('inventory.create') }}" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg hover:opacity-90 transition">Allocate Inventory</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold text-erp-text">Reduce Search Time</h2>
            <p class="text-sm text-erp-textMuted mt-1">Instantly locate any item's warehouse, aisle, and bin.</p>
          </div>
          <div class="text-sm text-erp-textMuted">{{ count($itemIndex) }} items found</div>
        </div>

        <div class="mt-4">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-erp-textMuted"></i>
            <input id="inventorySearch" placeholder="Search by item name, SKU, or warehouse..." class="w-full rounded-lg border border-erp-border py-3 pl-10 pr-4" />
          </div>

          <div id="inventoryCards" class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($itemIndex as $it)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-erp-border" data-search="{{ strtolower($it['name'] . ' ' . $it['sku'] . ' ' . $it['warehouse']) }}">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-erp-textMuted">{{ $it['sku'] }}</div>
                  <h3 class="text-lg font-semibold text-erp-text">{{ $it['name'] }}</h3>
                  <div class="text-xs text-erp-textMuted mt-2">{{ $it['warehouse'] }}<br>Aisle {{ $it['aisle'] }} · Bin {{ $it['bin'] }}</div>
                </div>
                <div class="text-right">
                  <div class="text-sm text-erp-textMuted">Units</div>
                  <div class="text-xl font-bold text-erp-text">{{ number_format($it['qty']) }}</div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-semibold text-erp-text">Cost Optimizer</h3>
        <p class="text-sm text-erp-textMuted mt-1">Reduce transportation and holding costs with data-driven moves.</p>

        <div class="mt-4 space-y-4">
          <div class="rounded-lg border border-erp-border p-4">
            <div class="flex items-start justify-between">
              <div>
                <div class="text-sm font-medium">Consolidate West Coast shipments</div>
                <div class="text-xs text-erp-textMuted mt-1">Combine 3 partial truckloads to West Coast Dist (CA) into 1 full load.</div>
              </div>
              <div class="text-sm font-semibold text-erp-darkGreen">Save $4,200/mo</div>
            </div>
            <div class="mt-3 text-right">
              <button class="px-3 py-1 text-sm border rounded-md">Dismiss</button>
              <button class="ml-2 px-3 py-1 bg-erp-darkGreen text-white rounded-md">Apply</button>
            </div>
          </div>

          <div class="rounded-lg border border-erp-border p-4">
            <div class="flex items-start justify-between">
              <div>
                <div class="text-sm font-medium">Rebalance overflow to Southern Hub</div>
                <div class="text-xs text-erp-textMuted mt-1">Shift 2,000 units closer to demand and shorten last-mile routes.</div>
              </div>
              <div class="text-sm font-semibold text-erp-darkGreen">Save $3,100/mo</div>
            </div>
            <div class="mt-3 text-right">
              <button class="px-3 py-1 text-sm border rounded-md">Dismiss</button>
              <button class="ml-2 px-3 py-1 bg-erp-darkGreen text-white rounded-md">Apply</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-semibold text-erp-text">Warehouse Status</h3>
        <div class="mt-4 grid grid-cols-1 gap-3">
          @foreach ($warehouseInventory as $w)
          <div class="rounded-lg border border-erp-border p-4 bg-white">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-medium text-erp-text">{{ $w['name'] }}</div>
                <div class="text-xs text-erp-textMuted">ID: {{ $w['id'] }} · {{ number_format($w['items']) }} items</div>
              </div>
              <div class="text-sm text-erp-textMuted">{{ $w['status'] }}</div>
            </div>
            <div class="mt-3">
              <div class="w-full bg-erp-bg rounded-full h-2">
                <div class="h-2 rounded-full bg-erp-lightGreen" style="width: {{ $w['capacity'] }}%"></div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-semibold text-erp-text">Alerts</h3>
        <div class="mt-4 space-y-3 text-sm text-erp-textMuted">
          @foreach ($lowStockItems as $low)
          <div class="rounded-lg border border-erp-border p-3 bg-white flex items-center justify-between">
            <div>
              <div class="font-medium text-erp-text">{{ $low['item'] }}</div>
              <div class="text-xs">{{ $low['warehouse'] }}</div>
            </div>
            <div class="text-right">
              <div class="font-semibold text-erp-darkGreen">{{ $low['current'] }} / {{ $low['required'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-semibold text-erp-text">Quick Stock Transfer</h3>
        <form id="quickTransfer" class="mt-4 space-y-3">
          <div>
            <label class="text-sm text-erp-textMuted">From Warehouse</label>
            <select id="fromWarehouse" class="w-full rounded-lg border border-erp-border px-3 py-2">
              <option>Select Origin...</option>
              @foreach ($warehouseInventory as $w)
              <option value="{{ $w['id'] }}">{{ $w['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-sm text-erp-textMuted">To Warehouse</label>
            <select id="toWarehouse" class="w-full rounded-lg border border-erp-border px-3 py-2">
              <option>Select Destination...</option>
              @foreach ($warehouseInventory as $w)
              <option value="{{ $w['id'] }}">{{ $w['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-sm text-erp-textMuted">Item to Transfer</label>
            <select id="transferItem" class="w-full rounded-lg border border-erp-border px-3 py-2">
              <option>Search items...</option>
              @foreach ($itemIndex as $it)
              <option value="{{ $it['sku'] }}">{{ $it['name'] }} ({{ $it['sku'] }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-sm text-erp-textMuted">Quantity</label>
            <input id="transferQty" type="number" min="1" class="w-full rounded-lg border border-erp-border px-3 py-2" placeholder="Enter amount" />
          </div>
          <div>
            <button type="button" id="doTransfer" class="w-full bg-erp-darkGreen text-white px-4 py-2 rounded-lg">Initiate Transfer</button>
          </div>
        </form>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-semibold text-erp-text">Recent Allocations</h3>
        <div class="mt-4 space-y-3">
          @foreach ($allocations as $a)
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium text-erp-text">{{ $a['item'] }}</div>
              <div class="text-xs text-erp-textMuted">Allocated {{ number_format($a['qty']) }} units to {{ $a['to'] }}</div>
            </div>
            <div class="text-xs text-erp-textMuted">{{ $a['time'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('inventorySearch');
  const cards = Array.from(document.querySelectorAll('#inventoryCards [data-search]'));
  searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    cards.forEach(c => {
      const txt = c.dataset.search || '';
      c.style.display = (!q || txt.includes(q)) ? '' : 'none';
    });
  });

  // Quick transfer (real API integration)
  document.getElementById('doTransfer').addEventListener('click', function () {
    const from = document.getElementById('fromWarehouse').value;
    const to = document.getElementById('toWarehouse').value;
    const item = document.getElementById('transferItem').value;
    const qty = parseInt(document.getElementById('transferQty').value, 10);
    if (!from || !to || !item || isNaN(qty) || qty <= 0) {
      alert('Please complete all fields with valid values to perform a transfer.');
      return;
    }
    
    document.getElementById('doTransfer').disabled = true;
    document.getElementById('doTransfer').innerText = 'Transferring...';

    fetch('/api/transfer', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        from: from,
        to: to,
        sku: item,
        qty: qty
      })
    })
    .then(response => {
      return response.json().then(data => {
        if (!response.ok) {
          throw new Error(data.message || 'Transfer failed');
        }
        return data;
      });
    })
    .then(data => {
      alert('Success: ' + data.message);
      location.reload();
    })
    .catch(err => {
      alert('Error: ' + err.message);
      document.getElementById('doTransfer').disabled = false;
      document.getElementById('doTransfer').innerText = 'Initiate Transfer';
    });
  });
});
</script>

@endsection
