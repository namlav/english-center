<aside class="account-sidebar">
    <div class="account-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1, 'UTF-8'), 'UTF-8') }}</div>
    <strong>{{ $user->name }}</strong>
    <p>{{ $user->email }}</p>
    <nav aria-label="Tài khoản học tập">
        <a href="{{ route('account.courses') }}" @if(request()->routeIs('account.courses')) aria-current="page" @endif>
            <x-study-icon name="book"/>Khóa học của tôi
        </a>
        <a href="{{ route('enrollments.my_classes') }}" @if(request()->routeIs('enrollments.my_classes')) aria-current="page" @endif>
            <x-study-icon name="academic"/>Lớp học trực tiếp
        </a>
        <a href="{{ route('account.schedule') }}" @if(request()->routeIs('account.schedule')) aria-current="page" @endif>
            <x-study-icon name="clock"/>Lịch học của tôi
        </a>
        <a href="{{ route('account.show') }}" @if(request()->routeIs('account.show')) aria-current="page" @endif>
            <x-study-icon name="user"/>Trang cá nhân
        </a>
        <a href="{{ route('orders.index') }}" @if(request()->routeIs('orders.*')) aria-current="page" @endif>
            <x-study-icon name="file"/>Đơn hàng & Hóa đơn
        </a>
    </nav>
</aside>
