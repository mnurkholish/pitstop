<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $visitCount = $request->session()->increment('home_visit_count');

        return view('public.home', [
            'services' => Service::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->limit(4)
                ->get(),
            'activeServiceCount' => Service::query()->where('is_active', true)->count(),
            'visitCount' => $visitCount,
        ]);
    }
}
