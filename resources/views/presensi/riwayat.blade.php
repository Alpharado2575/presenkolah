@extends('layout.app')
@section('title', 'Riwayat Presensi')
@section('page-title', 'Riwayat Presensi')

@section('content')
{{-- Filter ────────────────────────────────────────────────────────────────── --}}
<div class="card stat-card mb-4">
    <div class="card-body">
        <form action="{{ route('presensi.riwayat') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Statistik ─────────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['hadir', 'Hadir',  'success', '#157347',  '#d1f0e0', 'bi-check-circle'],
        ['izin',  'Izin',   'primary', '#0a58ca',  '#d0e9ff', 'bi-file-earmark'],
        ['sakit', 'Sakit',  'warning', '#856404',  '#fff3cd', 'bi-thermometer'],
        ['alpha', 'Alpha',  'danger',  '#842029',  '#f8d7da', 'bi-x-circle'],
    ] as [$key, $label, $variant, $color, $bg, $icon])
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="icon-box mx-auto mb-2"
                     style="background:{{ $bg }};color:{{ $color }};width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div class="fw-bold fs-4" style="color:{{ $color }}">{{ $stats[$key] }}</div>
                <div class="text-muted small">{{ $label }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabel Riwayat ──────────────────────────────────────────────────────────── --}}
<div class="card stat-card">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-journal-text text-primary me-1"></i>
        Riwayat Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $i => $r)
                        <tr>
                            <td class="text-muted small">{{ $riwayat->firstItem() + $i }}</td>
                            <td class="small fw-semibold">{{ $r->tanggal->format('d/m/Y') }}</td>
                            <td class="small">{{ $r->tanggal->translatedFormat('l') }}</td>
                            <td class="small">{{ $r->jam_masuk  ? \Carbon\Carbon::parse($r->jam_masuk)->format('H:i')  : '—' }}</td>
                            <td class="small">{{ $r->jam_pulang ? \Carbon\Carbon::parse($r->jam_pulang)->format('H:i') : '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $r->status }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $r->keterangan ?? '—' }}</td>
                            <td>
                                @if($r->buktiUrl())
                                    <a href="{{ $r->buktiUrl() }}" target="_blank"
                                       class="btn btn-xs btn-outline-info btn-sm">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                Tidak ada data presensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="px-3 py-3 border-top">
                {{ $riwayat->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
