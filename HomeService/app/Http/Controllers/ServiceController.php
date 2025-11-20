<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Aritmatika: Hitung statistik layanan
    private function calculateServiceStats()
    {
        $totalServices = Service::count();
        $availableServices = Service::available()->count();
        $unavailableServices = $totalServices - $availableServices;
        
        return [
            'total' => $totalServices,
            'available' => $availableServices,
            'unavailable' => $unavailableServices,
            'availability_rate' => $totalServices > 0 ? ($availableServices / $totalServices) * 100 : 0
        ];
    }

    public function index()
    {
        $services = Service::withCount('orders')->latest()->get();
        $stats = $this->calculateServiceStats();
        
        // Perulangan: Kategorikan layanan
        $categories = [];
        foreach ($services as $service) {
            if (!isset($categories[$service->category])) {
                $categories[$service->category] = [];
            }
            $categories[$service->category][] = $service;
        }
        
        return view('services.index', compact('services', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = ['Elektronik', 'Plumbing', 'Listrik', 'AC', 'General'];
        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:1000',
            'duration' => 'required|integer|min:1|max:24',
            'category' => 'required|string|max:100',
            'is_available' => 'boolean'
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'category' => $request->category,
            'is_available' => $request->has('is_available')
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function show(Service $service)
    {
        // Load relasi dengan count orders
        $service->loadCount('orders');
        
        // Aritmatika: Hitung rating rata-rata
        $averageRating = 0;
        $totalReviews = 0;
        
        foreach ($service->orders as $order) {
            if ($order->review) {
                $averageRating += $order->review->rating;
                $totalReviews++;
            }
        }
        
        $averageRating = $totalReviews > 0 ? $averageRating / $totalReviews : 0;
        
        return view('services.show', compact('service', 'averageRating', 'totalReviews'));
    }

    public function edit(Service $service)
    {
        $categories = ['Elektronik', 'Plumbing', 'Listrik', 'AC', 'General'];
        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:1000',
            'duration' => 'required|integer|min:1|max:24',
            'category' => 'required|string|max:100',
            'is_available' => 'boolean'
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'category' => $request->category,
            'is_available' => $request->has('is_available')
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy(Service $service)
    {
        // Logika: Cek apakah layanan bisa dihapus
        if ($service->orders()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'Tidak dapat menghapus layanan yang sudah memiliki pesanan!');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil dihapus!');
    }

    public function toggleAvailability(Service $service)
    {
        $service->update(['is_available' => !$service->is_available]);
        
        $status = $service->is_available ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Layanan berhasil $status!");
    }
}