php @extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-erp-text">Demand Forecasting</h1>
      <p class="text-erp-textMuted mt-1">Analyze historical data to predict future inventory needs.</p>
    </div>
    <div class="flex items-center gap-2">
      <button id="addRecord" class="px-3 py-2 rounded-lg border border-erp-border text-sm">+ Add Record</button>
      <button id="generateForecast" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg">Generate Forecast</button>
    </div>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
    <div class="flex gap-6 items-center mb-4 flex-wrap">
      <div class="flex items-center gap-2">
        <label class="text-sm text-erp-textMuted">Filters:</label>
        <select id="filterProduct" class="rounded-lg border border-erp-border px-3 py-2">
          <option value="all">All Products</option>
          @foreach ($productForecasts as $p)
          <option value="{{ $p['sku'] }}">{{ $p['name'] }}</option>
          @endforeach
        </select>
      </div>
      <div class="relative" id="yearCombo">
        <input
          type="text"
          id="filterYear"
          inputmode="numeric"
          autocomplete="off"
          value="{{ $defaultYear }}"
          placeholder="Type or pick year"
          class="rounded-lg border border-erp-border px-3 py-2 w-36 pr-8"
        />
        <button type="button" id="yearComboToggle" class="absolute right-2 top-1/2 -translate-y-1/2 text-erp-textMuted text-xs" aria-label="Open year list">▾</button>
        <ul id="yearComboList" class="hidden absolute z-20 mt-1 w-full max-h-48 overflow-auto rounded-lg border border-erp-border bg-white shadow-lg text-sm"></ul>
      </div>
      <div>
        <select id="filterRange" class="rounded-lg border border-erp-border px-3 py-2">
          <option value="12m">Last 12 Months</option>
          <option value="6m">Last 6 Months</option>
          <option value="ytd">Year to Date</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 bg-erp-bg p-4 rounded-lg">
        <canvas id="demandChart" class="w-full h-60"></canvas>
      </div>
      <div class="bg-white p-4 rounded-lg border border-erp-border">
        <h3 class="text-sm text-erp-textMuted">Forecast Summary</h3>
        <div id="forecastSummary" class="mt-3 bg-erp-lightGreen/10 p-3 rounded">
          <div class="text-lg font-bold text-erp-darkGreen">Predicted Demand</div>
          <div id="predDemand" class="text-2xl font-bold text-erp-darkGreen">{{ number_format($predDemandTotal) }} units</div>
          <div id="forecastDrivers" class="text-sm text-erp-textMuted mt-2">Based on historical sales for {{ $defaultYear }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
    <h2 class="text-lg font-bold text-erp-text mb-4">Historical Sales Data</h2>

    <!-- Add / Edit Record Modal -->
    <div id="addRecordModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50">
      <div class="bg-white w-full max-w-xl rounded-xl shadow-lg border border-erp-border p-5">
        <div class="flex items-center justify-between">
          <h3 id="recordModalTitle" class="text-lg font-bold text-erp-text">Add Historical Sale</h3>
          <button type="button" id="closeAddRecordModal" class="text-erp-textMuted">✕</button>
        </div>

        <form id="addRecordForm" class="mt-4 space-y-3">
          @csrf
          <input type="hidden" id="recordId" name="record_id" value="" />
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-erp-textMuted">Order ID</label>
              <input name="order_id" required class="w-full rounded-lg border border-erp-border px-3 py-2" />
            </div>
            <div>
              <label class="text-sm text-erp-textMuted">Date</label>
              <input type="date" name="date" required class="w-full rounded-lg border border-erp-border px-3 py-2" />
            </div>
          </div>

          <div>
            <label class="text-sm text-erp-textMuted">Product Name</label>
            <select id="addProductSelect" name="product_name" required class="w-full rounded-lg border border-erp-border px-3 py-2">
              <option value="">Select a product</option>
              @foreach ($productForecasts as $p)
              <option
                value="{{ $p['name'] }}"
                data-sku="{{ $p['sku'] }}"
                data-category="{{ $p['category'] }}"
              >{{ $p['name'] }}</option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-erp-textMuted">SKU</label>
              <input id="addSku" name="sku" required readonly class="w-full rounded-lg border border-erp-border px-3 py-2 bg-erp-bg" />
            </div>
            <div>
              <label class="text-sm text-erp-textMuted">Category</label>
              <input id="addCategory" name="category" required readonly class="w-full rounded-lg border border-erp-border px-3 py-2 bg-erp-bg" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-erp-textMuted">Quantity</label>
              <input type="number" name="qty" min="0" required class="w-full rounded-lg border border-erp-border px-3 py-2" />
            </div>
            <div>
              <label class="text-sm text-erp-textMuted">Revenue</label>
              <input type="number" step="0.01" name="revenue" min="0" required class="w-full rounded-lg border border-erp-border px-3 py-2" />
            </div>
          </div>

          <div class="flex items-center justify-between pt-2">
            <button type="button" id="deleteRecordBtn" class="hidden px-3 py-2 rounded-lg border border-red-200 text-red-600 text-sm hover:bg-red-50">Delete</button>
            <div class="flex gap-3 ml-auto">
              <button type="button" id="cancelAddRecord" class="px-3 py-2 rounded-lg border border-erp-border text-sm">Cancel</button>
              <button type="submit" id="recordSubmitBtn" class="bg-erp-darkGreen text-white px-4 py-2 rounded-lg text-sm">Save</button>
            </div>
          </div>

          <div id="addRecordError" class="hidden text-sm text-red-600"></div>
        </form>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="border-b border-erp-border">
          <tr>
            <th class="py-3 px-4 text-erp-text font-semibold">Order ID</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Product</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Category</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Date</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Quantity</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Revenue</th>
            <th class="py-3 px-4 text-erp-text font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody id="historicalTable">
          @foreach ($historicalSales as $row)
          <tr
            data-id="{{ $row['id'] }}"
            data-sku="{{ $row['sku'] }}"
            data-year="{{ \Carbon\Carbon::parse($row['date'])->year }}"
            data-order="{{ $row['order'] }}"
            data-product="{{ $row['product'] }}"
            data-category="{{ $row['category'] }}"
            data-date="{{ $row['date'] }}"
            data-qty="{{ $row['qty'] }}"
            data-revenue="{{ $row['revenue'] }}"
            class="border-b border-erp-border hover:bg-erp-bg transition"
          >
            <td class="py-3 px-4">{{ $row['order'] }}</td>
            <td class="py-3 px-4">{{ $row['product'] }}</td>
            <td class="py-3 px-4"><span class="text-xs rounded-full bg-erp-bg px-2 py-1">{{ $row['category'] }}</span></td>
            <td class="py-3 px-4">{{ $row['date'] }}</td>
            <td class="py-3 px-4">{{ number_format($row['qty']) }}</td>
            <td class="py-3 px-4">${{ number_format($row['revenue']) }}</td>
            <td class="py-3 px-4"><button type="button" class="js-edit-record text-erp-darkGreen hover:underline">Edit</button></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-erp-border">
    <h3 class="text-lg font-bold text-erp-text mb-2">Model Accuracy</h3>
    <div class="space-y-3">
      @foreach ($modelAccuracy as $model)
      <div class="flex items-center justify-between p-3 bg-erp-bg rounded-lg">
        <span class="text-erp-text font-medium">{{ $model['model'] }}</span>
        <div class="flex gap-6">
          <span class="text-erp-text"><strong>Accuracy:</strong> {{ $model['accuracy'] }}</span>
          <span class="text-erp-text"><strong>MAPE:</strong> {{ $model['mape'] }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Success toast -->
<div id="toast" class="fixed top-5 right-5 z-[60] hidden max-w-sm rounded-xl border border-erp-border bg-white px-4 py-3 shadow-lg transition-all duration-300 translate-y-[-8px] opacity-0">
  <div class="flex items-start gap-3">
    <div class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-erp-darkGreen text-white text-sm">✓</div>
    <div>
      <div id="toastTitle" class="font-semibold text-erp-text">Saved</div>
      <div id="toastMessage" class="text-sm text-erp-textMuted">Record saved successfully.</div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrfToken = document.querySelector('input[name="_token"]')?.value;

  const addRecordBtn = document.getElementById('addRecord');
  const addRecordModal = document.getElementById('addRecordModal');
  const closeAddRecordModalBtn = document.getElementById('closeAddRecordModal');
  const cancelAddRecordBtn = document.getElementById('cancelAddRecord');
  const addRecordForm = document.getElementById('addRecordForm');
  const addRecordErrorEl = document.getElementById('addRecordError');
  const recordModalTitle = document.getElementById('recordModalTitle');
  const recordIdInput = document.getElementById('recordId');
  const recordSubmitBtn = document.getElementById('recordSubmitBtn');
  const deleteRecordBtn = document.getElementById('deleteRecordBtn');
  const addProductSelect = document.getElementById('addProductSelect');
  const addSku = document.getElementById('addSku');
  const addCategory = document.getElementById('addCategory');

  const historicalTableBody = document.getElementById('historicalTable');
  const toastEl = document.getElementById('toast');
  const toastTitleEl = document.getElementById('toastTitle');
  const toastMessageEl = document.getElementById('toastMessage');
  let toastTimer = null;

  const escapeHtml = (str) => String(str)
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');

  const showToast = (title, message) => {
    if (!toastEl) return;
    if (toastTitleEl) toastTitleEl.textContent = title;
    if (toastMessageEl) toastMessageEl.textContent = message;
    toastEl.classList.remove('hidden');
    requestAnimationFrame(() => {
      toastEl.classList.remove('opacity-0', 'translate-y-[-8px]');
      toastEl.classList.add('opacity-100', 'translate-y-0');
    });
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toastEl.classList.add('opacity-0', 'translate-y-[-8px]');
      toastEl.classList.remove('opacity-100', 'translate-y-0');
      setTimeout(() => toastEl.classList.add('hidden'), 300);
    }, 3200);
  };

  const openAddModal = () => {
    if (!addRecordModal) return;
    resetRecordForm();
    if (recordModalTitle) recordModalTitle.textContent = 'Add Historical Sale';
    if (recordSubmitBtn) recordSubmitBtn.textContent = 'Save';
    deleteRecordBtn?.classList.add('hidden');
    addRecordModal.classList.remove('hidden');
    addRecordModal.classList.add('flex');
    addRecordErrorEl && addRecordErrorEl.classList.add('hidden');
  };

  const resetRecordForm = () => {
    addRecordForm?.reset();
    if (recordIdInput) recordIdInput.value = '';
    if (addSku) addSku.value = '';
    if (addCategory) addCategory.value = '';
  };

  const setProductSelectByName = (productName) => {
    if (!addProductSelect) return;
    let matched = false;
    Array.from(addProductSelect.options).forEach((opt) => {
      if (opt.value === productName) {
        addProductSelect.value = productName;
        matched = true;
      }
    });
    if (matched) {
      const opt = addProductSelect.options[addProductSelect.selectedIndex];
      if (addSku) addSku.value = opt?.dataset?.sku || '';
      if (addCategory) addCategory.value = opt?.dataset?.category || '';
    }
  };

  const openEditModal = (row) => {
    if (!addRecordModal || !row) return;
    resetRecordForm();
    if (recordModalTitle) recordModalTitle.textContent = 'Edit Historical Sale';
    if (recordSubmitBtn) recordSubmitBtn.textContent = 'Update';
    deleteRecordBtn?.classList.remove('hidden');
    if (recordIdInput) recordIdInput.value = row.dataset.id || '';

    addRecordForm.order_id.value = row.dataset.order || '';
    addRecordForm.date.value = row.dataset.date || '';
    addRecordForm.qty.value = row.dataset.qty || '';
    addRecordForm.revenue.value = row.dataset.revenue || '';

    setProductSelectByName(row.dataset.product || '');
    if (addSku) addSku.value = row.dataset.sku || addSku.value;
    if (addCategory) addCategory.value = row.dataset.category || addCategory.value;

    addRecordModal.classList.remove('hidden');
    addRecordModal.classList.add('flex');
    addRecordErrorEl && addRecordErrorEl.classList.add('hidden');
  };

  const closeAddModal = () => {
    if (!addRecordModal) return;
    addRecordModal.classList.add('hidden');
    addRecordModal.classList.remove('flex');
    addRecordErrorEl && addRecordErrorEl.classList.add('hidden');
    deleteRecordBtn?.classList.add('hidden');
  };

  if (addRecordBtn) addRecordBtn.addEventListener('click', openAddModal);
  if (closeAddRecordModalBtn) closeAddRecordModalBtn.addEventListener('click', closeAddModal);
  if (cancelAddRecordBtn) cancelAddRecordBtn.addEventListener('click', closeAddModal);

  deleteRecordBtn?.addEventListener('click', async () => {
    const recordId = recordIdInput?.value || '';
    if (!recordId) return;

    const orderLabel = addRecordForm?.order_id?.value || 'this record';
    if (!confirm(`Delete ${orderLabel}? This cannot be undone.`)) return;

    deleteRecordBtn.disabled = true;

    try {
      const formData = new FormData();
      formData.append('_method', 'DELETE');

      const res = await fetch(`/forecasting/records/${recordId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      });

      const json = await res.json();
      if (!res.ok || !json.ok) throw new Error(json?.message || 'Failed to delete record');

      const row = historicalTableBody?.querySelector(`tr[data-id="${recordId}"]`);
      row?.remove();

      closeAddModal();
      resetRecordForm();
      applyTableFilters();
      await refreshForecast({ silent: true });
      showToast('Record deleted', 'Historical sale was removed.');
    } catch (err) {
      if (addRecordErrorEl) {
        addRecordErrorEl.textContent = err?.message || 'Unable to delete record.';
        addRecordErrorEl.classList.remove('hidden');
      }
    } finally {
      deleteRecordBtn.disabled = false;
    }
  });

  historicalTableBody?.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-edit-record');
    if (!btn) return;
    e.preventDefault();
    openEditModal(btn.closest('tr'));
  });

  if (addProductSelect) {
    addProductSelect.addEventListener('change', function () {
      const opt = this.options[this.selectedIndex];
      if (addSku) addSku.value = opt?.dataset?.sku || '';
      if (addCategory) addCategory.value = opt?.dataset?.category || '';
    });
  }

  // Infinite year dropdown (independent from typed input value)
  const yearInput = document.getElementById('filterYear');
  const yearList = document.getElementById('yearComboList');
  const yearToggle = document.getElementById('yearComboToggle');
  const yearCombo = document.getElementById('yearCombo');
  const YEAR_CHUNK = 40;
  let yearHigh = {{ (int) $defaultYear }} + YEAR_CHUNK;
  let yearLow = {{ (int) $defaultYear }} - YEAR_CHUNK;
  let yearListReady = false;

  const createYearOption = (y) => {
    const li = document.createElement('li');
    li.className = 'year-option px-3 py-2 cursor-pointer hover:bg-erp-bg';
    li.dataset.value = String(y);
    li.textContent = String(y);
    li.addEventListener('mousedown', (e) => {
      e.preventDefault();
      selectYear(String(y));
    });
    return li;
  };

  const fillYearRange = (from, to, mode = 'replace') => {
    if (!yearList) return;
    const frag = document.createDocumentFragment();
    for (let y = from; y >= to; y--) {
      frag.appendChild(createYearOption(y));
    }
    if (mode === 'replace') {
      yearList.innerHTML = '';
      yearList.appendChild(frag);
    } else if (mode === 'prepend') {
      yearList.insertBefore(frag, yearList.firstChild);
    } else {
      yearList.appendChild(frag);
    }
  };

  const ensureYearList = () => {
    if (yearListReady) return;
    fillYearRange(yearHigh, yearLow, 'replace');
    yearListReady = true;
  };

  const openYearList = () => {
    if (!yearList) return;
    ensureYearList();
    yearList.classList.remove('hidden');
    // Scroll to roughly the typed/current year without adding it to the list
    const target = parseInt((yearInput?.value || '').trim(), 10);
    if (!Number.isNaN(target)) {
      while (target > yearHigh) {
        const oldHeight = yearList.scrollHeight;
        const nextHigh = yearHigh + YEAR_CHUNK;
        fillYearRange(nextHigh, yearHigh + 1, 'prepend');
        yearHigh = nextHigh;
        yearList.scrollTop = yearList.scrollHeight - oldHeight;
      }
      while (target < yearLow) {
        const nextLow = yearLow - YEAR_CHUNK;
        fillYearRange(yearLow - 1, nextLow, 'append');
        yearLow = nextLow;
      }
      const el = yearList.querySelector(`.year-option[data-value="${target}"]`);
      if (el) el.scrollIntoView({ block: 'nearest' });
    }
  };

  const closeYearList = () => {
    yearList?.classList.add('hidden');
  };

  const selectYear = (value) => {
    if (!yearInput) return;
    yearInput.value = value;
    closeYearList();
    applyTableFilters();
    refreshForecast({ silent: true });
  };

  if (yearToggle) {
    yearToggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (yearList?.classList.contains('hidden')) openYearList();
      else closeYearList();
    });
  }

  if (yearInput) {
    yearInput.addEventListener('focus', openYearList);
    yearInput.addEventListener('input', () => {
      // Typing only updates the filter value — never mutates the dropdown list
      openYearList();
    });
    yearInput.addEventListener('change', () => {
      applyTableFilters();
      refreshForecast({ silent: true });
    });
    yearInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        closeYearList();
        applyTableFilters();
        refreshForecast({ silent: true });
      } else if (e.key === 'Escape') {
        closeYearList();
      }
    });
  }

  if (yearList) {
    yearList.addEventListener('scroll', () => {
      if (yearList.scrollTop < 24) {
        const oldHeight = yearList.scrollHeight;
        const nextHigh = yearHigh + YEAR_CHUNK;
        fillYearRange(nextHigh, yearHigh + 1, 'prepend');
        yearHigh = nextHigh;
        yearList.scrollTop = yearList.scrollHeight - oldHeight;
      }
      if (yearList.scrollTop + yearList.clientHeight > yearList.scrollHeight - 24) {
        const nextLow = yearLow - YEAR_CHUNK;
        fillYearRange(yearLow - 1, nextLow, 'append');
        yearLow = nextLow;
      }
    });
  }

  document.addEventListener('click', (e) => {
    if (yearCombo && !yearCombo.contains(e.target)) closeYearList();
  });

  const months = @json(array_column($demandData, 'month'));
  const demandActual = @json(array_column($demandData, 'actual'));
  const demandForecast = @json(array_column($demandData, 'forecast'));

  const ctx = document.getElementById('demandChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [
        { label: 'Actual', data: demandActual, borderColor: '#10b981', backgroundColor: '#10b98122', tension: 0.4, fill: true },
        { label: 'Forecast', data: demandForecast, borderColor: '#3b82f6', backgroundColor: '#3b82f622', tension: 0.4, fill: true }
      ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
  window.__forecastChart = chart;

  const filterProduct = document.getElementById('filterProduct');
  const filterRange = document.getElementById('filterRange');
  const predDemandEl = document.getElementById('predDemand');
  const forecastDriversEl = document.getElementById('forecastDrivers');
  const generateForecastBtn = document.getElementById('generateForecast');

  const applyTableFilters = () => {
    const sku = filterProduct?.value || 'all';
    const year = (yearInput?.value || '').trim();
    Array.from(document.querySelectorAll('#historicalTable tr')).forEach((r) => {
      const skuOk = sku === 'all' || r.dataset.sku === sku;
      const yearOk = !year || r.dataset.year === year;
      r.style.display = (skuOk && yearOk) ? '' : 'none';
    });
  };

  const updateChartFromJson = (json) => {
    if (!window.__forecastChart) return;
    window.__forecastChart.data.labels = json.months;
    window.__forecastChart.data.datasets[0].data = json.demandActual;
    window.__forecastChart.data.datasets[1].data = json.demandForecast;
    window.__forecastChart.update();

    if (predDemandEl) {
      predDemandEl.textContent = Number(json.predDemandTotal).toLocaleString() + ' units';
    }
    if (forecastDriversEl) {
      forecastDriversEl.textContent = json.message || 'Based on historical sales';
    }
  };

  const refreshForecast = async ({ silent = false } = {}) => {
    const sku = filterProduct?.value || 'all';
    const year = (yearInput?.value || '').trim();
    const range = filterRange?.value || '12m';

    if (year && !/^\d{4}$/.test(year)) {
      if (!silent) showToast('Invalid year', 'Enter a 4-digit year (e.g. 2023).');
      return;
    }

    const originalLabel = generateForecastBtn?.textContent;
    if (generateForecastBtn && !silent) {
      generateForecastBtn.disabled = true;
      generateForecastBtn.textContent = 'Generating...';
    }

    try {
      const res = await fetch('/forecasting/generate', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ sku, year, range })
      });

      const json = await res.json();
      if (!res.ok || !json.ok) throw new Error(json?.message || 'Forecast generation failed');

      updateChartFromJson(json);
      applyTableFilters();
      if (!silent) {
        showToast('Forecast generated', json.message || 'Charts updated from historical data.');
      }
    } catch (err) {
      if (!silent) alert(err?.message || 'Unable to generate forecast.');
    } finally {
      if (generateForecastBtn && !silent) {
        generateForecastBtn.disabled = false;
        generateForecastBtn.textContent = originalLabel;
      }
    }
  };

  const buildRowHtml = (sale) => {
    const saleYear = String(sale.date).slice(0, 4);
    return `
      <tr
        data-id="${escapeHtml(sale.id)}"
        data-sku="${escapeHtml(sale.sku)}"
        data-year="${escapeHtml(saleYear)}"
        data-order="${escapeHtml(sale.order)}"
        data-product="${escapeHtml(sale.product)}"
        data-category="${escapeHtml(sale.category)}"
        data-date="${escapeHtml(sale.date)}"
        data-qty="${escapeHtml(sale.qty)}"
        data-revenue="${escapeHtml(sale.revenue)}"
        class="border-b border-erp-border hover:bg-erp-bg transition"
      >
        <td class="py-3 px-4">${escapeHtml(sale.order)}</td>
        <td class="py-3 px-4">${escapeHtml(sale.product)}</td>
        <td class="py-3 px-4"><span class="text-xs rounded-full bg-erp-bg px-2 py-1">${escapeHtml(sale.category)}</span></td>
        <td class="py-3 px-4">${escapeHtml(sale.date)}</td>
        <td class="py-3 px-4">${Number(sale.qty).toLocaleString()}</td>
        <td class="py-3 px-4">$${Number(sale.revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
        <td class="py-3 px-4"><button type="button" class="js-edit-record text-erp-darkGreen hover:underline">Edit</button></td>
      </tr>`;
  };

  const updateRowFromSale = (row, sale) => {
    const saleYear = String(sale.date).slice(0, 4);
    row.dataset.id = sale.id;
    row.dataset.sku = sale.sku;
    row.dataset.year = saleYear;
    row.dataset.order = sale.order;
    row.dataset.product = sale.product;
    row.dataset.category = sale.category;
    row.dataset.date = sale.date;
    row.dataset.qty = sale.qty;
    row.dataset.revenue = sale.revenue;

    const cells = row.querySelectorAll('td');
    if (cells.length >= 6) {
      cells[0].textContent = sale.order;
      cells[1].textContent = sale.product;
      cells[2].innerHTML = `<span class="text-xs rounded-full bg-erp-bg px-2 py-1">${escapeHtml(sale.category)}</span>`;
      cells[3].textContent = sale.date;
      cells[4].textContent = Number(sale.qty).toLocaleString();
      cells[5].textContent = '$' + Number(sale.revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    }
  };

  if (addRecordForm) {
    addRecordForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      addRecordErrorEl && (addRecordErrorEl.className = 'hidden text-sm text-red-600');

      if (!addProductSelect?.value) {
        if (addRecordErrorEl) {
          addRecordErrorEl.textContent = 'Please select a product.';
          addRecordErrorEl.classList.remove('hidden');
        }
        return;
      }

      const formData = new FormData(addRecordForm);
      const recordId = recordIdInput?.value || '';
      const isEdit = Boolean(recordId);
      const url = isEdit ? `/forecasting/records/${recordId}` : '/forecasting/records';

      if (isEdit) {
        formData.append('_method', 'PUT');
      }

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: formData
        });

        const json = await res.json();
        if (!res.ok || !json.ok) {
          const firstError = json?.errors ? Object.values(json.errors).flat()[0] : null;
          throw new Error(firstError || json?.message || 'Failed to save');
        }

        const sale = json.sale;
        const saleYear = String(sale.date).slice(0, 4);

        if (isEdit) {
          const row = historicalTableBody.querySelector(`tr[data-id="${sale.id}"]`);
          if (row) updateRowFromSale(row, sale);
          showToast('Record updated', 'Historical sale was updated successfully.');
        } else {
          historicalTableBody.insertAdjacentHTML('afterbegin', buildRowHtml(sale));
          if (yearInput) yearInput.value = saleYear;
          if (filterProduct) filterProduct.value = 'all';
          showToast('Record saved', 'Added to Historical Sales Data.');
        }

        closeAddModal();
        resetRecordForm();
        applyTableFilters();
        await refreshForecast({ silent: true });
      } catch (err) {
        if (addRecordErrorEl) {
          addRecordErrorEl.textContent = err?.message || 'Unable to save record.';
          addRecordErrorEl.classList.remove('hidden');
        }
      }
    });
  }

  if (generateForecastBtn) {
    generateForecastBtn.addEventListener('click', () => refreshForecast({ silent: false }));
  }

  // Range (incl. Year to Date) updates the graph immediately
  filterRange?.addEventListener('change', () => refreshForecast({ silent: true }));

  filterProduct?.addEventListener('change', () => {
    applyTableFilters();
    refreshForecast({ silent: true });
  });

  applyTableFilters();
});
</script>

@endsection
