<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pengumuman;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────────
        $admin = User::create([
            'nama'      => 'Administrator',
            'username'  => 'admin',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ── Guru ───────────────────────────────────────────────────────────────
        $guru1 = User::create([
            'nama'      => 'Budi Santoso, S.Kom',
            'username'  => 'guru.budi',
            'password'  => Hash::make('guru123'),
            'role'      => 'guru',
            'is_active' => true,
        ]);

        User::create([
            'nama'      => 'Sri Wahyuni, S.Pd',
            'username'  => 'guru.sri',
            'password'  => Hash::make('guru123'),
            'role'      => 'guru',
            'is_active' => true,
        ]);

        // ── Siswa ──────────────────────────────────────────────────────────────
        $siswas = [
            ['Gabriel Caesar Saputra',       'siswa.gabriel', 'XI-SIJA-1'],
            ['Muhammad Alfarado Pradipa',     'siswa.alfarado','XI-SIJA-1'],
            ['Navizha Aulya Mitzy Bidari',    'siswa.navizha', 'XI-SIJA-1'],
            ['Nisrina Naura Naniah',          'siswa.nisrina', 'XI-SIJA-1'],
            ['Samuel Marcello',               'siswa.samuel',  'XI-SIJA-1'],
        ];

        foreach ($siswas as [$nama, $username, $kelas]) {
            User::create([
                'nama'      => $nama,
                'username'  => $username,
                'password'  => Hash::make('siswa123'),
                'role'      => 'siswa',
                'kelas'     => $kelas,
                'is_active' => true,
            ]);
        }

        // ── Pengumuman Contoh ──────────────────────────────────────────────────
        Pengumuman::create([
            'judul'       => 'Selamat Datang di Sistem Presensi Digital SMKN 7 Semarang',
            'isi'         => 'Sistem presensi digital ini digunakan untuk mencatat kehadiran siswa dan guru secara online. Pastikan presensi dilakukan setiap hari sebelum pukul 07.30.',
            'target'      => 'semua',
            'dibuat_oleh' => $admin->id,
        ]);

        Pengumuman::create([
            'judul'       => 'Tata Cara Presensi Online',
            'isi'         => 'Klik menu Presensi, lalu tekan tombol Presensi Masuk saat tiba di sekolah. Jangan lupa Presensi Pulang saat meninggalkan sekolah. Jika tidak hadir, upload bukti izin/sakit.',
            'target'      => 'siswa',
            'dibuat_oleh' => $admin->id,
        ]);
    }
}
