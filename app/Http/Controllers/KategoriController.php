<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Helpers\ActivityLogger;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        $search = $request->search;

        $data = Kategori::when($search, function ($query) use ($search) {
            $query->where('nama_kategori', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('kategori.index', compact('data', 'search'));
    }

    public function create()
    {
        //
        return view('kategori.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori',
        'deskripsi' => 'nullable|string',
    ]);

    Kategori::create($request->all());

    ActivityLogger::log(
        'CREATE',
        'Kategori',
        'Menambahkan kategori: ' . $request->nama_kategori
    );

    return redirect()
        ->route('kategori.index')
        ->with('success', 'Kategori berhasil ditambahkan.');
    }


    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {

        ActivityLogger::log(
            'UPDATE',
            'Kategori',
            'Mengubah kategori: ' . $kategori->nama_kategori
        );

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        $namaKategori = $kategori->nama_kategori;

        $kategori->delete();

        ActivityLogger::log(
            'DELETE',
            'Kategori',
            'Menghapus kategori: ' . $namaKategori
        );
    }
}
