@extends('layout.app')
@section('title', 'Pengumuman')
@section('page-title', 'Kelola Pengumuman')

@section('content')
<div class="row g-4">
    {{-- Form Tambah ────────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-megaphone text-warning me-1"></i> Buat Pengumuman
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul</label>
                        <input type="text" name="judul" class="form-control"
                               value="{{ old('judul') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Isi Pengumuman</label>
                        <textarea name="isi" class="form-control" rows="5" required>{{ old('isi') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ditujukan Kepada</label>
                        <select name="target" class="form-select">
                            <option value="semua">Semua (Siswa & Guru)</option>
                            <option value="siswa">Siswa Saja</option>
                            <option value="guru">Guru Saja</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-1"></i> Kirim Pengumuman
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Pengumuman ──────────────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-list-ul text-primary me-1"></i> Daftar Pengumuman
            </div>
            <div class="card-body p-0">
                @forelse($pengumumans as $p)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold">{{ $p->judul }}</span>
                                <span class="badge ms-2
                                    {{ $p->target === 'semua' ? 'bg-primary' : ($p->target === 'siswa' ? 'bg-success' : 'bg-warning text-dark') }}
                                    " style="font-size:.65rem">
                                    {{ ucfirst($p->target) }}
                                </span>
                            </div>
                            <form action="{{ route('admin.pengumuman.destroy', $p) }}" method="POST"
                                  onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        <p class="text-muted small mt-1 mb-1">{{ $p->isi }}</p>
                        <div class="text-muted" style="font-size:.7rem">
                            <i class="bi bi-person"></i> {{ $p->pembuat->nama ?? 'Admin' }}
                            &bull; {{ $p->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-megaphone fs-2 d-block mb-2"></i>
                        Belum ada pengumuman.
                    </div>
                @endforelse
            </div>
            @if($pengumumans->hasPages())
                <div class="px-3 py-3 border-top">{{ $pengumumans->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
