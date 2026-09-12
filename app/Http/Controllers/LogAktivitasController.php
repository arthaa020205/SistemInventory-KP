<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $data = LogAktivitas::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where('aktivitas', 'like', "%{$search}%")
                    ->orWhere('modul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('log-aktivitas.index', compact(
            'data',
            'search'
        ));
    }
}