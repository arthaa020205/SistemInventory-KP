<div class="mb-3">
    <label class="form-label">Nama</label>

    <input type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $user->name ?? '') }}">

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Email</label>

    <input type="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', $user->email ?? '') }}">

    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">
        Password
    </label>

    <input type="password"
        name="password"
        class="form-control @error('password') is-invalid @enderror">

    @if(isset($user))
        <small class="text-muted">
            Kosongkan jika password tidak diubah.
        </small>
    @endif

    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">
        Konfirmasi Password
    </label>

    <input type="password"
        name="password_confirmation"
        class="form-control">
</div>

<div class="mb-3">

    <label class="form-label">
        Role
    </label>

    <select name="role" class="form-select">

        @foreach($roles as $role)

            <option
                value="{{ $role->name }}"
                @selected(old('role', isset($user) ? $user->roles->first()?->name : '') == $role->name)>

                {{ $role->name }}

            </option>

        @endforeach

    </select>

</div>

@if(isset($user))

<div class="mb-3">

    <label class="form-label">
        Status
    </label>

    <select name="status" class="form-select">

        <option value="Aktif"
            @selected($user->status == 'Aktif')>

            Aktif

        </option>

        <option value="Nonaktif"
            @selected($user->status == 'Nonaktif')>

            Nonaktif

        </option>

    </select>

</div>

@endif

<button class="btn btn-primary">
    <i class="fas fa-save"></i>
    Simpan
</button>

<a href="{{ route('users.index') }}"
    class="btn btn-secondary">

    Kembali

</a>