<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',      // 'siswa' | 'guru' | 'admin'
        'kelas',     // diisi jika siswa
        'foto',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────
    public function isAdmin(): bool  { return $this->role === 'admin'; }
    public function isGuru(): bool   { return $this->role === 'guru'; }
    public function isSiswa(): bool  { return $this->role === 'siswa'; }

    public function presensiHariIni()
    {
        return $this->presensis()
            ->whereDate('tanggal', today())
            ->first();
    }

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('uploads/foto/' . $this->foto)
            : asset('img/default-avatar.png');
    }
}
