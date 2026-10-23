<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trung tâm Quản trị Admin — English Center')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/english-center-mark.svg') }}">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Metis Admin Styles -->
    <link rel="stylesheet" href="{{ asset('css/metis-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commerce.css') }}">
    
    <style>
        /* Metis Custom Utilities */
        .metis-nav-link.has-sub {
            cursor: pointer;
        }
        .metis-nav-arrow {
            margin-left: auto;
            transition: transform 0.2s ease;
            font-size: 0.75rem;
        }
        .metis-collapse.show + .metis-nav-link .metis-nav-arrow,
        .metis-nav-item.open .metis-nav-arrow {
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="metis-admin-body">
<div class="metis-wrapper">

    <!-- METIS SIDEBAR BÊN TRÁI -->
    <aside class="metis-sidebar" id="metisSidebar">
        <!-- Brand Header -->
        <div class="metis-sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="metis-brand">
                <div class="metis-brand-logo">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="metis-brand-text">
                    <span>METIS ADMIN</span>
                    <small style="display: block; font-size: 10px; color: #60a5fa; font-weight: 600;">ENGLISH CENTER</small>
                </div>
            </a>
        </div>

        <!-- User Info -->
        <div class="metis-sidebar-user">
            <div class="metis-avatar">
                {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="metis-user-info">
                <div class="metis-user-name">{{ auth()->user()->name ?? 'Quản trị viên' }}</div>
                <div class="metis-user-role">
                    <span class="metis-status-indicator"></span> Quản trị tối cao (Admin)
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Tree -->
        <nav class="metis-sidebar-nav">
            <div class="metis-nav-heading">BẢNG ĐIỀU KHIỂN & BÁO CÁO</div>
            <div class="metis-nav-item">
                <a href="{{ route('admin.dashboard') }}" class="metis-nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Tổng quan & Doanh thu</span>
                </a>
            </div>

            <div class="metis-nav-heading">E-LEARNING & GIÁO TRÌNH</div>
            <!-- Khóa học & Bài học -->
            <div class="metis-nav-item">
                <a href="{{ route('shop_admin.index') }}" class="metis-nav-link {{ request()->routeIs('shop_admin.*') && !request()->routeIs('shop_admin.orders.*') && !request()->routeIs('shop_admin.bank.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Khóa học & Video</span>
                    <span class="metis-badge bg-primary">21 khóa</span>
                </a>
            </div>

            <!-- Khảo thí & Đề thi -->
            <div class="metis-nav-item">
                <a href="{{ route('exam_admin.index') }}" class="metis-nav-link {{ request()->routeIs('exam_admin.*') ? 'active' : '' }}">
                    <i class="bi bi-card-checklist"></i>
                    <span>Ngân hàng Đề thi thử</span>
                </a>
            </div>

            <div class="metis-nav-heading">BÁN HÀNG & DOANH THU</div>
            <!-- Đơn hàng -->
            <div class="metis-nav-item">
                <a href="{{ route('shop_admin.orders.index') }}" class="metis-nav-link {{ request()->routeIs('shop_admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check-fill"></i>
                    <span>Đơn hàng & Kích hoạt</span>
                </a>
            </div>

            <!-- VietQR -->
            <div class="metis-nav-item">
                <a href="{{ route('shop_admin.bank.edit') }}" class="metis-nav-link {{ request()->routeIs('shop_admin.bank.*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Cấu hình VietQR</span>
                </a>
            </div>



            <div class="metis-nav-heading">HỆ THỐNG & TÀI KHOẢN</div>
            <!-- Tài khoản người dùng -->
            <div class="metis-nav-item">
                <a href="{{ route('users.index') }}" class="metis-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Quản lý Tài khoản</span>
                </a>
            </div>

            <!-- Học viên -->
            <div class="metis-nav-item">
                <a href="{{ route('students.index') }}" class="metis-nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Hồ sơ Học viên</span>
                </a>
            </div>

            <!-- Giảng viên -->
            <div class="metis-nav-item">
                <a href="{{ route('teachers.index') }}" class="metis-nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Đội ngũ Giảng viên</span>
                </a>
            </div>

            <div class="metis-nav-heading">TRUNG TÂM & NỘI DUNG</div>
            <!-- Lớp học -->
            <div class="metis-nav-item">
                <a href="{{ route('class_rooms.index') }}" class="metis-nav-link {{ request()->routeIs('class_rooms.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Lớp học & Phòng học</span>
                </a>
            </div>

            <!-- Quản lý Website & Banner -->
            <div class="metis-nav-item">
                <a href="{{ route('website.home.edit') }}" class="metis-nav-link {{ request()->routeIs('website.home.*') ? 'active' : '' }}">
                    <i class="bi bi-image-fill"></i>
                    <span>Banner & Trang chủ</span>
                </a>
            </div>
            <div class="metis-nav-item">
                <a href="{{ route('website.index') }}" class="metis-nav-link {{ request()->routeIs('website.index') || request()->routeIs('website.teachers.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Giáo viên Banner</span>
                </a>
            </div>
        </nav>

        <!-- Bottom Link to Public Web -->
        <div style="padding: 1rem 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
            <a href="{{ route('home') }}" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 8px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); color: #f8fafc; font-size: 13px; font-weight: 600; text-decoration: none; transition: background 0.15s;">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Xem Website Học viên</span>
            </a>
        </div>
    </aside>

    <!-- METIS MAIN CONTENT VÙNG NỘI DUNG -->
    <div class="metis-main">
        <!-- Topbar Header -->
        <header class="metis-topbar">
            <div class="metis-topbar-left">
                <button class="metis-toggle-btn" id="metisSidebarToggle" title="Ẩn/Hiện Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="metis-topbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Tìm kiếm nhanh tác vụ quản trị...">
                </div>
            </div>

            <div class="metis-topbar-right">
                <a href="{{ route('home') }}" target="_blank" class="metis-btn-client">
                    <i class="bi bi-globe"></i> Trang Học viên
                </a>

                <!-- Admin Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="metis-avatar" style="width: 36px; height: 36px; font-size: 0.9rem;">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline fw-semibold text-dark fs-6">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <i class="bi bi-chevron-down text-muted fs-7"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                        <li class="px-3 py-2 border-bottom">
                            <strong class="d-block text-dark">{{ auth()->user()->name }}</strong>
                            <small class="text-muted text-break">{{ auth()->user()->email }}</small>
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('account.show') }}"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Bảng điều khiển</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-4 mb-0 rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-4 mb-0 rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-4 mb-0 rounded-3 shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <strong>Vui lòng kiểm tra lại thông tin:</strong>
                </div>
                <ul class="mb-0 ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        <main class="metis-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="metis-footer">
            <div>
                © {{ date('Y') }} <strong>English Center</strong> — Trung tâm Quản trị Metis Edition.
            </div>
            <div>
                Phiên bản: <span class="badge bg-primary-subtle text-primary">v2.4 LMS Online</span>
            </div>
        </footer>
    </div>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Sidebar Desktop & Mobile
    document.getElementById('metisSidebarToggle')?.addEventListener('click', function() {
        const sidebar = document.getElementById('metisSidebar');
        if (window.innerWidth >= 992) {
            sidebar.classList.toggle('collapsed');
        } else {
            sidebar.classList.toggle('mobile-open');
        }
    });

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            const sidebar = document.getElementById('metisSidebar');
            const toggle = document.getElementById('metisSidebarToggle');
            if (sidebar.classList.contains('mobile-open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        }
    });
</script>
@yield('scripts')
</body>
</html>
