<div class="card mt-4">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exchange-alt me-1"></i>
            Konversi Satuan
        </h3>
    </div>

    <div class="card-body">

        <div class="alert alert-light border">
            <strong>Satuan Dasar:</strong>
            {{ strtoupper($barang->satuan->kode_satuan ?? $barang->satuan->nama_satuan) }}

            <div class="small text-muted mt-1">
                Semua stok barang akan dihitung berdasarkan satuan dasar.
            </div>
        </div>


        {{-- FORM TAMBAH KONVERSI --}}
        <form
            action="{{ route('barang.konversi.store', $barang) }}"
            method="POST">

            @csrf

            <div class="row align-items-end">

                <div class="col-md-5">

                    <label class="form-label">
                        Satuan Kemasan
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="satuan_id"
                        class="form-select @error('satuan_id') is-invalid @enderror">

                        <option value="">
                            -- Pilih Satuan --
                        </option>

                       @foreach($satuan as $item)

                            @if($item->id !== $barang->satuan_id)

                                <option
                                    value="{{ $item->id }}"
                                    @selected(old('satuan_id') == $item->id)>

                                    {{ strtoupper($item->kode_satuan ?: $item->nama_satuan) }}

                                </option>

                            @endif

                        @endforeach

                    </select>

                    @error('satuan_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-5">

                    <label class="form-label">
                        Isi dalam Satuan Dasar
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="nilai_konversi"
                            class="form-control @error('nilai_konversi') is-invalid @enderror"
                            min="1"
                            value="{{ old('nilai_konversi') }}"
                            placeholder="Contoh: 50">

                        <span class="input-group-text">
                            {{ strtoupper($barang->satuan->kode_satuan ?? $barang->satuan->nama_satuan) }}
                        </span>

                    </div>

                    @error('nilai_konversi')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-2">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        <i class="fas fa-plus"></i>
                        Tambah

                    </button>

                </div>

            </div>

        </form>


        <hr>


        {{-- DAFTAR KONVERSI --}}
        <h6 class="mb-3">
            Daftar Konversi
        </h6>

        @if($barang->konversiSatuan->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                Satuan
                            </th>

                            <th>
                                Konversi
                            </th>

                            <th width="100">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($barang->konversiSatuan as $index => $konversi)

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    <strong>
                                        {{ strtoupper($konversi->satuan->kode_satuan ?? $konversi->satuan->nama_satuan) }}
                                    </strong>
                                </td>

                                <td>
                                    1
                                    {{ strtoupper($konversi->satuan->kode_satuan ?? $konversi->satuan->nama_satuan) }}

                                    =

                                    <strong>
                                        {{ number_format($konversi->nilai_konversi) }}
                                    </strong>

                                    {{ strtoupper($barang->satuan->kode_satuan ?? $barang->satuan->nama_satuan) }}
                                </td>

                                <td>

                                    <form
                                        action="{{ route('barang.konversi.destroy', [$barang, $konversi]) }}"
                                        method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Hapus konversi satuan ini?')">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center text-muted py-4">

                <i class="fas fa-box-open fa-2x mb-2"></i>

                <div>
                    Belum ada konversi satuan.
                </div>

            </div>

        @endif

    </div>

</div>