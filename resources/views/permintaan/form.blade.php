<div class="mb-3">

    <label class="form-label">

        Kode Permintaan

    </label>

    <input
        type="text"
        class="form-control"
        value="{{ old('kode_permintaan', $kode ?? $permintaan->kode_permintaan ?? '') }}"
        readonly>

</div>

<div class="mb-3">

    <label class="form-label">

        Barang

        <span class="text-danger">*</span>

    </label>

    <select
        name="barang_id"
        class="form-select @error('barang_id') is-invalid @enderror">

        <option value="">-- Pilih Barang --</option>

        @foreach($barang as $item)

            <option
                value="{{ $item->id }}"
                @selected(old('barang_id', $permintaan->barang_id ?? '') == $item->id)>

                {{ $item->kode_barang }}
                -
                {{ $item->nama_barang }}
                (Stok : {{ $item->stok }} {{ $item->satuan->nama_satuan }})

            </option>

        @endforeach

    </select>

    @error('barang_id')

        <div class="invalid-feedback">

            {{ $message }}

        </div>

    @enderror

</div>

<div class="row">

    <div class="col-md-6">

        <label class="form-label">

            Jumlah Permintaan

        </label>

        <input
            type="number"
            min="1"
            name="jumlah"
            class="form-control @error('jumlah') is-invalid @enderror"
            value="{{ old('jumlah', $permintaan->jumlah ?? '') }}">

        @error('jumlah')

            <div class="invalid-feedback">

                {{ $message }}

            </div>

        @enderror

    </div>

    <div class="col-md-6">

        <label class="form-label">

            Tanggal Permintaan

        </label>

        <input
            type="date"
            name="tanggal_permintaan"
            class="form-control @error('tanggal_permintaan') is-invalid @enderror"
            value="{{ old('tanggal_permintaan', isset($permintaan) ? $permintaan->tanggal_permintaan->format('Y-m-d') : date('Y-m-d')) }}">

        @error('tanggal_permintaan')

            <div class="invalid-feedback">

                {{ $message }}

            </div>

        @enderror

    </div>

</div>

<div class="mt-3">

    <label class="form-label">

        Alasan Pengadaan

    </label>

    <textarea
        name="alasan"
        rows="4"
        class="form-control @error('alasan') is-invalid @enderror"
        placeholder="Contoh : Stok barang menipis dan diperkirakan habis minggu ini">{{ old('alasan', $permintaan->alasan ?? '') }}</textarea>

    @error('alasan')

        <div class="invalid-feedback">

            {{ $message }}

        </div>

    @enderror

</div>

<hr>

<div class="d-flex justify-content-end">

    <a
        href="{{ route('permintaan.index') }}"
        class="btn btn-secondary me-2">

        <i class="fas fa-arrow-left"></i>

        Kembali

    </a>

    <button
        type="submit"
        class="btn btn-primary"
        @disabled(isset($permintaan) && $permintaan->status != 'Menunggu')>

        <i class="fas fa-save"></i>

        Simpan

    </button>

</div>