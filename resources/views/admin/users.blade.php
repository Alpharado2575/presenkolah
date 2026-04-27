@extends('layout.app')
@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')
{{-- Filter + Tambah ──────────────────────────────────────────────────────── --}}
<div class="card stat-card mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-end">
        <form action="{{ route('admin.users') }}" method="GET" class="d-flex gap-2 flex-grow-1">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / username..."
                   value="{{ request('search') }}">
            <select name="role" class="form-select" style="width:auto">
                <option value="">Semua Role</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="guru"  {{ request('role') === 'guru'  ? 'selected' : '' }}>Guru</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </button>
    </div>
</div>

{{-- Tabel ────────────────────────────────────────────────────────────────── --}}
<div class="card stat-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $u)
                        <tr>
                            <td class="text-muted small">{{ $users->firstItem() + $i }}</td>
                            <td class="fw-semibold small">{{ $u->nama }}</td>
                            <td class="small text-muted">{{ $u->username }}</td>
                            <td>
                                <span class="badge {{ match($u->role) {
                                    'admin'  => 'bg-danger',
                                    'guru'   => 'bg-success',
                                    default  => 'bg-primary',
                                } }}">{{ ucfirst($u->role) }}</span>
                            </td>
                            <td class="small">{{ $u->kelas ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="editUser({{ $u }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus user {{ $u->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Tidak ada user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-3 py-3 border-top">{{ $users->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal Tambah ─────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin._form_user')
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit ───────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body" id="editBody">
                    @include('admin._form_user', ['edit' => true])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editUser(u) {
    const f = document.getElementById('formEdit');
    f.action = `/admin/users/${u.id}`;

    f.querySelector('[name="nama"]').value      = u.nama;
    f.querySelector('[name="username"]').value  = u.username;
    f.querySelector('[name="role"]').value      = u.role;
    f.querySelector('[name="kelas"]').value     = u.kelas ?? '';
    f.querySelector('[name="is_active"]').checked = u.is_active;

    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
@endpush
