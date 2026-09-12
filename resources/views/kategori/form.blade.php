<div class="mb-3">
    <label class="form-label">Nama Kategori</label>

    <input
        type="text"
        name="nama_kategori"
        class="form-control @error('nama_kategori') is-invalid @enderror"
        value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}"
        placeholder="Masukkan nama kategori">

    @error('nama_kategori')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>

    <textarea
        name="deskripsi"
        rows="4"
        class="form-control @error('deskripsi') is-invalid @enderror"
        placeholder="Masukkan deskripsi (opsional)">{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>

    @error('deskripsi')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mt-4">

    <button class="btn btn-primary">
        <i class="fas fa-save"></i>
        Simpan
    </button>

    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
        Kembali
    </a>

</div>