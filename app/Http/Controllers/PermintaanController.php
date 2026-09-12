<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanPengadaan;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $data = PermintaanPengadaan::with([
            'barang.satuan',
            'user',
            'approvedBy'
        ])

        ->when($search, function ($q) use ($search) {

            $q->where(function ($query) use ($search) {

                $query->where('kode_permintaan', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($x) use ($search) {

                        $x->where('nama_barang', 'like', "%{$search}%");

                    });

            });

        })

        ->when($status, function ($q) use ($status) {

            $q->where('status', $status);

        })

        ->latest()

        ->paginate(10)

        ->withQueryString();

        return view('permintaan.index', compact(
            'data',
            'search',
            'status'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::where('status','Aktif')
                ->orderBy('nama_barang')
                ->get();

        $last = PermintaanPengadaan::latest()->first();

        if(!$last){

            $kode = 'PR0001';

        }else{

            $nomor = (int) substr($last->kode_permintaan,2);

            $kode = 'PR'.str_pad($nomor+1,4,'0',STR_PAD_LEFT);

        }

        return view('permintaan.create',compact(
            'barang',
            'kode'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'barang_id'=>'required|exists:barang,id',

            'jumlah'=>'required|integer|min:1',

            'tanggal_permintaan'=>'required|date',

            'alasan'=>'required'

        ]);

        PermintaanPengadaan::create([

            'kode_permintaan'=>$this->generateKode(),

            'barang_id'=>$request->barang_id,

            'jumlah'=>$request->jumlah,

            'tanggal_permintaan'=>$request->tanggal_permintaan,

            'alasan'=>$request->alasan,

            'status'=>'Menunggu',

            'user_id'=>auth()->id()

        ]);

        return redirect()

            ->route('permintaan.index')

            ->with('success','Permintaan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PermintaanPengadaan $permintaan)
    {
        $permintaan->load([
            'barang.satuan',
            'user'
        ]);

        return view('permintaan.show', compact('permintaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PermintaanPengadaan $permintaan)
    {
        if ($permintaan->status != 'Menunggu') {

            return redirect()
                ->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah diproses tidak dapat diedit.');

        }
        
        if ($permintaan->status != 'Menunggu') {

            return redirect()
                ->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah diproses tidak dapat diubah.');

        }

        $barang = Barang::where('status', 'Aktif')
            ->orderBy('nama_barang')
            ->get();

        return view('permintaan.edit', compact(
            'permintaan',
            'barang'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PermintaanPengadaan $permintaan)
    {
        if ($permintaan->status != 'Menunggu') {

            return redirect()
                ->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah diproses tidak dapat diubah.');

        }

        $request->validate([

            'barang_id' => 'required|exists:barang,id',

            'jumlah' => 'required|integer|min:1',

            'tanggal_permintaan' => 'required|date',

            'alasan' => 'required'

        ]);

        $permintaan->update([

            'barang_id' => $request->barang_id,

            'jumlah' => $request->jumlah,

            'tanggal_permintaan' => $request->tanggal_permintaan,

            'alasan' => $request->alasan

        ]);

        return redirect()
            ->route('permintaan.index')
            ->with('success', 'Permintaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermintaanPengadaan $permintaan)
    {
        if ($permintaan->status != 'Menunggu') {

            return back()->with(
                'error',
                'Permintaan yang sudah diproses tidak dapat dihapus.'
            );

        }

        $permintaan->delete();

        return redirect()
            ->route('permintaan.index')
            ->with('success', 'Permintaan berhasil dihapus.');
    }

    private function generateKode()
    {
        $last = PermintaanPengadaan::latest()->first();

        if(!$last){

            return 'PR0001';

        }

        $nomor = (int) substr($last->kode_permintaan,2);

        return 'PR'.str_pad($nomor+1,4,'0',STR_PAD_LEFT);
    }

    public function approval(PermintaanPengadaan $permintaan)
    {
        if (!auth()->user()->hasRole('Owner')) {

            abort(403);

        }

        $permintaan->load([
            'barang.satuan',
            'user',
            'approver'
        ]);

        return view('permintaan.approval', compact('permintaan'));
    }

    public function setujui(PermintaanPengadaan $permintaan)
    {
        if (!auth()->user()->hasRole('Owner')) {

            abort(403);

        }

        if ($permintaan->status != 'Menunggu') {

            return back()->with('error', 'Permintaan sudah diproses.');

        }

        $permintaan->update([

            'status' => 'Disetujui',

            'approved_by' => auth()->id(),

            'approved_at' => now(),

            'catatan_owner' => null,

        ]);

        return redirect()
            ->route('permintaan.index')
            ->with('success', 'Permintaan berhasil disetujui.');
    }

    public function tolak(Request $request, PermintaanPengadaan $permintaan)
    {
        if (!auth()->user()->hasRole('Owner')) {

            abort(403);

        }

        if ($permintaan->status != 'Menunggu') {

            return back()->with('error', 'Permintaan sudah diproses.');

        }

        $request->validate([

            'catatan_owner' => 'required'

        ]);

        $permintaan->update([

            'status' => 'Ditolak',

            'catatan_owner' => $request->catatan_owner,

            'approved_by' => auth()->id(),

            'approved_at' => now(),

        ]);

        return redirect()
            ->route('permintaan.index')
            ->with('success', 'Permintaan berhasil ditolak.');
    }
}
