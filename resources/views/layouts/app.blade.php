<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ Thống Quản Lý Trung Tâm Ngoại Ngữ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('class_rooms.index') }}">
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
                        <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">Khóa học</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teachers.index') }}">Giáo viên</a></li>
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
