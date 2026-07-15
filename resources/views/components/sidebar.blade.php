<!-- Sidebar Component -->
<div class="w-64 bg-white border-r border-erp-border">
    <div class="p-6 border-b border-erp-border">
        <h1 class="text-xl font-bold text-erp-darkGreen">SupplyChain ERP</h1>
    </div>
    
    <nav class="p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="home" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Dashboard</span>
        </a>
        
        <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="package" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Inventory</span>
        </a>
        
        <a href="{{ route('procurement.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="shopping-cart" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Procurement</span>
        </a>
        
        <a href="{{ route('logistics.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="truck" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Logistics</span>
        </a>
        
        <a href="{{ route('forecasting.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="trending-up" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Forecasting</span>
        </a>
        
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-erp-bg transition">
            <i data-lucide="bar-chart-2" class="w-5 h-5 text-erp-darkGreen"></i>
            <span class="text-erp-text font-medium">Reports</span>
        </a>
    </nav>
</div>
