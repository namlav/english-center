@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', $teacher->exists ? 'Sửa Giảng Viên & Banner Website' : 'Thêm Giảng Viên Mới & Banner Website')

@section('content')

@php($isEdit = $teacher->exists)
@php($pt = $publicTeacher ?? null)
@php($currentPhoto = $pt && $pt->photo_path ? asset($pt->photo_path) : asset('images/teachers/prep-33.png'))

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-person-badge-fill text-primary me-2"></i>{{ $isEdit ? 'Chỉnh Sửa Hồ Sơ Giảng Viên' : 'Thêm Giảng Viên Mới' }}
        </h3>
        <p class="text-secondary mb-0 fs-7">
            Hồ sơ tài khoản giảng dạy, thông tin chuyên môn đào tạo và ảnh chân dung hiển thị trên Banner Website.
        </p>
    </div>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Về danh sách
    </a>
</div>

<form method="POST" action="{{ $isEdit ? route('teachers.update', $teacher) : route('teachers.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-4">
        {{-- CỘT TRÁI (8 COLS): THÔNG TIN TÀI KHOẢN & HỒ SƠ ĐÀO TẠO --}}
        <div class="col-lg-8">
            {{-- THÔNG TIN CƠ BẢN --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <strong class="text-dark fs-7"><i class="bi bi-person-fill text-primary me-1"></i> 1. Thông Tin Tài Khoản & Định Danh</strong>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="name" class="form-label fw-semibold fs-7">Họ và tên giảng viên <span class="text-danger">*</span></label>
                            <input id="name" name="name" class="form-control" maxlength="255" 
                                   placeholder="Ví dụ: Thầy Jackson Howard, Cô Trang Trương..."
                                   value="{{ old('name', $teacher->user?->name ?? $pt?->name) }}" required>
                        </div>
                        <div class="col-md-5">
                            <label for="teacher_code" class="form-label fw-semibold fs-7">Mã giảng viên <span class="text-danger">*</span></label>
                            <input id="teacher_code" name="teacher_code" class="form-control font-monospace" maxlength="30" 
                                   placeholder="Ví dụ: GV004"
                                   value="{{ old('teacher_code', $teacher->teacher_code ?? 'GV' . sprintf('%03d', ((int)\App\Models\Teacher::max('id')) + 1)) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold fs-7">Email tài khoản <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control" maxlength="255" 
                                   placeholder="Ví dụ: gv.howard@englishcenter.vn"
                                   value="{{ old('email', $teacher->user?->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold fs-7">Số điện thoại liên hệ</label>
                            <input id="phone" name="phone" class="form-control" maxlength="20" 
                                   placeholder="Ví dụ: 0912 345 678"
                                   value="{{ old('phone', $teacher->user?->phone) }}">
                        </div>

                        @unless($isEdit)
                            <div class="col-md-12">
                                <label for="password" class="form-label fw-semibold fs-7">Mật khẩu đăng nhập <span class="text-danger">*</span></label>
                                <input id="password" name="password" type="password" class="form-control" minlength="6" 
                                       placeholder="Tối thiểu 6 ký tự (mặc định có thể nhập 123456)" required>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>

            {{-- CHUYÊN MÔN & BẰNG CẤP HỌC THUẬT --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <strong class="text-dark fs-7"><i class="bi bi-award-fill text-primary me-1"></i> 2. Chuyên Môn & Trình Độ Học Thuật</strong>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="specialization" class="form-label fw-semibold fs-7">Chuyên môn giảng dạy <span class="text-danger">*</span></label>
                            <input id="specialization" name="specialization" class="form-control" maxlength="255" 
                                   placeholder="Ví dụ: IELTS Academic Master, TOEIC 900+..."
                                   value="{{ old('specialization', $teacher->specialization) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="degree" class="form-label fw-semibold fs-7">Bằng cấp / Trình độ cao nhất</label>
                            <input id="degree" name="degree" class="form-control" maxlength="255" 
                                   placeholder="Ví dụ: Thạc sĩ TESOL UCL, 8.5 IELTS..."
                                   value="{{ old('degree', $teacher->degree) }}">
                        </div>

                        <div class="col-12">
                            <label for="provider" class="form-label fw-semibold fs-7">Đơn vị đào tạo / Bằng cấp nổi bật (Hiển thị Banner)</label>
                            <input id="provider" name="provider" class="form-control" maxlength="100" 
                                   placeholder="Ví dụ: Đại học Nottingham (Anh Quốc), PREP, ĐH Sư Phạm..."
                                   value="{{ old('provider', $pt?->provider ?? $teacher->degree ?? 'English Center') }}">
                        </div>

                        <div class="col-12">
                            <label for="bio" class="form-label fw-semibold fs-7">Tiểu sử & Giới thiệu bản thân</label>
                            <textarea id="bio" name="bio" class="form-control" rows="4" 
                                      placeholder="Mô tả kinh nghiệm giảng dạy, thành tích học thuật và phong cách sư phạm...">{{ old('bio', $teacher->bio ?? $pt?->bio) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI (4 COLS): ẢNH THẬT & CẤU HÌNH BANNER WEBSITE --}}
        <div class="col-lg-4">
            {{-- CARD ẢNH CHÂN DUNG --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <strong class="text-dark fs-7"><i class="bi bi-image text-success me-1"></i> Ảnh Chân Dung Thật</strong>
                    <small class="text-muted d-block fs-8">Ảnh đại diện hiển thị trên Banner website & hệ thống.</small>
                </div>
                <div class="card-body p-3 text-center">
                    {{-- KHUNG XEM TRƯỚC ẢNH --}}
                    <div class="mb-3 position-relative d-inline-block">
                        <img id="imagePreview" src="{{ $currentPhoto }}" alt="Avatar Preview" 
                             class="rounded-3 border shadow-sm" 
                             style="width: 140px; height: 140px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 badge bg-primary rounded-pill p-2" title="Ảnh thật">
                            <i class="bi bi-camera-fill"></i>
                        </span>
                    </div>

                    {{-- NÚT UPLOAD ẢNH --}}
                    <div class="mb-3 text-start">
                        <label for="teacherImage" class="form-label fs-8 fw-semibold text-secondary">Tải ảnh mới từ máy tính</label>
                        <input type="file" name="image" id="teacherImage" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted fs-8 d-block mt-1">Định dạng JPG, PNG, WEBP tối đa 4MB.</small>
                    </div>

                    {{-- HOẶC CHỌN ẢNH MẪU CÓ SẴN --}}
                    <div class="text-start border-top pt-2">
                        <label class="form-label fs-8 fw-semibold text-secondary mb-2">Hoặc chọn nhanh ảnh mẫu có sẵn:</label>
                        <div class="d-flex gap-2 justify-content-center">
                            @foreach(['images/teachers/prep-33.png' => 'Mẫu 1', 'images/teachers/prep-12.png' => 'Mẫu 2', 'images/teachers/prep-5.png' => 'Mẫu 3'] as $path => $lbl)
                                <label class="border rounded-2 p-1 text-center cursor-pointer preset-photo-card" style="cursor: pointer; width: 65px;">
                                    <input type="radio" name="preset_image" value="{{ $path }}" class="d-none" onchange="document.getElementById('imagePreview').src = '{{ asset($path) }}'">
                                    <img src="{{ asset($path) }}" class="rounded-1 mb-1" style="width: 48px; height: 48px; object-fit: cover;">
                                    <span class="d-block fs-8 text-secondary">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD CẤU HÌNH BANNER TRANG CHỦ --}}
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-warning">
                <div class="card-header bg-white py-3 border-bottom">
                    <strong class="text-dark fs-7"><i class="bi bi-stars text-warning me-1"></i> Cấu Hình Banner Trang Chủ</strong>
                </div>
                <div class="card-body p-3">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="show_on_banner" name="show_on_banner" value="1" 
                               {{ old('show_on_banner', $pt ? $pt->is_active : true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark fs-7" for="show_on_banner">
                            Hiển thị trên Banner Website
                        </label>
                        <small class="text-muted d-block fs-8 mt-1">
                            Giáo viên này sẽ tự động xuất hiện trên băng chuyền xoay ảnh thật (Teacher Showcase) ngoài trang chủ.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label fw-semibold fs-8 text-secondary">Thứ tự hiển thị trên Banner (#)</label>
                        <input type="number" id="position" name="position" class="form-control form-control-sm font-monospace" min="1" max="99" 
                               value="{{ old('position', $pt?->position ?? (\App\Models\PublicTeacher::max('position') + 1)) }}">
                        <small class="text-muted fs-8">Số nhỏ hơn sẽ được ưu tiên hiển thị trước.</small>
                    </div>

                    <div class="p-2 bg-light rounded-2 border fs-8 text-secondary">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        Đã liên kết tự động giữa tài khoản giảng dạy và băng chuyền banner trang chủ.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NÚT THAO TÁC CUỐI TRANG --}}
    <div class="border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
        <a class="btn btn-outline-secondary px-4" href="{{ route('teachers.index') }}">
            <i class="bi bi-x-circle me-1"></i> Hủy
        </a>
        <button class="btn btn-primary px-4 fw-bold shadow-sm" type="submit">
            <i class="bi bi-check-circle-fill me-1"></i> {{ $isEdit ? 'Lưu Thay Đổi & Cập Nhật Banner' : 'Thêm Giảng Viên & Hiện Lên Banner' }}
        </button>
    </div>
</form>

<script>
    // Preview ảnh khi chọn file
    document.getElementById('teacherImage')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('imagePreview').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection
