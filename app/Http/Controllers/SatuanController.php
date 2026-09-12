<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $data = Satuan::when($search, function ($query) use ($search) {
            $query->where('kode_satuan', 'like', "%{$search}%")
                  ->orWhere('nama_satuan', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('satuan.index', compact('data', 'search'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_satuan' => 'required|string|max:20|unique:satuan,kode_satuan',
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
        ]);

        $satuan = Satuan::create([
            'kode_satuan' => strtoupper($request->kode_satuan),
            'nama_satuan' => $request->nama_satuan,
        ]);

        ActivityLogger::log(
            'CREATE',
            'Satuan',
            'Menambahkan satuan: ' . $satuan->nama_satuan
        );

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil ditambahkan.');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'kode_satuan' => 'required|string|max:20|unique:satuan,kode_satuan,' . $satuan->id,
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan,' . $satuan->id,
        ]);

        $namaSatuanLama = $satuan->nama_satuan;

        $satuan->update([
            'kode_satuan' => strtoupper($request->kode_satuan),
            'nama_satuan' => $request->nama_satuan,
        ]);

        ActivityLogger::log(
            'UPDATE',
            'Satuan',
            'Mengubah satuan: ' . $namaSatuanLama .
            ' menjadi ' . $satuan->nama_satuan
        );

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil diubah.');
    }

    public function destroy(Satuan $satuan)
    {
        $namaSatuan = $satuan->nama_satuan;

        $satuan->delete();

        ActivityLogger::log(
            'DELETE',
            'Satuan',
            'Menghapus satuan: ' . $namaSatuan
        );

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil dihapus.');
    }
}