<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KonversiSatuan;
use App\Models\Satuan;
use Illuminate\Http\Request;

class KonversiSatuanController extends Controller
{
    /**
     * Simpan konversi satuan.
     */
    public function store(Request $request, Barang $barang)
    {
        $request->validate([
            'satuan_id' => [
                'required',
                'exists:satuan,id',
                'different:' . $barang->satuan_id,
            ],
            'nilai_konversi' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'satuan_id.different' => 'Satuan konversi tidak boleh sama dengan satuan dasar.',
            'nilai_konversi.min' => 'Nilai konversi minimal 1.',
        ]);

        // Cegah satuan yang sama digunakan dua kali
        $sudahAda = KonversiSatuan::where('barang_id', $barang->id)
            ->where('satuan_id', $request->satuan_id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'satuan_id' => 'Satuan tersebut sudah memiliki konversi untuk barang ini.'
                ])
                ->withInput();
        }

        KonversiSatuan::create([
            'barang_id' => $barang->id,
            'satuan_id' => $request->satuan_id,
            'nilai_konversi' => $request->nilai_konversi,
        ]);

        return back()->with(
            'success',
            'Konversi satuan berhasil ditambahkan.'
        );
    }


    /**
     * Hapus konversi satuan.
     */
    public function destroy(
        Barang $barang,
        KonversiSatuan $konversiSatuan
    ) {
        // Pastikan konversi memang milik barang tersebut
        if ($konversiSatuan->barang_id !== $barang->id) {
            abort(404);
        }

        $konversiSatuan->delete();

        return back()->with(
            'success',
            'Konversi satuan berhasil dihapus.'
        );
    }
}