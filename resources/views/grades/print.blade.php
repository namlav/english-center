<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điểm lớp {{ $classRoom->code }} — English Center</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            margin: 0;
            padding: 2rem;
            line-height: 1.5;
        }
        .sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            font-size: 0.85rem;
            color: #64748b;
        }
        .doc-title {
            text-align: center;
            margin: 1.5rem 0;
        }
        .doc-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
            color: #0f172a;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem 2rem;
            margin-bottom: 1.5rem;
            background: #f1f5f9;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            font-size: 0.925rem;
        }
        .meta-grid div strong {
            color: #334155;
            display: inline-block;
            min-width: 130px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.75rem;
            text-align: left;
        }
        th {
            background-color: #e2e8f0;
            font-weight: 600;
            color: #1e293b;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-passed { background: #dcfce7; color: #15803d; }
        .badge-failed { background: #fee2e2; color: #b91c1c; }
        .badge-studying { background: #e0f2fe; color: #0369a1; }
        .signature-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            text-align: center;
            margin-top: 3rem;
            page-break-inside: avoid;
        }
        .signature-title {
            font-weight: 600;
            margin-bottom: 5rem;
        }
        .action-bar {
            max-width: 900px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-primary { background: #2563eb; color: #ffffff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .sheet { box-shadow: none; padding: 0; max-width: 100%; }
            .action-bar { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <a href="{{ route('grades.index', $classRoom) }}" class="btn btn-secondary">← Quay lại danh sách điểm</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ In bảng điểm / Lưu PDF</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <div class="brand-name">TRUNG TÂM NGOẠI NGỮ ENGLISH CENTER</div>
                <div class="brand-sub">Học từng ngày, vững từng bước</div>
            </div>
            <div style="text-align: right; font-size: 0.85rem; color: #64748b;">
                <div>Ngày in: {{ date('d/m/Y H:i') }}</div>
                <div>Mã văn bản: BD-{{ $classRoom->code }}</div>
            </div>
        </div>

        <div class="doc-title">
            <h1>BẢNG ĐIỂM HỌC VIÊN</h1>
            <div style="color: #64748b; font-size: 0.95rem;">Khóa học: {{ $classRoom->course->name }} ({{ $classRoom->course->code }})</div>
        </div>

        <div class="meta-grid">
            <div><strong>Mã lớp học:</strong> {{ $classRoom->code }}</div>
            <div><strong>Giảng viên:</strong> {{ $classRoom->teacher->user->name ?? 'Chưa phân công' }}</div>
            <div><strong>Tên lớp học:</strong> {{ $classRoom->name }}</div>
            <div><strong>Lịch học:</strong> {{ $classRoom->schedule ?? 'Chưa có lịch' }}</div>
            <div><strong>Sĩ số lớp:</strong> {{ $classRoom->enrollments->count() }} học viên</div>
            <div><strong>Trạng thái lớp:</strong> {{ match($classRoom->status) { 'ongoing' => 'Đang học', 'upcoming' => 'Sắp mở', 'completed' => 'Đã kết thúc', default => $classRoom->status } }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 45px;">STT</th>
                    <th style="width: 110px;">Mã HV</th>
                    <th>Họ và tên</th>
                    <th class="text-center" style="width: 80px;">Giữa kỳ</th>
                    <th class="text-center" style="width: 80px;">Cuối kỳ</th>
                    <th class="text-center" style="width: 80px;">Điểm TB</th>
                    <th class="text-center" style="width: 100px;">Kết quả</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @php($i = 1)
                @forelse($classRoom->enrollments as $enrollment)
                    @php($student = $enrollment->student)
                    @php($mid = $enrollment->midterm_score !== null ? (float)$enrollment->midterm_score : null)
                    @php($fin = $enrollment->final_score !== null ? (float)$enrollment->final_score : null)
                    @php($avg = ($mid !== null && $fin !== null) ? round(($mid + $fin) / 2, 2) : null)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td><strong>{{ $student->student_code }}</strong></td>
                        <td>{{ $student->user->name }}</td>
                        <td class="text-center">{{ $mid !== null ? number_format($mid, 1) : '-' }}</td>
                        <td class="text-center">{{ $fin !== null ? number_format($fin, 1) : '-' }}</td>
                        <td class="text-center"><strong>{{ $avg !== null ? number_format($avg, 1) : '-' }}</strong></td>
                        <td class="text-center">
                            @if($enrollment->result_status === 'passed')
                                <span class="badge badge-passed">Đạt</span>
                            @elseif($enrollment->result_status === 'failed')
                                <span class="badge badge-failed">Chưa đạt</span>
                            @else
                                <span class="badge badge-studying">Đang học</span>
                            @endif
                        </td>
                        <td>{{ $enrollment->remarks ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem; color: #94a3b8;">Lớp chưa có học viên đăng ký.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <div>
                <div class="signature-title">GIẢNG VIÊN PHỤ TRÁCH<br><span style="font-size:0.8rem; font-weight:normal; color:#64748b;">(Ký và ghi rõ họ tên)</span></div>
                <div><strong>{{ $classRoom->teacher->user->name ?? '' }}</strong></div>
            </div>
            <div>
                <div class="signature-title">PHÒNG ĐÀO TẠO & HỌC VỤ<br><span style="font-size:0.8rem; font-weight:normal; color:#64748b;">(Ký và đóng dấu)</span></div>
                <div><strong>ENGLISH CENTER</strong></div>
            </div>
        </div>
    </div>
</body>
</html>
