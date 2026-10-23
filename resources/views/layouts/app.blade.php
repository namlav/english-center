<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ Thống Quản Lý Trung Tâm Ngoại Ngữ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --ink:#172b4d; --muted:#66758c; --brand:#3457d5; --brand-dark:#263fa8; --canvas:#f3f6fb; --line:#e5eaf2; --radius:18px; }
        body { min-height:100vh; color:var(--ink); background:radial-gradient(ellipse at 10% 0%,#e9efff 0,transparent 34rem),var(--canvas); font-family:Inter,"Segoe UI",system-ui,-apple-system,sans-serif; }
        .navbar { background:linear-gradient(110deg,#243e9c,#4169e1)!important; padding:.85rem 0; }
        .navbar .container { max-width:1440px; }
        .navbar-brand { letter-spacing:.04em; font-size:1.05rem; }
        .navbar .nav-link { color:rgba(255,255,255,.79)!important; border-radius:10px; padding:.55rem .75rem!important; transition:background .18s,color .18s; }
        .navbar .nav-link:hover,.navbar .nav-link:focus { color:#fff!important; background:rgba(255,255,255,.13); }
        .navbar .nav-link.text-warning { color:#fff!important; background:rgba(255,255,255,.16); }
        .navbar .btn-outline-light { border-color:rgba(255,255,255,.55); border-radius:10px; padding:.45rem .8rem; }
        main.container { max-width:1440px; padding-top:2rem!important; padding-bottom:3rem!important; }
        h1,h2,h3,h4,h5 { color:var(--ink); letter-spacing:-.025em; }
        .text-secondary,.text-muted { color:var(--muted)!important; }
        .card { border:1px solid rgba(222,229,240,.9)!important; border-radius:var(--radius)!important; box-shadow:0 8px 24px rgba(26,45,85,.055)!important; overflow:hidden; }
        .card-header { border-bottom:1px solid var(--line); }
        .card-footer { border-top:1px solid var(--line); }
        .btn { border-radius:10px; font-weight:600; transition:transform .15s,box-shadow .15s,background .15s; }
        .btn:hover { transform:translateY(-1px); box-shadow:0 5px 12px rgba(28,48,91,.12); }
        .btn-primary { --bs-btn-bg:var(--brand); --bs-btn-border-color:var(--brand); --bs-btn-hover-bg:var(--brand-dark); --bs-btn-hover-border-color:var(--brand-dark); }
        .form-control,.form-select { min-height:43px; border-color:#dbe2ed; border-radius:10px; }
        .form-control:focus,.form-select:focus { border-color:#93a8f2; box-shadow:0 0 0 .2rem rgba(52,87,213,.13); }
        .form-label { color:#465873; font-weight:600; }
        .table { --bs-table-striped-bg:#f8faff; }
        .table thead th { color:#52627b; font-size:.78rem; text-transform:uppercase; letter-spacing:.055em; white-space:nowrap; }
        .table-primary { --bs-table-bg:#edf2ff; --bs-table-color:var(--ink); }
        .table td,.table th { padding:.85rem .9rem; border-color:var(--line); }
        .table tbody tr:hover { --bs-table-bg:#f7f9ff; }
        .badge { border-radius:999px; padding:.45em .72em; font-weight:650; }
        .alert { border:0; border-radius:14px; box-shadow:0 5px 16px rgba(27,50,92,.06); }
        .page-heading { margin-bottom:1.35rem; }
        .page-eyebrow { color:var(--brand); font-size:.75rem; font-weight:750; letter-spacing:.12em; text-transform:uppercase; }
        @media(max-width:767.98px) { main.container { padding-top:1.25rem!important; } .navbar .navbar-nav { padding-top:.65rem; } .navbar .nav-link { margin:.1rem 0; } }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-mortarboard-fill me-1"></i> ENGLISH CENTER
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('class_rooms.index') }}">Tra cứu Lớp học</a>
                    </li>

                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('website.index') }}">Quản lý website</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('shop_admin.index') }}">Khóa học &amp; Video</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('class_rooms.index') }}">Lớp học</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teachers.index') }}">Giảng viên</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">Học viên</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Tài khoản</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-warning" href="{{ route('reports.index') }}">Thống kê</a></li>
                    @endif

                    @if(auth()->user()->isTeacher())
                        <li class="nav-item">
                            <a class="nav-link fw-semibold text-warning" href="{{ route('teacher.classes') }}">Lớp tôi phụ trách</a>
                        </li>
                    @endif

                    @if(auth()->user()->isStudent())
                        <li class="nav-item">
                            <a class="nav-link fw-semibold text-warning" href="{{ route('enrollments.my_classes') }}">Lớp đã đăng ký</a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center text-white">
                    <span class="me-3">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        <span class="badge bg-warning text-dark text-uppercase ms-1">{{ auth()->user()->role }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" type="submit">Đăng xuất</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
