@extends('layout.app')
@section('title', 'Beranda')
@section('page-title', 'Beranda')

@section('content')
{{-- Greeting ─────────────────────────────────────────────────────────────── --}}
<div class="card stat-card mb-4 bg-primary text-white">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="icon-box bg-white bg-opacity-25" style="width:56px;height:56px;border-radius:12px;font-size:1.6rem;">
            @if($user->isSiswa()) 🎒
            @elseif($user->isGuru()) 📚
            @else 🛡️
            @endif
        </div>
        <div>
            <h5 class="mb-0 fw-bold">Selamat datang, {{ $user->nama }}!</h5>
            <div class="opacity-75 small">
                {{ ucfirst($user->role) }}
                @if($user->kelas) — Kelas {{ $user->kelas }} @endif
                &bull; {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
        <div class="ms-auto text-end d-none d-md-block">
            <div class="fs-3 fw-bold">{{ now()->format('H:i') }}</div>
            <div class="opacity-75 small">WIB</div>
        </div>
    </div>
</div>

{{-- Status Presensi Hari Ini ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#d1f0e0;color:#157347;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Jam Masuk Hari Ini</div>
                        <div class="fw-bold fs-5">
                            {{ $presensiHariIni?->jam_masuk
                                ? \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i')
                                : '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#fde8d0;color:#c0550a;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Jam Pulang Hari Ini</div>
                        <div class="fw-bold fs-5">
                            {{ $presensiHariIni?->jam_pulang
                                ? \Carbon\Carbon::parse($presensiHariIni->jam_pulang)->format('H:i')
                                : '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#e7d8f8;color:#6f42c1;width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Status Hari Ini</div>
                        @if($presensiHariIni)
                            <span class="badge badge-{{ $presensiHariIni->status }} fw-semibold" style="font-size:.9rem">
                                {{ ucfirst($presensiHariIni->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary fw-semibold" style="font-size:.9rem">Belum Presensi</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Statistik Bulan Ini & Pengumuman ────────────────────────────────────── --}}
<div class="row g-4">
    {{-- Statistik ──────────────────────────────────────────────────────────── --}}
    <div class="col-md-5">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-bar-chart text-primary me-1"></i> Statistik Bulan Ini
            </div>
            <div class="card-body">
                @php
                    $totalHari = array_sum($stats);
                @endphp
                @foreach([
                    ['hadir', 'Hadir',  '#157347', '#d1f0e0'],
                    ['izin',  'Izin',   '#0a58ca', '#d0e9ff'],
                    ['sakit', 'Sakit',  '#856404', '#fff3cd'],
                    ['alpha', 'Alpha',  '#842029', '#f8d7da'],
                ] as [$key, $label, $color, $bg])
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded" style="width:10px;height:10px;background:{{ $color }}"></div>
                            <span class="small">{{ $label }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3" style="min-width:160px">
                            <div class="progress flex-grow-1" style="height:8px;border-radius:4px;background:#f0f0f0">
                                @php $pct = $totalHari > 0 ? ($stats[$key] / $totalHari * 100) : 0 @endphp
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:4px"></div>
                            </div>
                            <span class="fw-bold small" style="color:{{ $color }};min-width:20px;text-align:right">
                                {{ $stats[$key] }}
                            </span>
                        </div>
                    </div>
                @endforeach
                <div class="text-center mt-3">
                    <a href="{{ route('presensi.riwayat') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right"></i> Lihat Riwayat Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengumuman ──────────────────────────────────────────────────────────── --}}
    <div class="col-md-7">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-megaphone text-primary me-1"></i> Pengumuman
            </div>
            <div class="card-body p-0">
                @forelse($pengumumans as $p)
                    <div class="px-3 py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="fw-semibold small">{{ $p->judul }}</span>
                            <span class="badge
                                @if($p->target === 'semua') bg-primary
                                @elseif($p->target === 'siswa') bg-success
                                @else bg-warning text-dark
                                @endif
                                ms-2 flex-shrink-0" style="font-size:.65rem">
                                {{ ucfirst($p->target) }}
                            </span>
                        </div>
                        <p class="text-muted small mb-1 mt-1">{{ Str::limit($p->isi, 120) }}</p>
                        <div class="text-muted" style="font-size:.7rem">
                            <i class="bi bi-calendar3"></i> {{ $p->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-bell-slash fs-2"></i>
                        <p class="mt-2 small">Belum ada pengumuman.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Quick Action ──────────────────────────────────────────────────────────── --}}
<div class="mt-4 d-flex gap-2 flex-wrap">
    <a href="{{ route('presensi.index') }}" class="btn btn-primary">
        <i class="bi bi-clock-history me-1"></i> Presensi Sekarang
    </a>
    <a href="{{ route('presensi.riwayat') }}" class="btn btn-outline-primary">
        <i class="bi bi-journal-text me-1"></i> Riwayat Presensi
    </a>
</div>
@endsection
