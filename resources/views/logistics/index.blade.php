@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-erp-text">Logistics & Transportation</h1>
      <p class="text-erp-textMuted mt-1">Monitor shipments, routes, and delivery schedules.</p>
    </div>
    <div class="flex items-center gap-3">
      <button class="px-3 py-2 rounded-lg border border-erp-border text-sm">Track Shipment</button>
      <a href="{{ route('logistics.create') }}" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg hover:opacity-90 transition">Schedule Delivery</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border flex items-center justify-between">
          <div>
            <p class="text-sm text-erp-textMuted">In Transit</p>
            <h3 class="text-2xl font-bold text-erp-text">{{ collect($shipments)->where('status','In Transit')->count() }}</h3>
          </div>
          <div class="text-3xl text-erp-darkGreen">🚚</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border flex items-center justify-between">
          <div>
            <p class="text-sm text-erp-textMuted">Delivered (Today)</p>
            <h3 class="text-2xl font-bold text-erp-text">{{ collect($shipments)->where('status','Delivered')->count() }}</h3>
          </div>
          <div class="text-3xl text-erp-lightGreen">✅</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-erp-border flex items-center justify-between">
          <div>
            <p class="text-sm text-erp-textMuted">Delayed</p>
            <h3 class="text-2xl font-bold text-erp-text">0</h3>
          </div>
          <div class="text-3xl text-erp-warmOrange">⚠️</div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h2 class="text-lg font-bold text-erp-text mb-4">Live Route Tracking</h2>
        <div id="map" class="w-full h-64 rounded-lg bg-erp-bg"></div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg font-bold text-erp-text">Active Shipments</h2>
          <div class="flex items-center gap-2">
            <input id="shipmentSearch" placeholder="Search Tracking ID..." class="rounded-lg border border-erp-border px-3 py-2 text-sm" />
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="border-b border-erp-border">
              <tr>
                <th class="py-3 px-4 text-erp-text font-semibold">Tracking ID</th>
                <th class="py-3 px-4 text-erp-text font-semibold">Destination</th>
                <th class="py-3 px-4 text-erp-text font-semibold">Carrier</th>
                <th class="py-3 px-4 text-erp-text font-semibold">Status</th>
                <th class="py-3 px-4 text-erp-text font-semibold">ETA</th>
                <th class="py-3 px-4 text-erp-text font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody id="shipmentsTable">
              @foreach ($shipments as $s)
              <tr class="border-b border-erp-border hover:bg-erp-bg transition" data-tracking="{{ strtolower($s['id']) }}">
                <td class="py-3 px-4 font-semibold">{{ $s['id'] }}</td>
                <td class="py-3 px-4">{{ $s['to'] }}</td>
                <td class="py-3 px-4">FastFreight</td>
                <td class="py-3 px-4"><span class="px-3 py-1 rounded-full bg-erp-lightGreen/20 text-erp-darkGreen text-sm">{{ $s['status'] }}</span></td>
                <td class="py-3 px-4">{{ $s['eta'] }}</td>
                <td class="py-3 px-4"><a href="#" class="text-erp-darkGreen">View</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-sm text-erp-textMuted">Today's Schedule</h3>
        <div class="mt-3 space-y-3 text-sm text-erp-textMuted">
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium text-erp-text">Morning Dispatch</div>
              <div class="text-xs">08:30 AM</div>
            </div>
            <div class="text-sm text-erp-textMuted">On Time</div>
          </div>
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium text-erp-text">Delivery to Retail #45</div>
              <div class="text-xs">10:30 AM</div>
            </div>
            <div class="text-sm text-erp-textMuted">ETA</div>
          </div>
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium text-erp-text">Evening Dispatch</div>
              <div class="text-xs">05:00 PM</div>
            </div>
            <div class="text-sm text-erp-textMuted">Planned</div>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
        <h3 class="text-lg font-bold text-erp-text">Live Route Integration</h3>
        <p class="text-sm text-erp-textMuted mt-2">Interactive Map Integration (powered by Leaflet)</p>
      </div>
    </div>
  </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<!-- Leaflet MarkerCluster -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Initialize map
  const map = L.map('map').setView([39.8283, -98.5795], 4);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const shipments = @json($shipments);

  // Marker clustering
  const markers = L.markerClusterGroup();
  const polylineCoords = [];

  shipments.forEach(s => {
    if (s.lat && s.lng) {
      const m = L.marker([s.lat, s.lng]);
      m.bindPopup(`<strong>${s.id}</strong><br>${s.to}<br>Status: ${s.status}`);
      markers.addLayer(m);
      polylineCoords.push([s.lat, s.lng]);
    }
  });

  map.addLayer(markers);

  // Draw polyline connecting shipment points (sample route visualization)
  if (polylineCoords.length >= 2) {
    const routeLine = L.polyline(polylineCoords, { color: '#3b82f6', weight: 3, opacity: 0.7 }).addTo(map);
    try {
      map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
    } catch (e) {
      // ignore fitBounds errors
    }
  } else if (polylineCoords.length === 1) {
    map.setView(polylineCoords[0], 8);
  }

  // Shipment search
  const search = document.getElementById('shipmentSearch');
  const rows = Array.from(document.querySelectorAll('#shipmentsTable tr'));
  search.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    rows.forEach(r => {
      const t = r.dataset.tracking || '';
      r.style.display = (!q || t.includes(q)) ? '' : 'none';
    });
  });
});
</script>

@endsection
