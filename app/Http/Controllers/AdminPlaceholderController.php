<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminPlaceholderController extends Controller
{
    public function bookings(): View
    {
        return $this->placeholder('Daftar Booking', 'Pengelolaan booking aktif akan dibangun pada tahap berikutnya.');
    }

    public function history(): View
    {
        return $this->placeholder('Riwayat Booking', 'Arsip booking selesai dan dibatalkan akan dibangun pada tahap berikutnya.');
    }

    private function placeholder(string $title, string $description): View
    {
        return view('admin.placeholder', compact('title', 'description'));
    }
}
