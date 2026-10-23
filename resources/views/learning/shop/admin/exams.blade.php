@extends('layouts.admin')
@section('title', 'Quản lý Ngân hàng Đề thi thử Online')
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">KHẢO THÍ & ĐỀ THI TRỰC TUYẾN</span>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Ngân hàng Đề thi thử Online</h1>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0;">Quản lý bộ đề thi trắc nghiệm TOEIC, IELTS, Đọc - Nghe tự động chấm điểm.</p>
        </div>
        <a class="button" href="{{ route('exam_admin.create') }}" style="font-weight: 700; padding: 10px 20px; text-decoration: none;">+ Thêm đề thi mới</a>
    </div>

    {{-- Thống kê nhanh --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Tổng số bộ đề</span>
            <div style="font-size: 26px; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $papers->total() ?? $papers->count() }} bộ đề</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Tổng số câu hỏi</span>
            <div style="font-size: 26px; font-weight: 800; color: #059669; margin-top: 4px;">{{ number_format($papers->sum('questions_count'), 0, ',', '.') }} câu hỏi</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Đã công bố</span>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $papers->where('status', 'published')->count() }} đề</div>
        </div>
    </div>

    {{-- Ô tìm kiếm nhanh --}}
    <div style="margin-bottom: 18px; display: flex; gap: 12px; align-items: center;">
        <input type="text" id="examSearchInput" onkeyup="filterExamTable()" placeholder="🔍 Lọc theo tên đề thi hoặc kỹ năng..." style="padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; max-width: 450px;">
        <span style="font-size: 13px; color: #64748b;" id="examCountNotice">Hiển thị {{ $papers->count() }} đề thi</span>
    </div>

    <div class="admin-table-wrap" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 10px;">
        <table class="admin-table" id="examsAdminTable">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 45%;">Tên đề thi & Danh mục</th>
                    <th>Kỹ năng</th>
                    <th style="text-align: center;">Thời gian</th>
                    <th style="text-align: center;">Số câu hỏi</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($papers as $i => $paper)
                    @php
                        $skillColors = [
                            'reading' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'label' => 'Đọc hiểu'],
                            'listening' => ['bg' => '#f5f3ff', 'color' => '#6d28d9', 'label' => 'Nghe hiểu'],
                            'writing' => ['bg' => '#fff7ed', 'color' => '#c2410c', 'label' => 'Viết'],
                            'speaking' => ['bg' => '#ecfdf5', 'color' => '#047857', 'label' => 'Nói'],
                            'mixed' => ['bg' => '#f1f5f9', 'color' => '#334155', 'label' => 'Tổng hợp'],
                        ];
                        $sc = $skillColors[$paper->skill] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => ucfirst($paper->skill)];
                    @endphp
                    <tr class="exam-row">
                        <td style="color: #94a3b8; font-weight: 600;">{{ $i + 1 }}</td>
                        <td>
                            <strong style="color: #0f172a; font-size: 14px; display: block;" class="exam-title-text">{{ $paper->title }}</strong>
                            <span style="font-size: 12px; color: #64748b;">
                                {{ $paper->learningCategory?->name ?? 'Tổng quát' }} · Slug: {{ $paper->slug }}
                            </span>
                        </td>
                        <td>
                            <span style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; font-size: 12px; font-weight: 700; padding: 3px 9px; border-radius: 4px;" class="exam-skill-text">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td style="text-align: center; color: #475569; font-weight: 600;">
                            {{ (int)ceil($paper->duration_seconds / 60) }} phút
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #ecfdf5; color: #047857; font-size: 12.5px; font-weight: 700; padding: 3px 9px; border-radius: 20px;">
                                {{ $paper->questions_count }} câu
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($paper->status === 'published')
                                <span style="background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 6px;">
                                    Đã công bố
                                </span>
                            @elseif($paper->status === 'draft')
                                <span style="background: #f1f5f9; color: #64748b; font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 6px;">
                                    Bản nháp
                                </span>
                            @else
                                <span style="background: #fef2f2; color: #991b1b; font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 6px;">
                                    Lưu trữ
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('practice.show', $paper->slug) }}" target="_blank" class="button button-outline button-small" style="padding: 5px 10px; font-size: 12px; margin-right: 6px; text-decoration: none;" title="Làm thử ngoài web">
                                👁️ Làm thử
                            </a>
                            <a href="{{ route('exam_admin.edit', $paper) }}" class="button button-small" style="padding: 5px 12px; font-size: 12px; text-decoration: none;">
                                ✏️ Soạn đề & Câu hỏi
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('learning.shop.pager', ['records' => $papers])
</section>

<script>
function filterExamTable() {
    const input = document.getElementById('examSearchInput');
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#examsAdminTable tbody tr.exam-row');
    let count = 0;

    rows.forEach(row => {
        const title = row.querySelector('.exam-title-text')?.innerText.toLowerCase() || '';
        const skill = row.querySelector('.exam-skill-text')?.innerText.toLowerCase() || '';
        if (title.includes(filter) || skill.includes(filter)) {
            row.style.display = '';
            count++;
        } else {
            row.style.display = 'none';
        }
    });

    const notice = document.getElementById('examCountNotice');
    if (notice) {
        notice.innerText = 'Hiển thị ' + count + ' đề thi';
    }
}
</script>
@endsection
