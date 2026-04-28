<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
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

        if ($user->presensiHariIni()) {
            return back()->withErrors(['msg' => 'Anda sudah melakukan presensi masuk hari ini.']);
        }

        // ✅ VALIDASI FOTO
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // ✅ UPLOAD FOTO
        $file = $request->file('foto');
        $filename = 'masuk_' . $user->id . '_' . time() . '.' . $file->extension();

        // simpan lokal
        $file->storeAs('presensi', $filename, 'public');

        // upload ke FTP
        try {
            Storage::disk('ftp')->put(
                $filename,
                fopen($file->getRealPath(), 'r+')
            );
        } catch (\Exception $e) {
            \Log::error('FTP ERROR: ' . $e->getMessage());
        }

        // ✅ SIMPAN DB
        Presensi::create([
            'user_id'   => $user->id,
            'tanggal'   => today('Asia/Jakarta'),
            'jam_masuk' => now('Asia/Jakarta')->toTimeString(),
            'status'    => 'hadir',
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'foto_masuk'      => $filename
        ]);

        // ✅ EMAIL
        try {
            Mail::raw("Presensi MASUK berhasil\nUser: {$user->nama}\nJam: " . now()->format('H:i'), function ($message) {
                $message->to('muhammadalfarado5@gmail.com')
                        ->subject('Presensi Masuk');
            });
        } catch (\Exception $e) {
            \Log::error('MAIL ERROR: '.$e->getMessage());
        }
        return back()->with('success', 'Presensi masuk berhasil pukul ' . now('Asia/Jakarta')->format('H:i'));
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

        // ✅ VALIDASI FOTO
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // ✅ UPLOAD FOTO
        $file = $request->file('foto');
        $filename = 'pulang_' . $user->id . '_' . time() . '.' . $file->extension();

        $file->storeAs('presensi', $filename, 'public');

        // FTP
        try {
            Storage::disk('ftp')->put(
                $filename,
                fopen($file->getRealPath(), 'r+')
            );
        } catch (\Exception $e) {
            \Log::error('FTP ERROR: ' . $e->getMessage());
        }

        // ✅ UPDATE DB
        $presensi->update([
            'jam_pulang' => now('Asia/Jakarta')->toTimeString(),
            'foto_pulang'       => $filename // overwrite atau bisa pisah kolom kalau mau
        ]);

        // EMAIL
        try {
            Mail::raw("Presensi PULANG berhasil\nUser: {$user->nama}\nJam: " . now()->format('H:i'), function ($message) {
                $message->to('muhammadalfarado5@gmail.com')
                        ->subject('Presensi Pulang');
            });
        } catch (\Exception $e) {
            \Log::error('MAIL PULANG ERROR: '.$e->getMessage());
        }

        return back()->with('success', 'Presensi pulang berhasil pukul ' . now('Asia/Jakarta')->format('H:i'));
    }

    public function riwayat(Request $request)
    {
        $user  = Auth::user();
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Query dasar
        $query = Presensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        // Data untuk tabel
        $riwayat = (clone $query)->orderBy('tanggal', 'desc')->paginate(20);

        // Data untuk statistik total sebulan
        $stats = [
            'hadir' => (clone $query)->where('status', 'hadir')->count(),
            'izin'  => (clone $query)->where('status', 'izin')->count(),
            'sakit' => (clone $query)->where('status', 'sakit')->count(),
            'alpha' => (clone $query)->where('status', 'alpha')->count(),
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
            $file->storeAs('izin', $namaFile, 'public');
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
<<<<<<< Updated upstream

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
=======
}
>>>>>>> Stashed changes
