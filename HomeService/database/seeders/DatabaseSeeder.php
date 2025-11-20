<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Services
        $services = [
            [
                'name' => 'Service AC Split',
                'description' => 'Layanan perbaikan, perawatan, dan pemasangan AC split untuk rumah dan kantor',
                'price' => 250000,
                'duration' => 3,
                'category' => 'AC',
                'is_available' => true
            ],
            [
                'name' => 'Perbaikan Instalasi Listrik',
                'description' => 'Perbaikan dan pemasangan instalasi listrik rumah, termasuk panel dan stop kontak',
                'price' => 350000,
                'duration' => 4,
                'category' => 'Listrik',
                'is_available' => true
            ],
            [
                'name' => 'Service Pipa Air',
                'description' => 'Perbaikan kebocoran pipa air, pemasangan water heater, dan perawatan saluran air',
                'price' => 200000,
                'duration' => 2,
                'category' => 'Plumbing',
                'is_available' => true
            ],
            [
                'name' => 'Service Kulkas',
                'description' => 'Perbaikan kulkas tidak dingin, kebocoran freon, dan masalah kompresor',
                'price' => 300000,
                'duration' => 3,
                'category' => 'Elektronik',
                'is_available' => true
            ],
            [
                'name' => 'Pengecatan Rumah',
                'description' => 'Jasa pengecatan dinding rumah interior dan exterior dengan cat berkualitas',
                'price' => 500000,
                'duration' => 6,
                'category' => 'General',
                'is_available' => false
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create Sample Orders
        $orders = [
            [
                'service_id' => 1,
                'customer_name' => 'Claudya Ayu Lestari',
                'customer_email' => 'Claudya.ayu@email.com',
                'customer_phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jambi selatan',
                'order_date' => Carbon::today()->addDays(2),
                'order_time' => '10:00',
                'total_price' => 247500, // Setelah kalkulasi
                'status' => 'confirmed',
                'notes' => 'AC tidak dingin dan berisik'
            ],
            [
                'service_id' => 2,
                'customer_name' => 'Zhabita Adia Nopitri',
                'customer_email' => 'zhaa.bita@email.com',
                'customer_phone' => '081298765432',
                'address' => 'Jl. Sudirman No. 456, Jambi Timur',
                'order_date' => Carbon::today()->addDays(1),
                'order_time' => '14:00',
                'total_price' => 346500,
                'status' => 'completed',
                'notes' => 'Listrik sering mati sendiri'
            ],
            [
                'service_id' => 3,
                'customer_name' => 'Novi Yanti',
                'customer_email' => 'Novi.Yanti@email.com',
                'customer_phone' => '081345678901',
                'address' => 'Jl. Thamrin No. 789, Jakarta Pusat',
                'order_date' => Carbon::today(),
                'order_time' => '09:30',
                'total_price' => 198000,
                'status' => 'in_progress',
                'notes' => 'Pipa bawah wastafel bocor'
            ]
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }

        // Create Sample Reviews
        $reviews = [
            [
                'order_id' => 2,
                'rating' => 5,
                'comment' => 'Pelayanan sangat memuaskan! Teknisinya profesional dan cepat tanggap. Listrik di rumah sudah normal kembali. Terima kasih!',
                'is_approved' => true
            ],
            [
                'order_id' => 1,
                'rating' => 4,
                'comment' => 'Service AC cukup bagus, teknisi datang tepat waktu. Hanya saja harganya agak mahal tapi sepadan dengan kualitas.',
                'is_approved' => true
            ]
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}