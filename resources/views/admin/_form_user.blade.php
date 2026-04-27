{{-- Shared form fields for create/edit user modal --}}
<div class="mb-3">
    <label class="form-label small fw-semibold">Nama Lengkap</label>
    <input type="text" name="nama" class="form-control"
           value="{{ old('nama') }}" required>
</div>
<div class="mb-3">
    <label class="form-label small fw-semibold">Username</label>
    <input type="text" name="username" class="form-control"
           value="{{ old('username') }}" required>
</div>
<div class="mb-3">
    <label class="form-label small fw-semibold">
        Password {{ isset($edit) ? '(kosongkan jika tidak ingin diubah)' : '' }}
    </label>
    <input type="password" name="password" class="form-control"
           {{ isset($edit) ? '' : 'required' }}>
</div>
<div class="row g-2">
    <div class="col-6">
        <label class="form-label small fw-semibold">Role</label>
        <select name="role" class="form-select" required>
            <option value="siswa">Siswa</option>
            <option value="guru">Guru</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <div class="col-6">
        <label class="form-label small fw-semibold">Kelas (jika Siswa)</label>
        <input type="text" name="kelas" class="form-control"
               placeholder="e.g. XI-SIJA-1">
    </div>
</div>
<div class="form-check mt-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
    <label class="form-check-label small" for="is_active">Akun Aktif</label>
</div>
