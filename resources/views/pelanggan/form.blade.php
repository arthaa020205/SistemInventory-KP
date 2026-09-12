@csrf

<div class="row">

    <div class="col-md-6">

        <div class="form-group">

            <label>Kode Pelanggan</label>

            <input
                type="text"
                name="kode_pelanggan"
                class="form-control @error('kode_pelanggan') is-invalid @enderror"
                value="{{ old('kode_pelanggan', $pelanggan->kode_pelanggan ?? $kode ?? '') }}"
                readonly>

            @error('kode_pelanggan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>

    <div class="col-md-6">

        <div class="form-group">

            <label>Status</label>

            <select
                name="status"
                class="form-control @error('status') is-invalid @enderror">

                <option value="Aktif"
                    {{ old('status', $pelanggan->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="Tidak Aktif"
                    {{ old('status', $pelanggan->status ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>
                    Tidak Aktif
                </option>

            </select>

            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>

</div>

<div class="form-group">

    <label>Nama Pelanggan</label>

    <input
        type="text"
        name="nama_pelanggan"
        class="form-control @error('nama_pelanggan') is-invalid @enderror"
        value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan ?? '') }}">

    @error('nama_pelanggan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

</div>

<div class="form-group">

    <label>No HP</label>

    <input
        type="text"
        name="no_hp"
        class="form-control @error('no_hp') is-invalid @enderror"
        value="{{ old('no_hp', $pelanggan->no_hp ?? '') }}">

    @error('no_hp')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

</div>

<div class="form-group">

    <label>Alamat</label>

    <textarea
        name="alamat"
        rows="4"
        class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>

    @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

</div>

<div class="mt-4">

    <button class="btn btn-primary">

        <i class="fas fa-save"></i>

        Simpan

    </button>

    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Kembali

    </a>

</div>