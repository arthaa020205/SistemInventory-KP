<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $data = Pelanggan::when($search, function ($query) use ($search) {

            $query->where('kode_pelanggan', 'like', "%{$search}%")
                ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");

        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('pelanggan.index', compact(
            'data',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $last = Pelanggan::latest()->first();

        if (!$last) {

            $kode = 'PLG0001';

        } else {

            $nomor = (int) substr($last->kode_pelanggan, 3);

            $kode = 'PLG' . str_pad(
                $nomor + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        return view('pelanggan.create', compact('kode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'kode_pelanggan' => 'required|unique:pelanggan',

            'nama_pelanggan' => 'required|max:100',

            'no_hp' => 'nullable|max:20',

            'alamat' => 'nullable',

            'status' => 'required',

        ]);

        $pelanggan = Pelanggan::create($request->all());

        ActivityLogger::log(
            'CREATE',
            'Pelanggan',
            'Menambahkan pelanggan: ' .
            $pelanggan->nama_pelanggan .
            ' (' . $pelanggan->kode_pelanggan . ')'
        );

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view(
            'pelanggan.edit',
            compact('pelanggan')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([

            'kode_pelanggan' =>
                'required|unique:pelanggan,kode_pelanggan,' .
                $pelanggan->id,

            'nama_pelanggan' => 'required|max:100',

            'no_hp' => 'nullable|max:20',

            'alamat' => 'nullable',

            'status' => 'required',

        ]);

        $namaLama = $pelanggan->nama_pelanggan;
        $kodeLama = $pelanggan->kode_pelanggan;

        $pelanggan->update($request->all());

        ActivityLogger::log(
            'UPDATE',
            'Pelanggan',
            'Mengubah pelanggan: ' .
            $kodeLama . ' - ' .
            $namaLama .
            ' menjadi ' .
            $pelanggan->kode_pelanggan . ' - ' .
            $pelanggan->nama_pelanggan
        );

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        $namaPelanggan = $pelanggan->nama_pelanggan;
        $kodePelanggan = $pelanggan->kode_pelanggan;

        $pelanggan->delete();

        ActivityLogger::log(
            'DELETE',
            'Pelanggan',
            'Menghapus pelanggan: ' .
            $kodePelanggan . ' - ' .
            $namaPelanggan
        );

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}