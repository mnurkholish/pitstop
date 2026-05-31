<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function services(): View
    {
        return $this->placeholder(
            'Layanan',
            'Katalog layanan lengkap PitStop akan tersedia pada tahap berikutnya.',
        );
    }

    public function about(): View
    {
        return $this->placeholder(
            'Tentang PitStop',
            'PitStop membantu pelanggan merencanakan service kendaraan dengan lebih praktis dan transparan.',
        );
    }

    public function contact(): View
    {
        return $this->placeholder(
            'Kontak',
            'Informasi kontak bengkel akan dilengkapi pada tahap halaman publik berikutnya.',
        );
    }

    private function placeholder(string $title, string $description): View
    {
        return view('public.placeholder', compact('title', 'description'));
    }
}
