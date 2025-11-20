<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::latest()->take(3)->get();
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        
        // Logika: Hitung persentase order completed
        $completionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        
        return view('home', compact('services', 'totalOrders', 'completedOrders', 'completionRate'));
    }
}