<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Ganti Oli Mesin', 'description' => 'Penggantian oli mesin untuk membantu menjaga performa kendaraan tetap stabil.', 'price' => 85000, 'duration_minutes' => 30, 'image' => 'images/services/ganti-oli-mesin.png', 'is_active' => true],
            ['name' => 'Tune Up', 'description' => 'Perawatan mesin agar kendaraan tetap nyaman digunakan dan responsif.', 'price' => 200000, 'duration_minutes' => 90, 'image' => 'images/services/tune-up.png', 'is_active' => true],
            ['name' => 'Service Rem', 'description' => 'Pemeriksaan sistem rem untuk membantu menjaga keamanan saat berkendara.', 'price' => 120000, 'duration_minutes' => 60, 'image' => 'images/services/service-rem.png', 'is_active' => true],
            ['name' => 'Cek Mesin', 'description' => 'Pemeriksaan kondisi mesin untuk membantu menemukan masalah lebih awal.', 'price' => 50000, 'duration_minutes' => 45, 'image' => 'images/services/cek-mesin.png', 'is_active' => true],
            ['name' => 'Ganti Aki', 'description' => 'Pemeriksaan dan penggantian aki agar kelistrikan kendaraan tetap bekerja baik.', 'price' => 150000, 'duration_minutes' => 45, 'image' => 'images/services/ganti-aki.png', 'is_active' => false],
            ['name' => 'Spooring Balancing', 'description' => 'Penyetelan roda untuk membantu menjaga kenyamanan dan kestabilan kendaraan.', 'price' => 180000, 'duration_minutes' => 60, 'image' => 'images/services/spooring-balancing.png', 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service,
            );
        }
    }
}
