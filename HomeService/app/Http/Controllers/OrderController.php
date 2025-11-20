<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Aritmatika: Hitung statistik pesanan
    private function calculateOrderStats()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::pending()->count();
        $completedOrders = Order::completed()->count();
        $monthlyOrders = Order::thisMonth()->count();
        
        // Hitung revenue
        $totalRevenue = Order::completed()->sum('total_price');
        $monthlyRevenue = Order::completed()->thisMonth()->sum('total_price');
        
        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'monthly_orders' => $monthlyOrders,
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue
        ];
    }

    public function index()
    {
        $orders = Order::with(['service', 'review'])->latest()->get();
        $stats = $this->calculateOrderStats();
        
        // Perulangan: Group orders by status untuk chart
        $statusCounts = [];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        foreach ($statuses as $status) {
            $statusCounts[$status] = Order::where('status', $status)->count();
        }
        
        return view('orders.index', compact('orders', 'stats', 'statusCounts'));
    }

    public function create()
    {
        $services = Service::available()->get();
        
        // Logika: Generate waktu available (08:00 - 17:00)
        $timeSlots = [];
        for ($hour = 8; $hour <= 17; $hour++) {
            $timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            if ($hour < 17) {
                $timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':30';
            }
        }
        
        return view('orders.create', compact('services', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:15',
            'address' => 'required|string|min:10',
            'order_date' => 'required|date|after_or_equal:today',
            'order_time' => 'required',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($request->service_id);
            
            $order = new Order($request->all());
            $order->total_price = $order->calculateTotalPrice();
            $order->status = 'pending';
            $order->save();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 
                    "Pesanan berhasil dibuat! Total: Rp " . 
                    number_format($order->total_price, 0, ',', '.') .
                    " (Termasuk perhitungan diskon dan pajak)");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['service', 'review']);
        
        // Aritmatika: Hitung detail breakdown harga
        $priceBreakdown = [
            'base_price' => $order->service->price,
            'day_discount' => $order->calculateDayDiscount(),
            'time_discount' => $order->calculateTimeDiscount(),
            'tax' => $order->service->price * 0.1,
            'final_price' => $order->total_price
        ];
        
        return view('orders.show', compact('order', 'priceBreakdown'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        $statusText = [
            'pending' => 'menunggu',
            'confirmed' => 'dikonfirmasi', 
            'in_progress' => 'dalam pengerjaan',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan'
        ];

        return redirect()->back()->with('success', 
            "Status pesanan berhasil diubah menjadi {$statusText[$request->status]}!");
    }

    public function destroy(Order $order)
    {
        // Logika: Cek apakah order bisa dihapus
        if ($order->status == 'completed' && $order->review) {
            return redirect()->route('orders.index')
                ->with('error', 'Tidak dapat menghapus pesanan yang sudah completed dan memiliki review!');
        }

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan berhasil dihapus!');
    }
}