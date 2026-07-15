<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Response;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Shipment;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index(): View
    {
        $reportTypes = [
            ['id' => 'inventory', 'title' => 'Inventory Report', 'description' => 'Stock levels, warehouse capacity, low stock alerts'],
            ['id' => 'procurement', 'title' => 'Procurement Report', 'description' => 'Purchase orders, vendor performance, cost analysis'],
            ['id' => 'logistics', 'title' => 'Logistics Report', 'description' => 'Shipments, delivery performance, route optimization'],
            ['id' => 'forecasting', 'title' => 'Forecasting Report', 'description' => 'Demand predictions, trend analysis, recommendations'],
        ];

        return view('reports.index', [
            'reportTypes' => $reportTypes,
            'pageTitle' => 'Reports & Analytics',
        ]);
    }

    /**
     * Display inventory report
     */
    public function inventory(): View
    {
        $totalQty = Inventory::sum('qty');
        $totalValue = Inventory::with('product')->get()->sum(function($inv) {
            return $inv->qty * ($inv->product->price ?? 0);
        });

        $report = [
            'title' => 'Inventory Report',
            'generated' => now()->format('Y-m-d H:i:s'),
            'summary' => [
                'total_items' => $totalQty,
                'total_value' => '$' . number_format($totalValue),
                'warehouses' => Warehouse::count(),
                'low_stock_items' => Inventory::where('qty', '<', 200)->count(),
            ]
        ];

        return view('reports.inventory', [
            'report' => $report,
            'pageTitle' => 'Inventory Report',
        ]);
    }

    /**
     * Display procurement report
     */
    public function procurement(): View
    {
        $totalSpend = PurchaseOrder::sum('total');

        $report = [
            'title' => 'Procurement Report',
            'generated' => now()->format('Y-m-d H:i:s'),
            'summary' => [
                'total_orders' => PurchaseOrder::count(),
                'total_spend' => '$' . number_format($totalSpend),
                'active_suppliers' => Supplier::where('status', 'Active')->count(),
                'pending_orders' => PurchaseOrder::whereIn('status', ['Pending Approval', 'Processing', 'Order Placed'])->count(),
            ]
        ];

        return view('reports.procurement', [
            'report' => $report,
            'pageTitle' => 'Procurement Report',
        ]);
    }

    /**
     * Display logistics report
     */
    public function logistics(): View
    {
        $report = [
            'title' => 'Logistics Report',
            'generated' => now()->format('Y-m-d H:i:s'),
            'summary' => [
                'total_shipments' => Shipment::count(),
                'delivered' => Shipment::where('status', 'Delivered')->count(),
                'in_transit' => Shipment::where('status', 'In Transit')->count(),
                'avg_delivery_time' => '3.2 days',
            ]
        ];

        return view('reports.logistics', [
            'report' => $report,
            'pageTitle' => 'Logistics Report',
        ]);
    }

    /**
     * Export report in specified format
     */
    public function export(string $type): Response
    {
        // Export functionality placeholder
        return response()->json(['message' => 'Export functionality will be implemented'], 200);
    }
}
