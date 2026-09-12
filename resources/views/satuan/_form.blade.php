<div class="mb-3">
    <x-input-label for="kode_satuan" :value="'Kode Satuan'" />

    <x-text-input
        id="kode_satuan"
        name="kode_satuan"
        type="text"
        class="form-control"
        :value="old('kode_satuan', $satuan->kode_satuan ?? '')"
        placeholder="Contoh: KG, PCS, BOX"
        maxlength="20"
        required
    />

    <x-input-error :messages="$errors->get('kode_satuan')" />
</div>


<div class="mb-3">
    <x-input-label for="nama_satuan" :value="'Nama Satuan'" />

    <x-text-input
        id="nama_satuan"
        name="nama_satuan"
        type="text"
        class="form-control"
        :value="old('nama_satuan', $satuan->nama_satuan ?? '')"
        placeholder="Contoh: Kilogram, Pieces, Box"
        maxlength="50"
        required
    />

    <x-input-error :messages="$errors->get('nama_satuan')" />
</div>


<div class="mt-4">

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i>
        Simpan
    </button>

    <x-buttons.cancel :href="route('satuan.index')" />

</div>