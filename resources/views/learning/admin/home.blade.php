@extends('layouts.admin')

@section('title', 'Quản lý Banner & Trang chủ — English Center')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-image-fill text-primary"></i> Quản lý Banner & Trang Chủ</h3>
        <p class="text-secondary mb-0 fs-7">Cập nhật tiêu đề, mô tả và chọn các ảnh banner thật chất lượng cao hiển thị ngoài trang chủ.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Xem trang chủ ngoài website
        </a>
    </div>
</div>

<form class="portal-form admin-form" method="POST" action="{{ route('website.home.update') }}" enctype="multipart/form-data" style="max-width: 950px;">
    @csrf
    @method('PATCH')

    {{-- KHỐI 1: CHỌN NHANH BANNER ẢNH THẬT CHẤT LƯỢNG CAO --}}
    <div class="card border rounded-3 p-3 mb-4 bg-light shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="text-dark fs-6"><i class="bi bi-images text-primary me-1"></i> Chọn Nhanh Banner Ảnh Thật Có Sẵn</strong>
            <span class="badge bg-primary fs-8">Bộ sưu tập ảnh thật</span>
        </div>
        <p class="text-muted fs-8 mb-3">Click vào bất kỳ ảnh thật nào bên dưới để đặt làm banner trang chủ ngay lập tức:</p>
        
        <input type="hidden" name="preset_image" id="selectedPresetImage" value="">

        <div class="row g-3">
            @php
                $presets = [
                    ['path' => 'images/banners/banner-home-hero.jpg', 'title' => 'Học viên luyện thi', 'tag' => 'Tiêu chuẩn'],
                    ['path' => 'images/banners/banner-ielts-real.jpg', 'title' => 'IELTS Intensive 7.5+', 'tag' => 'IELTS Chuyên Sâu'],
                    ['path' => 'images/banners/banner-toeic-real.jpg', 'title' => 'TOEIC 850+ & Giao tiếp', 'tag' => 'TOEIC Thực chiến'],
                    ['path' => 'images/banners/banner-online-real.jpg', 'title' => 'E-Learning Máy tính', 'tag' => 'Học Online 24/7'],
                    ['path' => 'images/banners/banner-speaking-real.jpg', 'title' => 'Phản xạ Speaking 1-on-1', 'tag' => 'Luyện Nói'],
                ];
            @endphp

            @foreach($presets as $p)
                @php($isSelected = ($home['image'] === $p['path']))
                <div class="col-6 col-md-4 col-lg">
                    <div class="preset-banner-card border rounded-3 overflow-hidden bg-white position-relative {{ $isSelected ? 'border-primary border-3 shadow' : '' }}" 
                         onclick="selectPresetBanner('{{ $p['path'] }}', this)"
                         style="cursor: pointer; transition: all 0.2s;">
                        <img src="{{ asset($p['path']) }}" alt="{{ $p['title'] }}" style="width: 100%; height: 95px; object-fit: cover; display: block;">
                        <div class="p-2 text-center">
                            <span class="badge bg-light text-dark border fs-8 mb-1 d-inline-block">{{ $p['tag'] }}</span>
                            <div class="text-dark fw-bold fs-8 text-truncate">{{ $p['title'] }}</div>
                        </div>
                        <div class="preset-check position-absolute top-0 end-0 m-1 {{ $isSelected ? '' : 'd-none' }}">
                            <span class="badge bg-primary rounded-circle"><i class="bi bi-check-lg"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- KHỐI 2: BANNER HIỆN TẠI & TẢI ẢNH MỚI --}}
    <div class="card border rounded-3 p-3 mb-4 bg-white shadow-sm">
        <label class="fw-bold mb-2">Ảnh banner chính hiện tại:</label>
        <div class="mb-3">
            <img class="admin-banner-preview border rounded-3 shadow-sm" id="bannerLivePreview" src="{{ asset($home['image']) }}" alt="Banner hiện tại" style="width: 100%; max-width: 600px; height: 200px; object-fit: cover; display: block;">
        </div>
        <label for="image" class="fw-bold fs-7 mb-1">Hoặc tải lên ảnh banner mới từ máy tính (JPG, PNG, WebP — tối đa 4 MB):</label>
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" onchange="previewUploadImage(this)">
    </div>

    {{-- KHỐI 3: TIÊU ĐỀ & MÔ TẢ --}}
    <div class="card border rounded-3 p-3 mb-4 bg-white shadow-sm">
        <h5 class="fw-bold text-dark fs-6 mb-3 border-bottom pb-2">Nội dung chữ hiển thị trên Banner</h5>
        @foreach(['title'=>'Tiêu đề banner chính','description'=>'Mô tả banner chính','teacher_title'=>'Tiêu đề khu vực giáo viên','teacher_description'=>'Mô tả khu vực giáo viên'] as $key=>$label)
            <div class="mb-3">
                <label for="{{ $key }}" class="fw-bold text-secondary fs-7 mb-1">{{ $label }}</label>
                <textarea id="{{ $key }}" name="{{ $key }}" required rows="2" class="form-control">{{ old($key,$home[$key]) }}</textarea>
            </div>
        @endforeach
    </div>

    <div class="form-actions d-flex gap-2">
        <button class="button btn btn-primary px-4 py-2 fw-bold" type="submit">Lưu Cài Đặt Banner</button>
        <a class="button button-outline btn btn-outline-secondary px-3 py-2" href="{{ route('admin.dashboard') }}">Về Dashboard</a>
    </div>
</form>

<script>
    function selectPresetBanner(path, cardEl) {
        document.getElementById('selectedPresetImage').value = path;
        document.getElementById('bannerLivePreview').src = '{{ asset('') }}' + path;
        
        document.querySelectorAll('.preset-banner-card').forEach(c => {
            c.classList.remove('border-primary', 'border-3', 'shadow');
            c.querySelector('.preset-check')?.classList.add('d-none');
        });
        
        cardEl.classList.add('border-primary', 'border-3', 'shadow');
        cardEl.querySelector('.preset-check')?.classList.remove('d-none');
    }

    function previewUploadImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('bannerLivePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
