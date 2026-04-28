<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',       // 'hadir' | 'izin' | 'sakit' | 'alpha'
        'keterangan',
        'bukti_izin',   // nama file yang di-upload ke FTP/storage
        'latitude',
        'longitude',
        'foto_masuk',
        'foto_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('user', fn($q) => $q->where('role', $role));
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────
    public function sudahMasuk(): bool  { return !is_null($this->jam_masuk); }
    public function sudahPulang(): bool { return !is_null($this->jam_pulang); }

    public function badgeColor(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'izin'  => 'info',
            'sakit' => 'warning',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }

    public function buktiUrl(): ?string
    {
        return $this->bukti_izin
            ? asset('uploads/izin/' . $this->bukti_izin)
            : null;
    }
}
