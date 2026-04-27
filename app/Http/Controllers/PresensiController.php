<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Presensi;

class PresensiController extends Controller
{
    public function index()
    {
        $user            = Auth::user();
        $presensiHariIni = $user->presensiHariIni();
        $riwayat         = Presensi::where('user_id', $user->id)
                            ->latest('tanggal')
                            ->take(7)
                            ->get();

        return view('presensi.index', compact('user', 'presensiHariIni', 'riwayat'));
    }

    public function masuk(Request $request)
    {
        $user = Auth::user();

        // Cegah double presensi
        if ($user->presensiHariIni()) {
            return back()->withErrors(['msg' => 'Anda sudah melakukan presensi masuk hari ini.']);
        }

        Presensi::create([
            'user_id'   => $user->id,
            'tanggal'   => today(),
            'jam_masuk' => now()->toTimeString(),
            'status'    => 'hadir',
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'Presensi masuk berhasil dicatat pukul ' . now()->format('H:i'));
    }

    public function pulang(Request $request)
    {
        $user     = Auth::user();
        $presensi = $user->presensiHariIni();

        if (!$presensi) {
            return back()->withErrors(['msg' => 'Anda belum melakukan presensi masuk.']);
        }

        if ($presensi->sudahPulang()) {
            return back()->withErrors(['msg' => 'Anda sudah melakukan presensi pulang.']);
        }

        $presensi->update(['jam_pulang' => now()->toTimeString()]);

        return back()->with('success', 'Presensi pulang berhasil dicatat pukul ' . now()->format('H:i'));
    }

    public function riwayat(Request $request)
    {
        $user  = Auth::user();
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $riwayat = Presensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        $stats = [
            'hadir' => $riwayat->where('status', 'hadir')->count(),
            'izin'  => $riwayat->where('status', 'izin')->count(),
            'sakit' => $riwayat->where('status', 'sakit')->count(),
            'alpha' => $riwayat->where('status', 'alpha')->count(),
        ];

        return view('presensi.riwayat', compact('riwayat', 'stats', 'bulan', 'tahun'));
    }

    public function uploadIzin(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'status'     => 'required|in:izin,sakit',
            'keterangan' => 'nullable|string|max:500',
            'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user  = Auth::user();
        $namaFile = null;

        if ($request->hasFile('bukti')) {
            $file     = $request->file('bukti');
            $namaFile = time() . '_' . $user->id . '.' . $file->extension();
            $file->move(public_path('uploads/izin'), $namaFile);
        }

        Presensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $request->tanggal],
            [
                'status'     => $request->status,
                'keterangan' => $request->keterangan,
                'bukti_izin' => $namaFile,
            ]
        );

        return back()->with('success', 'Keterangan izin/sakit berhasil dikirim.');
    }

    public function dataPresensiSiswa(Request $request)
{
    $bulan = $request->get('bulan', date('m'));
    $tahun = $request->get('tahun', date('Y'));

    $dataSiswa = Presensi::with('user')
        ->whereHas('user', function ($query) {
            $query->where('role', 'siswa');
        })
        ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
        ->latest('tanggal')
        ->paginate(15)
        ->through(function ($presensi) {
            $presensi->nama  = $presensi->user->nama;
            $presensi->kelas = $presensi->user->kelas;
            unset($presensi->user);
            return $presensi;
        });

    return view('presensi.siswa', compact('dataSiswa'));
}
}
