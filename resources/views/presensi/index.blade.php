@extends('layout.app')
@section('title', 'Presensi')
@section('page-title', 'Presensi')

@section('content')
<div class="row g-4">
    {{-- Panel Presensi Hari Ini ────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-bold border-0 pt-3 pb-0">
                <i class="bi bi-clock-history text-primary me-1"></i>
                Presensi Hari Ini —
                <span class="text-primary">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="card-body">
                @php
                    $sudahMasuk  = $presensiHariIni?->sudahMasuk();
                    $sudahPulang = $presensiHariIni?->sudahPulang();
                @endphp

                {{-- Status bar --}}
                <div class="d-flex gap-3 mb-4">
                    <div class="text-center flex-fill p-3 rounded"
                         style="background: {{ $sudahMasuk ? '#d1f0e0' : '#f5f5f5' }}">
                        <div style="font-size:1.8rem">{{ $sudahMasuk ? '✅' : '⏳' }}</div>
                        <div class="small fw-semibold mt-1">Masuk</div>
                        <div class="small text-muted">
                            {{ $sudahMasuk
                                ? \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i')
                                : '—' }}
                        </div>
                    </div>
                    <div class="text-center flex-fill p-3 rounded"
                         style="background: {{ $sudahPulang ? '#d0e9ff' : '#f5f5f5' }}">
                        <div style="font-size:1.8rem">{{ $sudahPulang ? '🏠' : '⏳' }}</div>
                        <div class="small fw-semibold mt-1">Pulang</div>
                        <div class="small text-muted">
                            {{ $sudahPulang
                                ? \Carbon\Carbon::parse($presensiHariIni->jam_pulang)->format('H:i')
                                : '—' }}
                        </div>
                    </div>
                </div>

                {{-- Tombol Presensi --}}
                @if(!$sudahMasuk)
                    <form action="{{ route('presensi.masuk') }}" method="POST">
                        @csrf
                        <input type="hidden" name="latitude"  id="lat">
                        <input type="hidden" name="longitude" id="lng">
                        <button type="submit" class="btn btn-success w-100 py-3"
                                onclick="getLocation()">
                            <i class="bi bi-geo-alt-fill me-1"></i> Presensi Masuk
                        </button>
                    </form>
                @elseif(!$sudahPulang)
                    <form action="{{ route('presensi.pulang') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 py-3">
                            <i class="bi bi-house-door-fill me-1"></i> Presensi Pulang
                        </button>
                    </form>
                @else
                    <div class="alert alert-success text-center mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Presensi hari ini sudah selesai. Sampai jumpa besok!
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Form Izin / Sakit ──────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-bold border-0 pt-3 pb-0">
                <i class="bi bi-file-earmark-text text-warning me-1"></i> Keterangan Izin / Sakit
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Jika tidak dapat hadir, isi form ini dan upload bukti pendukung (surat dokter / surat orang tua).
                </p>
                <form action="{{ route('presensi.izin') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Tidak Hadir</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal', today()->toDateString()) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jenis Ketidakhadiran</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status"
                                       value="izin" id="statusIzin"
                                       {{ old('status', 'izin') === 'izin' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusIzin">Izin</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status"
                                       value="sakit" id="statusSakit"
                                       {{ old('status') === 'sakit' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusSakit">Sakit</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                                  placeholder="Jelaskan alasan ketidakhadiran...">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Bukti (opsional)</label>
                        <input type="file" name="bukti" class="form-control"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">Format: JPG, PNG, PDF. Maks 2MB.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-1"></i> Kirim Keterangan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat 7 Hari ─────────────────────────────────────────────────────────── --}}
<div class="card stat-card mt-4">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-journal-text text-primary me-1"></i> 7 Hari Terakhir
        <a href="{{ route('presensi.riwayat') }}" class="btn btn-sm btn-outline-primary float-end">
            Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td class="small">{{ $r->tanggal->translatedFormat('D, d M Y') }}</td>
                            <td class="small">{{ $r->jam_masuk  ? \Carbon\Carbon::parse($r->jam_masuk)->format('H:i')  : '—' }}</td>
                            <td class="small">{{ $r->jam_pulang ? \Carbon\Carbon::parse($r->jam_pulang)->format('H:i') : '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $r->status }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $r->keterangan ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada riwayat presensi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
        });
    }
}
</script>
@endpush
