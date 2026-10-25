@extends('layouts.learning')
@section('title',$teacher->name.' — English Center')
@section('content')
<section class="shell teacher-profile"><a class="text-link" href="{{ route('home') }}#teachers">← Đội ngũ giáo viên</a><div class="teacher-profile-grid"><div class="profile-portrait"><img src="{{ asset($teacher->photo_path) }}" alt="{{ $teacher->name }}" width="384" height="498"></div><div><span class="eyebrow">HỒ SƠ GIÁO VIÊN · {{ $teacher->provider }}</span><h1>{{ $teacher->name }}</h1><p>{{ $teacher->bio }}</p><h2>Chuyên môn và đào tạo</h2><ul>@foreach($teacher->qualifications??[] as $item)<li>{{ $item }}</li>@endforeach</ul>@if($teacher->source_url)<p class="small-source">Hồ sơ tham khảo từ {{ $teacher->provider }}. Các chức danh được trình bày theo thông tin tại nguồn.</p><a class="button" href="{{ $teacher->source_url }}" target="_blank" rel="noopener noreferrer">Xem hồ sơ tại {{ $teacher->provider }} ↗</a>@endif</div></div></section>
@endsection
