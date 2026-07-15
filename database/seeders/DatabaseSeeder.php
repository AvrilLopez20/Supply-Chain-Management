<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\Route;
use App\Models\Allocation;
use App\Models\HistoricalSale;
use App\Models\CostData;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Nadia Carter',
                'password' => Hash::make('password'),
                'role' => 'Operations Lead',
                'avatar' => 'NC',
            ]
        );

        // 2. Seed Warehouses
        $warehouses = [
            ['id' => 'WH-01', 'name' => 'Central Hub (NY)', 'capacity' => 85, 'status' => 'Optimal'],
            ['id' => 'WH-02', 'name' => 'West Coast Dist (CA)', 'capacity' => 92, 'status' => 'Near Capacity'],
            ['id' => 'WH-03', 'name' => 'Southern Hub (TX)', 'capacity' => 45, 'status' => 'Underutilized'],
            ['id' => 'WH-04', 'name' => 'Midwest Storage (IL)', 'capacity' => 78, 'status' => 'Optimal'],
        ];

        foreach ($warehouses as $wh) {
            Warehouse::updateOrCreate(['id' => $wh['id']], $wh);
        }

        // 3. Seed Products
        $products = [
            ['sku' => 'IWA-1001', 'name' => 'Industrial Widget A', 'category' => 'Manufacturing', 'price' => 300.00],
            ['sku' => 'CWS-2043', 'name' => 'Copper Wire Spool', 'category' => 'Raw Materials', 'price' => 15.50],
            ['sku' => 'CBV-3200', 'name' => 'Circuit Board V2', 'category' => 'Electronics', 'price' => 25.00],
            ['sku' => 'STB-0088', 'name' => 'Steel Bearings', 'category' => 'Mechanical', 'price' => 5.00],
            ['sku' => 'PKG-7712', 'name' => 'Packaging Materials', 'category' => 'Packaging', 'price' => 0.36],
            ['sku' => 'ALS-4420', 'name' => 'Aluminum Sheeting', 'category' => 'Raw Materials', 'price' => 3.50],
        ];

        $productModels = [];
        foreach ($products as $prod) {
            $productModels[$prod['sku']] = Product::updateOrCreate(['sku' => $prod['sku']], $prod);
        }

        // 4. Seed Inventory
        $inventorySeeds = [
            ['sku' => 'IWA-1001', 'warehouse_id' => 'WH-01', 'aisle' => 'A12', 'bin' => 'B04', 'qty' => 150],
            ['sku' => 'CWS-2043', 'warehouse_id' => 'WH-02', 'aisle' => 'C07', 'bin' => 'B11', 'qty' => 45],
            ['sku' => 'CBV-3200', 'warehouse_id' => 'WH-01', 'aisle' => 'A03', 'bin' => 'B02', 'qty' => 820],
            ['sku' => 'STB-0088', 'warehouse_id' => 'WH-03', 'aisle' => 'D15', 'bin' => 'B09', 'qty' => 5400],
            ['sku' => 'PKG-7712', 'warehouse_id' => 'WH-04', 'aisle' => 'E01', 'bin' => 'B01', 'qty' => 24000],
            ['sku' => 'ALS-4420', 'warehouse_id' => 'WH-03', 'aisle' => 'D02', 'bin' => 'B06', 'qty' => 1200],
        ];

        foreach ($inventorySeeds as $inv) {
            $product = $productModels[$inv['sku']];
            Inventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $inv['warehouse_id'],
                ],
                [
                    'aisle' => $inv['aisle'],
                    'bin' => $inv['bin'],
                    'qty' => $inv['qty'],
                ]
            );
        }

        // 5. Seed Suppliers
        $suppliers = [
            ['id' => 'SUP-001', 'name' => 'TechSupplies Inc.', 'rating' => 4.8, 'status' => 'Active'],
            ['id' => 'SUP-002', 'name' => 'Global Parts Ltd.', 'rating' => 4.5, 'status' => 'Active'],
            ['id' => 'SUP-003', 'name' => 'Prime Materials Co.', 'rating' => 4.7, 'status' => 'Active'],
            ['id' => 'SUP-004', 'name' => 'EuroTrade Partners', 'rating' => 4.2, 'status' => 'Active'],
            ['id' => 'SUP-005', 'name' => 'QuickShip Wholesale', 'rating' => 4.0, 'status' => 'Inactive'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(['id' => $sup['id']], $sup);
        }

        // 6. Seed Purchase Orders
        $pos = [
            ['id' => 'PO-10247', 'supplier_id' => 'SUP-001', 'total' => 24500.00, 'status' => 'Delivered', 'received_date' => '2024-07-05'],
            ['id' => 'PO-10248', 'supplier_id' => 'SUP-002', 'total' => 87300.00, 'status' => 'In Transit', 'received_date' => 'Expected: 2024-07-12'],
            ['id' => 'PO-10249', 'supplier_id' => 'SUP-003', 'total' => 15600.00, 'status' => 'Processing', 'received_date' => 'Expected: 2024-07-15'],
            ['id' => 'PO-10250', 'supplier_id' => 'SUP-004', 'total' => 42100.00, 'status' => 'Order Placed', 'received_date' => 'Expected: 2024-07-20'],
            ['id' => 'PO-10251', 'supplier_id' => 'SUP-005', 'total' => 8750.00, 'status' => 'Pending Approval', 'received_date' => '—'],
        ];

        foreach ($pos as $po) {
            PurchaseOrder::updateOrCreate(['id' => $po['id']], $po);
        }

        // 7. Seed Purchase Order Items
        $poItems = [
            ['purchase_order_id' => 'PO-10247', 'sku' => 'IWA-1001', 'qty' => 5],
            ['purchase_order_id' => 'PO-10248', 'sku' => 'CBV-3200', 'qty' => 12],
            ['purchase_order_id' => 'PO-10249', 'sku' => 'CWS-2043', 'qty' => 3],
            ['purchase_order_id' => 'PO-10250', 'sku' => 'STB-0088', 'qty' => 8],
            ['purchase_order_id' => 'PO-10251', 'sku' => 'PKG-7712', 'qty' => 2],
        ];

        foreach ($poItems as $item) {
            PurchaseOrderItem::updateOrCreate(
                [
                    'purchase_order_id' => $item['purchase_order_id'],
                    'sku' => $item['sku'],
                ],
                $item
            );
        }

        // 8. Seed Shipments
        $shipments = [
            ['id' => 'SHP-5001', 'from' => 'Central Hub (NY)', 'to' => 'West Coast Dist (CA)', 'items' => 8500, 'status' => 'In Transit', 'eta' => '2024-07-10', 'lat' => 40.7128, 'lng' => -74.0060],
            ['id' => 'SHP-5002', 'from' => 'Midwest Storage (IL)', 'to' => 'Customer A', 'items' => 1200, 'status' => 'Delivered', 'eta' => '2024-07-06', 'lat' => 41.8781, 'lng' => -87.6298],
            ['id' => 'SHP-5003', 'from' => 'Southern Hub (TX)', 'to' => 'Customer B', 'items' => 3400, 'status' => 'In Transit', 'eta' => '2024-07-09', 'lat' => 29.7604, 'lng' => -95.3698],
            ['id' => 'SHP-5004', 'from' => 'Central Hub (NY)', 'to' => 'Regional Dist (FL)', 'items' => 2100, 'status' => 'Picked', 'eta' => '2024-07-11', 'lat' => 25.7617, 'lng' => -80.1918],
            ['id' => 'SHP-5005', 'from' => 'West Coast Dist (CA)', 'to' => 'Customer C', 'items' => 950, 'status' => 'Delivered', 'eta' => '2024-07-05', 'lat' => 34.0522, 'lng' => -118.2437],
        ];

        foreach ($shipments as $ship) {
            Shipment::updateOrCreate(['id' => $ship['id']], $ship);
        }

        // 9. Seed Carriers
        $carriers = [
            ['name' => 'FastFreight Express', 'shipments' => 45, 'on_time' => '98%', 'rating' => 4.9],
            ['name' => 'National Logistics Co.', 'shipments' => 38, 'on_time' => '95%', 'rating' => 4.6],
            ['name' => 'Regional Transport Inc.', 'shipments' => 22, 'on_time' => '92%', 'rating' => 4.3],
            ['name' => 'EcoShip Solutions', 'shipments' => 15, 'on_time' => '88%', 'rating' => 4.1],
        ];

        foreach ($carriers as $car) {
            Carrier::updateOrCreate(['name' => $car['name']], $car);
        }

        // 10. Seed Routes
        $routes = [
            ['id' => 'RT-001', 'origin' => 'New York', 'destination' => 'Los Angeles', 'distance' => 2800, 'time' => '4-5 days', 'cost' => '$1,200'],
            ['id' => 'RT-002', 'origin' => 'Chicago', 'destination' => 'Miami', 'distance' => 1380, 'time' => '2-3 days', 'cost' => '$850'],
            ['id' => 'RT-003', 'origin' => 'Houston', 'destination' => 'Boston', 'distance' => 1630, 'time' => '2-3 days', 'cost' => '$950'],
            ['id' => 'RT-004', 'origin' => 'Seattle', 'destination' => 'Atlanta', 'distance' => 2350, 'time' => '3-4 days', 'cost' => '$1,100'],
        ];

        foreach ($routes as $route) {
            Route::updateOrCreate(['id' => $route['id']], $route);
        }

        // 11. Seed Allocations
        $allocations = [
            ['id' => 'ALC-1029', 'item' => 'Circuit Board V2', 'qty' => 500, 'to' => 'Production Line A', 'time' => '2 hours ago'],
            ['id' => 'ALC-1028', 'item' => 'Steel Bearings', 'qty' => 2000, 'to' => 'Assembly Plant 3', 'time' => '5 hours ago'],
            ['id' => 'ALC-1027', 'item' => 'Packaging Materials', 'qty' => 10000, 'to' => 'Shipping Dept', 'time' => 'Yesterday'],
        ];

        foreach ($allocations as $alloc) {
            Allocation::updateOrCreate(['id' => $alloc['id']], $alloc);
        }

        // 12. Seed Historical Sales (spread across months for forecasting demos)
        $historicalSales = [
            ['order_id' => 'ORD-001', 'product_name' => 'Industrial Widget A', 'sku' => 'IWA-1001', 'category' => 'Manufacturing', 'date' => '2023-01-18', 'qty' => 980, 'revenue' => 35280],
            ['order_id' => 'ORD-002', 'product_name' => 'Copper Wire Spool', 'sku' => 'CWS-2043', 'category' => 'Raw Materials', 'date' => '2023-02-11', 'qty' => 720, 'revenue' => 11160],
            ['order_id' => 'ORD-003', 'product_name' => 'Circuit Board V2', 'sku' => 'CBV-3200', 'category' => 'Electronics', 'date' => '2023-03-09', 'qty' => 2900, 'revenue' => 72500],
            ['order_id' => 'ORD-004', 'product_name' => 'Steel Bearings', 'sku' => 'STB-0088', 'category' => 'Mechanical', 'date' => '2023-04-14', 'qty' => 4100, 'revenue' => 20500],
            ['order_id' => 'ORD-005', 'product_name' => 'Industrial Widget A', 'sku' => 'IWA-1001', 'category' => 'Manufacturing', 'date' => '2023-05-20', 'qty' => 1150, 'revenue' => 41400],
            ['order_id' => 'ORD-006', 'product_name' => 'Circuit Board V2', 'sku' => 'CBV-3200', 'category' => 'Electronics', 'date' => '2023-06-08', 'qty' => 3100, 'revenue' => 77500],
            ['order_id' => 'ORD-007', 'product_name' => 'Industrial Widget A', 'sku' => 'IWA-1001', 'category' => 'Manufacturing', 'date' => '2023-07-15', 'qty' => 1250, 'revenue' => 45000],
            ['order_id' => 'ORD-008', 'product_name' => 'Copper Wire Spool', 'sku' => 'CWS-2043', 'category' => 'Raw Materials', 'date' => '2023-07-14', 'qty' => 800, 'revenue' => 12400],
            ['order_id' => 'ORD-009', 'product_name' => 'Circuit Board V2', 'sku' => 'CBV-3200', 'category' => 'Electronics', 'date' => '2023-07-12', 'qty' => 3400, 'revenue' => 85000],
            ['order_id' => 'ORD-010', 'product_name' => 'Steel Bearings', 'sku' => 'STB-0088', 'category' => 'Mechanical', 'date' => '2023-07-10', 'qty' => 5000, 'revenue' => 25000],
            ['order_id' => 'ORD-011', 'product_name' => 'Steel Bearings', 'sku' => 'STB-0088', 'category' => 'Mechanical', 'date' => '2023-08-22', 'qty' => 4600, 'revenue' => 23000],
            ['order_id' => 'ORD-012', 'product_name' => 'Industrial Widget A', 'sku' => 'IWA-1001', 'category' => 'Manufacturing', 'date' => '2023-09-05', 'qty' => 1380, 'revenue' => 49680],
            ['order_id' => 'ORD-013', 'product_name' => 'Circuit Board V2', 'sku' => 'CBV-3200', 'category' => 'Electronics', 'date' => '2023-10-19', 'qty' => 3600, 'revenue' => 90000],
            ['order_id' => 'ORD-014', 'product_name' => 'Copper Wire Spool', 'sku' => 'CWS-2043', 'category' => 'Raw Materials', 'date' => '2023-11-07', 'qty' => 940, 'revenue' => 14570],
            ['order_id' => 'ORD-015', 'product_name' => 'Steel Bearings', 'sku' => 'STB-0088', 'category' => 'Mechanical', 'date' => '2023-12-12', 'qty' => 5200, 'revenue' => 26000],
            ['order_id' => 'ORD-016', 'product_name' => 'Industrial Widget A', 'sku' => 'IWA-1001', 'category' => 'Manufacturing', 'date' => '2024-03-03', 'qty' => 1420, 'revenue' => 51120],
            ['order_id' => 'ORD-017', 'product_name' => 'Circuit Board V2', 'sku' => 'CBV-3200', 'category' => 'Electronics', 'date' => '2024-06-18', 'qty' => 3800, 'revenue' => 95000],
            ['order_id' => 'ORD-018', 'product_name' => 'Steel Bearings', 'sku' => 'STB-0088', 'category' => 'Mechanical', 'date' => '2024-09-25', 'qty' => 5400, 'revenue' => 27000],
        ];

        foreach ($historicalSales as $sale) {
            HistoricalSale::updateOrCreate(
                [
                    'order_id' => $sale['order_id'],
                    'sku' => $sale['sku'],
                ],
                $sale
            );
        }

        // 13. Seed Cost Data
        $costData = [
            ['category' => 'Raw Materials', 'spending' => 125000, 'budget' => 150000, 'variance' => '-16.7%'],
            ['category' => 'Packaging', 'spending' => 45000, 'budget' => 50000, 'variance' => '-10.0%'],
            ['category' => 'Equipment', 'spending' => 32000, 'budget' => 40000, 'variance' => '-20.0%'],
            ['category' => 'Logistics', 'spending' => 58000, 'budget' => 55000, 'variance' => '+5.5%'],
            ['category' => 'Labor', 'spending' => 102000, 'budget' => 100000, 'variance' => '+2.0%'],
        ];

        foreach ($costData as $cost) {
            CostData::updateOrCreate(['category' => $cost['category']], $cost);
        }
    }
}
