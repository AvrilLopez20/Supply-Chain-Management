<!-- Header Component -->
<div class="bg-white border-b border-erp-border">
    <div class="p-6 flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-erp-textMuted"></i>
            <input id="erpSearchInput" type="text" placeholder="Search reports, inventory, orders..." class="w-full rounded-lg border border-erp-border bg-erp-bg py-2 pl-9 pr-3 text-sm text-erp-text outline-none">
            <div id="erpSearchResults" class="absolute left-0 right-0 top-full z-20 mt-2 hidden rounded-xl border border-erp-border bg-white shadow-lg">
                <div class="p-2 text-sm text-erp-textMuted">No results yet</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <button class="p-2 hover:bg-erp-bg rounded-lg transition">
                <i data-lucide="bell" class="w-6 h-6 text-erp-textMuted"></i>
            </button>
            <button class="p-2 hover:bg-erp-bg rounded-lg transition">
                <i data-lucide="settings" class="w-6 h-6 text-erp-textMuted"></i>
            </button>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('erpSearchInput');
        const resultsBox = document.getElementById('erpSearchResults');
        const items = [
            { title: 'Dashboard', url: '/', keywords: 'dashboard overview metrics summary' },
            { title: 'Inventory', url: '/inventory', keywords: 'inventory stock warehouse items' },
            { title: 'Procurement', url: '/procurement', keywords: 'procurement orders suppliers purchase' },
            { title: 'Logistics', url: '/logistics', keywords: 'logistics shipments carriers tracking' },
            { title: 'Forecasting', url: '/forecasting', keywords: 'forecasting analytics demand prediction' },
            { title: 'Reports', url: '/reports', keywords: 'reports analytics export performance' },

        ];

        const renderResults = (query) => {
            const normalized = query.toLowerCase().trim();
            if (!normalized) {
                resultsBox.innerHTML = '<div class="p-2 text-sm text-erp-textMuted">Type to search ERP sections</div>';
                resultsBox.classList.add('hidden');
                return;
            }

            const filtered = items.filter(item =>
                item.title.toLowerCase().includes(normalized) ||
                item.keywords.toLowerCase().includes(normalized)
            );

            if (!filtered.length) {
                resultsBox.innerHTML = '<div class="p-2 text-sm text-erp-textMuted">No matching sections</div>';
                resultsBox.classList.remove('hidden');
                return;
            }

            resultsBox.innerHTML = filtered.map(item => `
                <a href="${item.url}" class="block border-b border-erp-border px-3 py-2 text-sm hover:bg-erp-bg last:border-b-0">
                    <div class="font-medium text-erp-text">${item.title}</div>
                    <div class="text-erp-textMuted">${item.keywords}</div>
                </a>
            `).join('');
            resultsBox.classList.remove('hidden');
        };

        input.addEventListener('input', function () {
            renderResults(this.value);
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#erpSearchInput') && !event.target.closest('#erpSearchResults')) {
                resultsBox.classList.add('hidden');
            }
        });
    });
</script>
