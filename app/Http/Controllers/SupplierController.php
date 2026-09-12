<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $data = Supplier::when($search, function ($query) use ($search) {
            $query->where('nama_supplier', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('telepon', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('supplier.index', compact('data', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|max:100',
            'pic' => 'required|max:100',
            'telepon' => 'required|max:20',
            'email' => 'nullable|email',
            'alamat' => 'nullable',
            'status' => 'required',
        ]);

        $supplier = Supplier::create($request->all());

        ActivityLogger::log(
            'CREATE',
            'Supplier',
            'Menambahkan supplier: ' . $supplier->nama_supplier
        );

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
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
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|max:100',
            'pic' => 'required|max:100',
            'telepon' => 'required|max:20',
            'email' => 'nullable|email',
            'alamat' => 'nullable',
            'status' => 'required',
        ]);

        $namaSupplierLama = $supplier->nama_supplier;

        $supplier->update($request->all());

        ActivityLogger::log(
            'UPDATE',
            'Supplier',
            'Mengubah supplier: ' . $namaSupplierLama .
            ' menjadi ' . $supplier->nama_supplier
        );

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $namaSupplier = $supplier->nama_supplier;

        $supplier->delete();

        ActivityLogger::log(
            'DELETE',
            'Supplier',
            'Menghapus supplier: ' . $namaSupplier
        );

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}