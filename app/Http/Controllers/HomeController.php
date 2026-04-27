<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Pengumuman;
use App\Models\Presensi;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pengumuman untuk role ini atau 'semua'
        $pengumumans = Pengumuman::where('target', 'semua')
            ->orWhere('target', $user->role)
            ->latest()
            ->take(5)
            ->get();

        // Statistik presensi bulan ini
        $stats = [
            'hadir' => Presensi::where('user_id', $user->id)->bulanIni()->where('status', 'hadir')->count(),
            'izin'  => Presensi::where('user_id', $user->id)->bulanIni()->where('status', 'izin')->count(),
            'sakit' => Presensi::where('user_id', $user->id)->bulanIni()->where('status', 'sakit')->count(),
            'alpha' => Presensi::where('user_id', $user->id)->bulanIni()->where('status', 'alpha')->count(),
        ];

        $presensiHariIni = $user->presensiHariIni();

        // Data tambahan untuk guru: semua presensi siswa, dengan nama & kelas
        $dataSiswa = null;
        if ($user->isGuru()) {
            $dataSiswa = Presensi::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'siswa');
                })
                ->latest('tanggal')
                ->get()
                ->map(function ($presensi) {
                    // Tambahkan field nama dan kelas dari relasi user
                    $presensi->nama  = $presensi->user->nama;
                    $presensi->kelas = $presensi->user->kelas;

                    // Hapus relasi user agar output lebih bersih (opsional)
                    unset($presensi->user);

                    return $presensi;
                });
        }
        return view('home.index', compact('user', 'pengumumans', 'stats', 'presensiHariIni', 'dataSiswa'));
    }
}