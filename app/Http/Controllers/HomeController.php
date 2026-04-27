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

        return view('home.index', compact('user', 'pengumumans', 'stats', 'presensiHariIni'));
    }
}
