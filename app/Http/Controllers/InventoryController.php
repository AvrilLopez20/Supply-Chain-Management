<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Allocation;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items
     */
    public function index(): View
    {
        $inventories = Inventory::with('product', 'warehouse')->get();
        $itemIndex = [];
        foreach ($inventories as $inv) {
            $itemIndex[] = [
                'name' => $inv->product->name ?? 'N/A',
                'sku' => $inv->product->sku ?? 'N/A',
                'warehouse' => $inv->warehouse->name ?? 'N/A',
                'aisle' => $inv->aisle ?? '—',
                'bin' => $inv->bin ?? '—',
                'qty' => $inv->qty,
            ];
        }

        $warehouses = Warehouse::all();
        $warehouseInventory = [];
        foreach ($warehouses as $wh) {
            $totalQty = Inventory::where('warehouse_id', $wh->id)->sum('qty');
            $warehouseInventory[] = [
                'id' => $wh->id,
                'name' => $wh->name,
                'capacity' => $wh->capacity,
                'items' => $totalQty,
                'status' => $wh->status,
            ];
        }

        $lowStocks = Inventory::where('qty', '<', 200)->with('product', 'warehouse')->get();
        $lowStockItems = [];
        foreach ($lowStocks as $ls) {
            $lowStockItems[] = [
                'item' => $ls->product->name ?? 'N/A',
                'current' => $ls->qty,
                'required' => 500,
                'warehouse' => $ls->warehouse->name ?? 'N/A',
            ];
        }

        $allocations = Allocation::orderBy('created_at', 'desc')->take(10)->get()->map(function($alc) {
            return [
                'id' => $alc->id,
                'item' => $alc->item,
                'qty' => $alc->qty,
                'to' => $alc->to,
                'time' => $alc->time,
            ];
        })->toArray();

        return view('inventory.index', [
            'itemIndex' => $itemIndex,
            'warehouseInventory' => $warehouseInventory,
            'lowStockItems' => $lowStockItems,
            'allocations' => $allocations,
            'pageTitle' => 'Inventory & Warehouses',
        ]);
    }

    /**
     * Show the form for creating a new inventory item
     */
    public function create(): View
    {
        $warehouses = Warehouse::all();
        return view('inventory.create', [
            'pageTitle' => 'Create Inventory Item',
            'warehouses' => $warehouses
        ]);
    }

    /**
     * Store a newly created inventory item in storage
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate and store inventory item
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string',
            'warehouse' => 'required|string',
            'qty' => 'required|integer|min:0',
        ]);

        // Find or create product
        $product = Product::firstOrCreate(
            ['sku' => $validated['sku']],
            [
                'name' => $validated['name'],
                'category' => 'General',
                'price' => 10.00
            ]
        );

        // Find warehouse
        $warehouse = Warehouse::where('name', $validated['warehouse'])
            ->orWhere('id', $validated['warehouse'])
            ->first();

        if (!$warehouse) {
            return redirect()->back()->withErrors(['warehouse' => 'Warehouse not found.']);
        }

        // Find or create inventory entry
        $inventory = Inventory::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if ($inventory) {
            $inventory->qty += $validated['qty'];
            $inventory->save();
        } else {
            Inventory::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'aisle' => 'A' . rand(1, 20),
                'bin' => 'B' . rand(1, 20),
                'qty' => $validated['qty']
            ]);
        }

        // Add to allocations list
        Allocation::create([
            'id' => 'ALC-' . rand(1000, 9999),
            'item' => $product->name,
            'qty' => $validated['qty'],
            'to' => $warehouse->name,
            'time' => 'Just now'
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created/allocated successfully.');
    }

    /**
     * Display the specified inventory item
     */
    public function show(string $id): View
    {
        return view('inventory.show', ['pageTitle' => 'Inventory Item Details']);
    }

    /**
     * Show the form for editing the specified inventory item
     */
    public function edit(string $id): View
    {
        return view('inventory.edit', ['pageTitle' => 'Edit Inventory Item']);
    }

    /**
     * Update the specified inventory item in storage
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item from storage
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * JSON API to fetch all inventory
     */
    public function apiIndex()
    {
        $inventories = Inventory::with('product', 'warehouse')->get()->map(function($inv) {
            return [
                'sku' => $inv->product->sku ?? '—',
                'name' => $inv->product->name ?? '—',
                'warehouse' => $inv->warehouse->name ?? '—',
                'warehouse_id' => $inv->warehouse_id,
                'aisle' => $inv->aisle,
                'bin' => $inv->bin,
                'qty' => $inv->qty
            ];
        });

        return response()->json($inventories);
    }

    /**
     * JSON API to perform stock transfer
     */
    public function apiTransfer(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'sku' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::where('sku', $validated['sku'])->first();
        if (!$product) {
            return response()->json(['message' => 'Product SKU not found'], 404);
        }

        if ($validated['from'] === $validated['to']) {
            return response()->json(['message' => 'Source and destination warehouses must be different'], 422);
        }

        // Get source inventory
        $sourceInv = Inventory::where('product_id', $product->id)
            ->where('warehouse_id', $validated['from'])
            ->first();

        if (!$sourceInv || $sourceInv->qty < $validated['qty']) {
            return response()->json(['message' => 'Insufficient stock in source warehouse'], 422);
        }

        // Get or create destination inventory
        $destInv = Inventory::where('product_id', $product->id)
            ->where('warehouse_id', $validated['to'])
            ->first();

        // Perform transfer
        $sourceInv->qty -= $validated['qty'];
        $sourceInv->save();

        if ($destInv) {
            $destInv->qty += $validated['qty'];
            $destInv->save();
        } else {
            Inventory::create([
                'product_id' => $product->id,
                'warehouse_id' => $validated['to'],
                'aisle' => 'A' . rand(1, 20),
                'bin' => 'B' . rand(1, 20),
                'qty' => $validated['qty']
            ]);
        }

        // Log Allocation
        $fromWhName = Warehouse::where('id', $validated['from'])->value('name') ?? $validated['from'];
        $toWhName = Warehouse::where('id', $validated['to'])->value('name') ?? $validated['to'];
        Allocation::create([
            'id' => 'ALC-' . rand(1000, 9999),
            'item' => $product->name,
            'qty' => $validated['qty'],
            'to' => $toWhName,
            'time' => 'Just now'
        ]);

        return response()->json([
            'message' => "Successfully transferred {$validated['qty']} units of {$product->name} from {$fromWhName} to {$toWhName}."
        ]);
    }
}
