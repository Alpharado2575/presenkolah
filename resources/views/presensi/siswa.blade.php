@extends('layout.app')
@section('title', 'Data Presensi Siswa')
@section('page-title', 'Data Presensi Siswa')

@section('content')
{{-- Filter ────────────────────────────────────────────────────────────────── --}}
<div class="card stat-card mb-4">
    <div class="card-body">
        <form action="{{ route('presensi.siswa') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('bulan', date('m')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('presensi.siswa') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data Siswa ───────────────────────────────────────────────────────────--}}
<div class="card stat-card">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-people text-primary me-1"></i>
        Rekap Presensi Siswa
        @if(request('bulan') || request('tahun'))
            <span class="text-muted fw-normal fs-6">
                ({{ \Carbon\Carbon::create()->month(request('bulan', date('m')))->translatedFormat('F') }}
                {{ request('tahun', date('Y')) }})
            </span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
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
                    @forelse($dataSiswa as $index => $presensi)
                        <tr>
                            <td class="text-muted small">{{ $dataSiswa->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ $presensi->nama }}</td>
                            <td class="small">{{ $presensi->kelas }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('l') }}</td>
                            <td class="small">{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '—' }}</td>
                            <td class="small">{{ $presensi->jam_pulang ? \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i') : '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $presensi->status }}">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $presensi->keterangan ?? '—' }}</td>
                            <td>
                                @if(isset($presensi->bukti_izin) && $presensi->bukti_izin)
                                    <a href="{{ asset('uploads/bukti/' . $presensi->bukti_izin) }}" target="_blank"
                                       class="btn btn-xs btn-outline-info btn-sm">
                                        <i class="bi bi-paperclip"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data presensi siswa pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dataSiswa->hasPages())
            <div class="px-3 py-3 border-top">
                {{ $dataSiswa->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection