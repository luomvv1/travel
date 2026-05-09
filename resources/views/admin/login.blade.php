<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Đăng nhập quản trị</title>

    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #1e293b;
            --panel: rgba(255, 255, 255, 0.96);
            --accent: #2563eb;
            --accent-2: #1d4ed8;
        }

        html, body {
            height: 100%;
        }

        body.login-page {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 30%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }

        .auth-shell {
            width: min(1040px, calc(100% - 32px));
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
            background: var(--panel);
        }

        .auth-hero {
            padding: 56px 48px;
            background:
                linear-gradient(160deg, rgba(37, 99, 235, 0.98), rgba(15, 23, 42, 0.98)),
                url('{{ asset('clients/assets/images/login/signin-image.jpg') }}') center/cover no-repeat;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 620px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .auth-hero h1 {
            font-size: clamp(32px, 4vw, 54px);
            line-height: 1.05;
            margin: 24px 0 12px;
            font-weight: 800;
        }

        .auth-hero p {
            max-width: 440px;
            font-size: 16px;
            line-height: 1.7;
            opacity: 0.92;
        }

        .hero-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 24px;
            font-size: 14px;
            opacity: 0.92;
        }

        .auth-form-wrap {
            padding: 56px 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-card h2 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .auth-card .subtitle {
            color: #64748b;
            margin-bottom: 28px;
        }

        .form-floating > .form-control {
            height: 58px;
            padding: 1.1rem 1rem 0.35rem;
            border-radius: 16px;
        }

        .form-floating > label {
            padding: 1rem 1rem;
            color: #64748b;
        }

        .form-control:focus {
            border-color: rgba(37, 99, 235, 0.55);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
        }

        .btn-login {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 14px 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #fff;
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.28);
        }

        .btn-login:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 14px;
        }

        @media (max-width: 992px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-hero {
                min-height: auto;
                padding: 40px 28px;
            }

            .auth-form-wrap {
                padding: 36px 24px 40px;
            }
        }
    </style>
</head>

<body class="login-page">
    <div class="auth-shell">
        <div class="auth-hero">
            <div>
                <div class="brand-badge">
                    <i class="fa fa-shield"></i>
                    Quản trị hệ thống
                </div>
                <h1>Đăng nhập admin</h1>
                <p>Chỉ dùng tài khoản quản trị để truy cập trang quản lý. Không có đăng ký, không có đăng nhập Google hay tùy chọn ngoài luồng.</p>
            </div>

            <div class="hero-footer">
                <span><i class="fa fa-lock"></i> Khu vực bảo mật</span>
                <span><i class="fa fa-clock-o"></i> Quản lý nhanh, tập trung</span>
            </div>
        </div>

        <div class="auth-form-wrap">
            <div class="auth-card">
                <h2>Đăng nhập</h2>
                <p class="subtitle">Nhập tài khoản và mật khẩu admin để tiếp tục.</p>

                @if(session('error_login'))
                    <div class="alert alert-danger">{{ session('error_login') }}</div>
                @endif

                @if ($errors->has('username') || $errors->has('password'))
                    <div class="alert alert-danger">
                        {{ $errors->first('username') ?: $errors->first('password') }}
                    </div>
                @endif

                <form action="{{ route('admin.login-account') }}" method="POST" id="formLoginAdmin" novalidate>
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="username" id="username" placeholder="Tài khoản" value="{{ old('username') }}" required>
                        <label for="username">Tài khoản</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu" required>
                        <label for="password">Mật khẩu</label>
                    </div>

                    <button type="submit" class="btn btn-login">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>