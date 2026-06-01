<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Ganti Oli Mesin', 'description' => 'Penggantian oli mesin berkualitas untuk menjaga performa dan usia mesin kendaraan.', 'price' => 85000, 'duration_minutes' => 30, 'image' => 'images/services/ganti-oli-mesin.png', 'is_active' => true],
            ['name' => 'Tune Up', 'description' => 'Perawatan menyeluruh mesin agar kendaraan tetap responsif dan hemat bahan bakar.', 'price' => 200000, 'duration_minutes' => 90, 'image' => 'images/services/tune-up.png', 'is_active' => true],
            ['name' => 'Service Rem', 'description' => 'Pemeriksaan dan penggantian kampas rem untuk keamanan berkendara yang optimal.', 'price' => 120000, 'duration_minutes' => 60, 'image' => 'images/services/service-rem.png', 'is_active' => true],
            ['name' => 'Cek Mesin', 'description' => 'Diagnosa komprehensif kondisi mesin menggunakan alat scan terkini.', 'price' => 50000, 'duration_minutes' => 45, 'image' => 'images/services/cek-mesin.png', 'is_active' => true],
            ['name' => 'Ganti Aki', 'description' => 'Pemeriksaan dan penggantian aki kendaraan.', 'price' => 150000, 'duration_minutes' => 45, 'image' => 'images/services/ganti-aki.png', 'is_active' => false],
            ['name' => 'Spooring Balancing', 'description' => 'Penyelarasan roda untuk menjaga stabilitas kendaraan.', 'price' => 180000, 'duration_minutes' => 60, 'image' => 'images/services/spooring-balancing.png', 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service,
            );
        }
    }
}
