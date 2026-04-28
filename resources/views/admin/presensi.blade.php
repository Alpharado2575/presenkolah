@extends('layout.app')
@section('title', 'Data Presensi')
@section('page-title', 'Data Presensi')

@section('content')
{{-- Filter --}}
<div class="card stat-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.presensi') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ request('tanggal', today()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Role</label>
                <select name="role" class="form-select">
                    <option value="">Semua</option>
                    <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru"  {{ request('role') === 'guru'  ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin"  {{ request('status') === 'izin'  ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.presensi.export', request()->all()) }}"
                   class="btn btn-outline-success w-100">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card stat-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $i => $p)
                        <tr>
                            <td class="text-muted small">{{ $presensis->firstItem() + $i }}</td>
                            <td class="small fw-semibold">{{ $p->user->nama }}</td>
                            <td>
                                <span class="badge {{ $p->user->isGuru() ? 'bg-success' : 'bg-primary' }}">
                                    {{ ucfirst($p->user->role) }}
                                </span>
                            </td>
                            <td class="small">{{ $p->tanggal->format('d/m/Y') }}</td>

                            {{-- MASUK --}}
                            <td class="small">
                                @if($p->jam_masuk)
                                    <div>{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</div>
                                    @if($p->foto_masuk)
                                        <img src="{{ asset('storage/presensi/'.$p->foto_masuk) }}"
                                             width="60"
                                             class="mt-1 rounded border"
                                             style="cursor:pointer"
                                             onclick="showImage(this.src)">
                                    @endif
                                @else
                                    —
                                @endif
                            </td>

                            {{-- PULANG --}}
                            <td class="small">
                                @if($p->jam_pulang)
                                    <div>{{ \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') }}</div>
                                    @if($p->foto_pulang)
                                        <img src="{{ asset('storage/presensi/'.$p->foto_pulang) }}"
                                             width="60"
                                             class="mt-1 rounded border"
                                             style="cursor:pointer"
                                             onclick="showImage(this.src)">
                                    @endif
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                            </td>

                            <td class="small text-muted" style="max-width:150px">
                                {{ Str::limit($p->keterangan, 50) ?? '—' }}
                            </td>

                            <td>
                                @if($p->buktiUrl())
                                    <a href="{{ $p->buktiUrl() }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="editPresensi({{ $p }}, {{ $p->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                Tidak ada data presensi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presensis->hasPages())
            <div class="px-3 py-3 border-top">
                {{ $presensis->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Preview Gambar --}}
<div id="imageModal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:9999;"
     onclick="this.style.display='none'">
    <img id="modalImg" style="max-width:90%; max-height:90%; border-radius:10px;">
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditPresensi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Presensi</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPresensi" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="editStatus" class="form-select">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                    <input type="time" name="jam_masuk" id="editJamMasuk" class="form-control mb-2">
                    <input type="time" name="jam_pulang" id="editJamPulang" class="form-control mb-2">
                    <textarea name="keterangan" id="editKeterangan" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showImage(src) {
    document.getElementById('modalImg').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function editPresensi(p, id) {
    document.getElementById('formEditPresensi').action = `/admin/presensi/${id}`;
    document.getElementById('editStatus').value = p.status;
    document.getElementById('editJamMasuk').value = p.jam_masuk ? p.jam_masuk.slice(0,5) : '';
    document.getElementById('editJamPulang').value = p.jam_pulang ? p.jam_pulang.slice(0,5) : '';
    document.getElementById('editKeterangan').value = p.keterangan ?? '';
    new bootstrap.Modal(document.getElementById('modalEditPresensi')).show();
}
</script>
@endpush