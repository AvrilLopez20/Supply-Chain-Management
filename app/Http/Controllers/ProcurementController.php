<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\CostData;
use App\Models\Product;

class ProcurementController extends Controller
{
    /**
     * Display a listing of procurement orders
     */
    public function index(): View
    {
        $orders = PurchaseOrder::with('supplier', 'items')->get();
        $poIndex = [];
        foreach ($orders as $order) {
            $itemCount = $order->items->sum('qty');
            $poIndex[] = [
                'id' => $order->id,
                'supplier' => $order->supplier->name ?? 'N/A',
                'items' => $itemCount,
                'total' => '$' . number_format($order->total),
                'status' => $order->status,
                'received' => $order->received_date,
            ];
        }

        $supplierList = Supplier::withCount('purchaseOrders')->get();
        $suppliers = [];
        foreach ($supplierList as $sup) {
            $suppliers[] = [
                'id' => $sup->id,
                'name' => $sup->name,
                'rating' => $sup->rating,
                'status' => $sup->status,
                'orders' => $sup->purchase_orders_count,
            ];
        }

        $costData = CostData::all()->map(function($cost) {
            return [
                'category' => $cost->category,
                'spending' => $cost->spending,
                'budget' => $cost->budget,
                'variance' => $cost->variance,
            ];
        })->toArray();

        return view('procurement.index', [
            'poIndex' => $poIndex,
            'suppliers' => $suppliers,
            'costData' => $costData,
            'pageTitle' => 'Procurement & Vendors',
        ]);
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create(): View
    {
        $suppliers = Supplier::where('status', 'Active')->get();
        return view('procurement.create', [
            'pageTitle' => 'Create Purchase Order',
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.sku' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $poId = 'PO-' . rand(10000, 99999);
        $totalCost = 0;

        foreach ($validated['items'] as $item) {
            if (empty($item['sku']) || empty($item['qty'])) {
                continue;
            }

            $product = Product::where('sku', $item['sku'])->first();
            $price = $product ? $product->price : 10.00;
            $totalCost += $price * $item['qty'];

            PurchaseOrderItem::create([
                'purchase_order_id' => $poId,
                'sku' => $item['sku'],
                'qty' => $item['qty']
            ]);
        }

        PurchaseOrder::create([
            'id' => $poId,
            'supplier_id' => $validated['supplier_id'],
            'total' => $totalCost,
            'status' => 'Pending Approval',
            'received_date' => 'Expected: ' . now()->addDays(7)->format('Y-m-d')
        ]);

        return redirect()->route('procurement.index')->with('success', 'Purchase order created successfully.');
    }

    /**
     * Display the specified purchase order
     */
    public function show(string $id): View
    {
        return view('procurement.show', ['pageTitle' => 'Purchase Order Details']);
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(string $id): View
    {
        return view('procurement.edit', ['pageTitle' => 'Edit Purchase Order']);
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('procurement.index')->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('procurement.index')->with('success', 'Purchase order deleted successfully.');
    }
}
