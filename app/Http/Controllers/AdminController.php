<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Pengumuman;

class AdminController extends Controller
{
    // ─── Dashboard ──────────────────────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_guru'  => User::where('role', 'guru')->count(),
            'hadir_hari_ini' => Presensi::hariIni()->where('status', 'hadir')->count(),
            'izin_hari_ini'  => Presensi::hariIni()->whereIn('status', ['izin', 'sakit'])->count(),
            'alpha_hari_ini' => Presensi::hariIni()->where('status', 'alpha')->count(),
        ];

        $recentPresensi = Presensi::with('user')
            ->hariIni()
            ->latest()
            ->take(10)
            ->get();

        $pengumumans = Pengumuman::with('pembuat')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPresensi', 'pengumumans'));
    }

    // ─── Users ──────────────────────────────────────────────────────────────────
    public function users(Request $request)
    {
        $users = User::query()
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%")
                                                   ->orWhere('username', 'like', "%{$request->search}%"))
            ->orderBy('role')
            ->orderBy('nama')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:100',
            'username'  => 'required|string|unique:users|max:50',
            'password'  => 'required|min:6',
            'role'      => 'required|in:siswa,guru,admin',
            'kelas'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:100',
            'username'  => "required|string|unique:users,username,{$user->id}|max:50",
            'role'      => 'required|in:siswa,guru,admin',
            'kelas'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // ─── Presensi ───────────────────────────────────────────────────────────────
    public function presensi(Request $request)
    {
        $presensis = Presensi::with('user')
            ->when($request->tanggal, fn($q) => $q->whereDate('tanggal', $request->tanggal))
            ->when($request->role,    fn($q) => $q->byRole($request->role))
            ->when($request->status,  fn($q) => $q->where('status', $request->status))
            ->latest('tanggal')
            ->paginate(25);

        return view('admin.presensi', compact('presensis'));
    }

    public function updatePresensi(Request $request, Presensi $presensi)
    {
        $data = $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string|max:500',
            'jam_masuk'  => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
        ]);

        $presensi->update($data);

        return back()->with('success', 'Data presensi berhasil diperbarui.');
    }

    public function exportPresensi(Request $request)
    {
        $tanggal = $request->tanggal ?? today()->toDateString();

        $presensis = Presensi::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('user_id')
            ->get();

        $csvRows   = [['Nama', 'Username', 'Role', 'Kelas', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan']];
        foreach ($presensis as $p) {
            $csvRows[] = [
                $p->user->nama,
                $p->user->username,
                $p->user->role,
                $p->user->kelas ?? '-',
                $p->tanggal->format('Y-m-d'),
                $p->jam_masuk  ?? '-',
                $p->jam_pulang ?? '-',
                $p->status,
                $p->keterangan ?? '-',
            ];
        }

        $fileName = "presensi_{$tanggal}.csv";
        $handle   = fopen('php://temp', 'r+');
        foreach ($csvRows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    // ─── Pengumuman ─────────────────────────────────────────────────────────────
    public function pengumuman()
    {
        $pengumumans = Pengumuman::with('pembuat')->latest()->paginate(15);
        return view('admin.pengumuman', compact('pengumumans'));
    }

    public function storePengumuman(Request $request)
    {
        $data = $request->validate([
            'judul'  => 'required|string|max:200',
            'isi'    => 'required|string',
            'target' => 'required|in:semua,siswa,guru',
        ]);

        $data['dibuat_oleh'] = auth()->id();

        Pengumuman::create($data);

        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroyPengumuman(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }
}
