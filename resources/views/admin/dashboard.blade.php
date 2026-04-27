@extends('layout.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Admin')

@section('content')
{{-- Stat Cards ────────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total Siswa',      $stats['total_siswa'],       '#1a237e', '#e8eaf6', 'bi-people-fill'],
        ['Total Guru',       $stats['total_guru'],        '#1b5e20', '#e8f5e9', 'bi-person-badge-fill'],
        ['Hadir Hari Ini',   $stats['hadir_hari_ini'],    '#157347', '#d1f0e0', 'bi-check-circle-fill'],
        ['Izin/Sakit',       $stats['izin_hari_ini'],     '#0a58ca', '#d0e9ff', 'bi-file-earmark-medical'],
        ['Alpha Hari Ini',   $stats['alpha_hari_ini'],    '#842029', '#f8d7da', 'bi-x-circle-fill'],
    ] as [$label, $val, $color, $bg, $icon])
        <div class="col-6 col-md-4 col-xl">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box"
                         style="background:{{ $bg }};color:{{ $color }};width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem">{{ $label }}</div>
                        <div class="fw-bold fs-4" style="color:{{ $color }}">{{ $val }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Presensi Hari Ini & Pengumuman ────────────────────────────────────────── --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-calendar-check text-primary me-1"></i>
                Presensi Hari Ini — {{ now()->translatedFormat('d F Y') }}
                <a href="{{ route('admin.presensi', ['tanggal' => today()]) }}"
                   class="btn btn-sm btn-outline-primary float-end">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPresensi as $p)
                                <tr>
                                    <td class="small fw-semibold">{{ $p->user->nama }}</td>
                                    <td>
                                        <span class="badge {{ $p->user->isGuru() ? 'bg-success' : 'bg-primary' }}">
                                            {{ ucfirst($p->user->role) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $p->jam_masuk  ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i')  : '—' }}</td>
                                    <td class="small">{{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $p->status }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada presensi hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-megaphone text-warning me-1"></i> Pengumuman Terbaru
                <a href="{{ route('admin.pengumuman') }}" class="btn btn-sm btn-outline-primary float-end">
                    Kelola
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($pengumumans as $p)
                    <div class="px-3 py-3 border-bottom">
                        <div class="fw-semibold small">{{ $p->judul }}</div>
                        <div class="text-muted small">{{ Str::limit($p->isi, 80) }}</div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="badge bg-secondary" style="font-size:.65rem">{{ ucfirst($p->target) }}</span>
                            <span class="text-muted" style="font-size:.7rem">{{ $p->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">Belum ada pengumuman.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
