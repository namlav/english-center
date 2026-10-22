@extends('layouts.learning')
@section('title','Quản lý đề thi thử')
@section('content')
<section class="shell admin-page"><h1>Đề thi thử & câu hỏi</h1>@include('learning.admin.nav')<a class="button" href="{{ route('exam_admin.create') }}">Thêm đề thi thử</a><div class="admin-table-wrap" style="margin-top:25px"><table class="admin-table"><thead><tr><th>Đề thi</th><th>Câu hỏi</th><th>Trạng thái</th><th>Sửa</th></tr></thead><tbody>@foreach($papers as $paper)<tr><td>{{ $paper->title }}</td><td>{{ $paper->questions_count }}</td><td>{{ $paper->status }}</td><td><a href="{{ route('exam_admin.edit',$paper) }}">Soạn đề & đáp án</a></td></tr>@endforeach</tbody></table></div></section>
@include('learning.shop.pager',['records'=>$papers])
@endsection
