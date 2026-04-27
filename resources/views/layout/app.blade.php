<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Presensi Digital') — SMKN 7 Semarang</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary:   #0d6efd;
            --secondary: #6c757d;
            --sidebar-w: 260px;
        }

        body { background: #f4f6fb; font-family: 'Segoe UI', sans-serif; }

        /* ── Sidebar ─────────────────────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
            color: #fff; overflow-y: auto; z-index: 1000;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-brand img { width: 42px; }
        .sidebar-brand .school-name { font-size: .75rem; opacity: .75; }
        .sidebar-brand .app-name   { font-size: 1rem; font-weight: 700; }

        .sidebar .nav-link {
            color: rgba(255,255,255,.8); border-radius: 8px;
            margin: 2px 12px; padding: 10px 14px;
            display: flex; align-items: center; gap: 10px;
            transition: background .2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.18); color: #fff;
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 22px; text-align: center; }
        .sidebar-section-label {
            padding: 1rem 1.5rem .3rem;
            font-size: .7rem; text-transform: uppercase;
            letter-spacing: .1em; opacity: .5;
        }

        /* ── Main content ────────────────────────────────────────────────────── */
        .main-wrapper { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.08);
            padding: .75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 999;
        }
        .content { padding: 1.75rem 1.5rem; }

        /* ── Stat cards ──────────────────────────────────────────────────────── */
        .stat-card {
            border: none; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
        .stat-card .icon-box {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* ── Badges ──────────────────────────────────────────────────────────── */
        .badge-hadir  { background: #d1f0e0; color: #157347; }
        .badge-izin   { background: #d0e9ff; color: #0a58ca; }
        .badge-sakit  { background: #fff3cd; color: #856404; }
        .badge-alpha  { background: #f8d7da; color: #842029; }

        /* ── Alert flash ──────────────────────────────────────────────────────── */
        .alert { border-radius: 10px; }

        /* ── Responsive ──────────────────────────────────────────────────────── */
        @media (max-width: 767px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────────────────────────── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <div>
            <div class="app-name"><i class="bi bi-check2-square"></i> PresenKolah</div>
            <div class="school-name">SMKN 7 Semarang</div>
        </div>
    </div>

    <nav class="mt-2 flex-grow-1">
        @auth
            @if(auth()->user()->isAdmin())
                <div class="sidebar-section-label">Admin Menu</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link @active('admin.dashboard')">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.users') }}"
                   class="nav-link @active('admin.users')">
                    <i class="bi bi-people"></i> Kelola Pengguna
                </a>
                <a href="{{ route('admin.presensi') }}"
                   class="nav-link @active('admin.presensi')">
                    <i class="bi bi-calendar-check"></i> Data Presensi
                </a>
                <a href="{{ route('admin.pengumuman') }}"
                   class="nav-link @active('admin.pengumuman')">
                    <i class="bi bi-megaphone"></i> Pengumuman
                </a>
            @else
                <div class="sidebar-section-label">Menu Utama</div>
                <a href="{{ route('home') }}"
                   class="nav-link @active('home')">
                    <i class="bi bi-house"></i> Beranda
                </a>

                {{-- Menu khusus untuk Guru --}}
                @if(auth()->user()->isGuru())
                    <a href="{{ route('presensi.siswa') }}"
                       class="nav-link @active('presensi.siswa')">
                        <i class="bi bi-people"></i> Data Siswa
                    </a>
                @endif

                <a href="{{ route('presensi.index') }}"
                   class="nav-link @active('presensi.index')">
                    <i class="bi bi-clock-history"></i> Presensi
                </a>
                <a href="{{ route('presensi.riwayat') }}"
                   class="nav-link @active('presensi.riwayat')">
                    <i class="bi bi-journal-text"></i> Riwayat
                </a>
            @endif
        @endauth
    </nav>

    @auth
    <div class="p-3 border-top border-white border-opacity-25">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                 style="width:36px;height:36px;flex-shrink:0">
                <i class="bi bi-person text-white"></i>
            </div>
            <div style="line-height:1.2; overflow:hidden">
                <div class="text-white fw-semibold text-truncate" style="font-size:.85rem">
                    {{ auth()->user()->nama }}
                </div>
                <div style="font-size:.7rem;opacity:.6">
                    {{ ucfirst(auth()->user()->role) }}
                    @if(auth()->user()->kelas) — {{ auth()->user()->kelas }} @endif
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
    @endauth
</aside>

{{-- ── Main ──────────────────────────────────────────────────────────────────── --}}
<div class="main-wrapper">
    {{-- Topbar --}}
    <div class="topbar">
        <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="fw-semibold text-primary">@yield('page-title', 'Dashboard')</div>
        <div class="text-muted small">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <div class="content">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>
@stack('scripts')
</body>
</html>
