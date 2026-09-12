<div class="mb-3">
    <label class="form-label">Nama Supplier</label>

    <input type="text"
        name="nama_supplier"
        class="form-control @error('nama_supplier') is-invalid @enderror"
        value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}"
        placeholder="Masukkan nama supplier">

    @error('nama_supplier')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">PIC</label>

    <input type="text"
        name="pic"
        class="form-control @error('pic') is-invalid @enderror"
        value="{{ old('pic', $supplier->pic ?? '') }}"
        placeholder="Masukkan nama PIC">

    @error('pic')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">No. Telepon</label>

    <input type="text"
        name="telepon"
        class="form-control @error('telepon') is-invalid @enderror"
        value="{{ old('telepon', $supplier->telepon ?? '') }}"
        placeholder="08xxxxxxxxxx">

    @error('telepon')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Email</label>

    <input type="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', $supplier->email ?? '') }}"
        placeholder="supplier@email.com">

    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Alamat</label>

    <textarea
        name="alamat"
        rows="3"
        class="form-control @error('alamat') is-invalid @enderror"
        placeholder="Masukkan alamat supplier">{{ old('alamat', $supplier->alamat ?? '') }}</textarea>

    @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Status</label>

    <select name="status" class="form-select">
        <option value="Aktif"
            {{ old('status', $supplier->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
            Aktif
        </option>

        <option value="Nonaktif"
            {{ old('status', $supplier->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
            Nonaktif
        </option>
    </select>
</div>

<div class="mt-4">

    <button class="btn btn-primary">
        <i class="fas fa-save"></i>
        Simpan
    </button>

    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
        Kembali
    </a>

</div>