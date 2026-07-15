<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\Route;
use App\Models\Warehouse;

class LogisticsController extends Controller
{
    /**
     * Display a listing of logistics/shipments
     */
    public function index(): View
    {
        $shipments = Shipment::all()->map(function($ship) {
            return [
                'id' => $ship->id,
                'from' => $ship->from,
                'to' => $ship->to,
                'items' => $ship->items,
                'status' => $ship->status,
                'eta' => $ship->eta,
                'lat' => floatval($ship->lat),
                'lng' => floatval($ship->lng),
            ];
        })->toArray();

        $carriers = Carrier::all()->map(function($car) {
            return [
                'name' => $car->name,
                'shipments' => $car->shipments,
                'onTime' => $car->on_time,
                'rating' => floatval($car->rating),
            ];
        })->toArray();

        $routes = Route::all()->map(function($route) {
            return [
                'id' => $route->id,
                'origin' => $route->origin,
                'destination' => $route->destination,
                'distance' => $route->distance,
                'time' => $route->time,
                'cost' => $route->cost,
            ];
        })->toArray();

        return view('logistics.index', [
            'shipments' => $shipments,
            'carriers' => $carriers,
            'routes' => $routes,
            'pageTitle' => 'Logistics & Shipping',
        ]);
    }

    /**
     * Show the form for creating a new shipment
     */
    public function create(): View
    {
        $warehouses = Warehouse::all();
        $carriers = Carrier::all();
        return view('logistics.create', [
            'pageTitle' => 'Create Shipment',
            'warehouses' => $warehouses,
            'carriers' => $carriers
        ]);
    }

    /**
     * Store a newly created shipment
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'carrier' => 'required|string',
            'items' => 'required|integer|min:1',
        ]);

        $shipmentId = 'SHP-' . rand(5000, 9999);

        // Generate coordinate coordinates roughly within the US map bounds
        $lat = 35.0 + (rand(-500, 500) / 100);
        $lng = -97.0 + (rand(-500, 500) / 100);

        Shipment::create([
            'id' => $shipmentId,
            'from' => $validated['from'],
            'to' => $validated['to'],
            'items' => $validated['items'],
            'status' => 'Picked',
            'eta' => now()->addDays(3)->format('Y-m-d'),
            'lat' => $lat,
            'lng' => $lng
        ]);

        // Increment carrier shipments count
        $carrier = Carrier::where('name', $validated['carrier'])->first();
        if ($carrier) {
            $carrier->increment('shipments');
        }

        return redirect()->route('logistics.index')->with('success', 'Shipment created successfully.');
    }

    /**
     * Display the specified shipment
     */
    public function show(string $id): View
    {
        return view('logistics.show', ['pageTitle' => 'Shipment Details']);
    }

    /**
     * Show the form for editing the specified shipment
     */
    public function edit(string $id): View
    {
        return view('logistics.edit', ['pageTitle' => 'Edit Shipment']);
    }

    /**
     * Update the specified shipment
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('logistics.index')->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified shipment
     */
    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('logistics.index')->with('success', 'Shipment deleted successfully.');
    }
}
