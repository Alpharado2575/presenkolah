<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PresenKolah SMKN 7 Semarang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 100%; max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            padding: 2.5rem 2rem;
            text-align: center; color: #fff;
        }
        .login-header .logo-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,.2);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; margin-bottom: 1rem;
        }
        .login-body { padding: 2rem; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #e0e0e0;
            padding: .75rem 1rem; transition: border-color .2s;
        }
        .form-control:focus {
            border-color: #1a237e; box-shadow: 0 0 0 3px rgba(26,35,126,.12);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px !important;
            background: #f5f5f5; border: 1.5px solid #e0e0e0; border-right: none;
        }
        .input-group .form-control { border-radius: 0 10px 10px 0 !important; }
        .btn-login {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            border: none; border-radius: 10px;
            padding: .75rem; font-weight: 600; color: #fff;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .role-badge {
            display: inline-block;
            padding: 3px 10px; border-radius: 20px;
            font-size: .75rem; font-weight: 500; margin: 2px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="logo-icon"><i class="bi bi-check2-square"></i></div>
        <h4 class="mb-0 fw-bold">PresenKolah</h4>
        <p class="mb-2 opacity-75 small">SMKN 7 Semarang</p>
        <div>
            <span class="role-badge bg-white bg-opacity-25 text-white">Siswa</span>
            <span class="role-badge bg-white bg-opacity-25 text-white">Guru</span>
            <span class="role-badge bg-white bg-opacity-25 text-white">Admin</span>
        </div>
    </div>

    <div class="login-body">
        <h5 class="fw-bold mb-1">Masuk ke Akun</h5>
        <p class="text-muted small mb-4">Gunakan username dan password yang diberikan admin.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            {{-- Username --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}"
                           placeholder="Masukkan username"
                           autocomplete="username" autofocus required>
                </div>
                @error('username')
                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Masukkan password"
                           autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePw">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            Lupa password? Hubungi admin sekolah.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.querySelector('input[name="password"]');
        const icon = document.getElementById('eyeIcon');
        if (pw.type === 'password') {
            pw.type = 'text'; icon.className = 'bi bi-eye-slash';
        } else {
            pw.type = 'password'; icon.className = 'bi bi-eye';
        }
    });
</script>
</body>
</html>
