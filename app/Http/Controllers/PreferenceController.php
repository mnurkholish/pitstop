<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function edit(Request $request): View
    {
        return view('preferences.edit', [
            'theme' => $this->theme($request),
            'fontSize' => $this->fontSize($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'font_size' => ['required', Rule::in(['normal', 'large'])],
        ]);

        return redirect()
            ->route('preferences.edit')
            ->with('success', 'Preferensi tampilan berhasil disimpan.')
            ->cookie('pitstop_theme', $validated['theme'], 60 * 24 * 365)
            ->cookie('pitstop_font_size', $validated['font_size'], 60 * 24 * 365);
    }

    private function theme(Request $request): string
    {
        return in_array($request->cookie('pitstop_theme'), ['light', 'dark'], true)
            ? $request->cookie('pitstop_theme')
            : 'light';
    }

    private function fontSize(Request $request): string
    {
        return in_array($request->cookie('pitstop_font_size'), ['normal', 'large'], true)
            ? $request->cookie('pitstop_font_size')
            : 'normal';
    }
}
