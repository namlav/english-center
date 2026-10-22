@extends('layouts.learning')
@section('title','Cập nhật dữ liệu website — English Center')
@section('content')
<section class="shell admin-page"><h1>Cập nhật dữ liệu website</h1><div class="detail-panel"><h2>Bổ sung hai bảng quản lý nội dung</h2><p>Database hiện tại cần thêm bảng hồ sơ giáo viên công khai và cấu hình trang chủ. File cập nhật cũng nạp 24 khóa tham khảo cùng 3 hồ sơ giáo viên PREP.</p><ol><li>Sao lưu database hiện tại trong phpMyAdmin.</li><li>Tải file SQL bên dưới.</li><li>Trong phpMyAdmin, chọn đúng database đang dùng rồi Import file vừa tải.</li><li>Quay lại trang này và tải lại để bắt đầu chỉnh sửa nội dung.</li></ol><a class="button" href="{{ route('website.upgrade') }}">Tải CAP_NHAT_WEBSITE_41_BANG.sql</a><p class="small-source">File giữ nguyên dữ liệu, mật khẩu và nội dung đã chỉnh sửa. Không cần xóa bảng hay tạo database mới.</p></div></section>
@endsection
