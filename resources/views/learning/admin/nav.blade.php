{{-- Thanh điều hướng Admin Dạng Cây (Tree Hierarchy Navigation) --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentUrl = request()->url();
@endphp

<div class="admin-tree-container" id="adminTreeNav">
    <div class="admin-tree-header">
        <div class="admin-tree-title">
            <span class="tree-icon">🌳</span>
            <div>
                <strong>CÂY DANH MỤC QUẢN TRỊ VIÊN</strong>
                <span class="tree-subtitle">Toàn bộ tính năng hệ thống — Đã tinh gọn các mục thừa</span>
            </div>
        </div>
        <div class="admin-tree-actions">
            <button type="button" class="tree-btn-sm" onclick="toggleAllTreeNodes(true)">Mở tất cả</button>
            <button type="button" class="tree-btn-sm" onclick="toggleAllTreeNodes(false)">Thu gọn</button>
            <button type="button" class="tree-btn-sm tree-btn-toggle" onclick="toggleTreePanel()">
                <span id="treeToggleLabel">Ẩn cây</span> ◬
            </button>
        </div>
    </div>

    <div class="admin-tree-body" id="adminTreeBody">
        <div class="admin-tree-grid">

            {{-- NHÁNH 1: WEBSITE & GIAO DIỆN (CMS) --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">🌐</span>
                    <span class="branch-title">1. Website & Giao Diện (CMS)</span>
                    <span class="badge-count">2 mục cốt lõi</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ str_starts_with($currentRoute, 'website.home') ? 'active' : '' }}">
                        <a href="{{ route('website.home.edit') }}">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Banner & Trang chủ</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'website.index' || str_starts_with($currentRoute, 'website.teachers') ? 'active' : '' }}">
                        <a href="{{ route('website.index') }}">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Đội ngũ Giáo viên Banner</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                </ul>
            </details>

            {{-- NHÁNH 2: KHÓA HỌC & BÀI HỌC (E-LEARNING LMS) --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">📚</span>
                    <span class="branch-title">2. Khóa Học & Giáo Trình (LMS)</span>
                    <span class="badge-count">3 mục cốt lõi</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ ($currentRoute === 'shop_admin.index' || str_starts_with($currentRoute, 'shop_admin.courses')) && !str_contains($currentUrl, '/lessons') ? 'active' : '' }}">
                        <a href="{{ route('shop_admin.index') }}">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Quản lý Khóa học E-learning</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ str_contains($currentUrl, '/lessons') ? 'active' : '' }}">
                        <a href="{{ route('shop_admin.index') }}" title="Chọn khóa học rồi vào Soạn bài học & Video YouTube">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Soạn Bài học & Video YouTube</span>
                            <span class="badge-tag tag-keep">6.392 bài</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'shop_admin.courses.create' ? 'active' : '' }}">
                        <a href="{{ route('shop_admin.courses.create') }}">
                            <span class="leaf-icon">➕</span>
                            <span class="leaf-name">Thêm Khóa học mới</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                </ul>
            </details>

            {{-- NHÁNH 3: KHẢO THÍ & ĐỀ THI TRỰC TUYẾN --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">📝</span>
                    <span class="branch-title">3. Khảo Thí & Đề Thi Online</span>
                    <span class="badge-count">2 mục</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ $currentRoute === 'exam_admin.index' || ($currentRoute === 'exam_admin.edit' && !str_contains($currentUrl, '/questions')) ? 'active' : '' }}">
                        <a href="{{ route('exam_admin.index') }}">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Ngân hàng Đề thi thử (IELTS/TOEIC)</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ str_contains($currentUrl, '/questions') ? 'active' : '' }}">
                        <a href="{{ route('exam_admin.index') }}" title="Vào chi tiết đề thi để soạn câu hỏi">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Soạn Câu hỏi & Đáp án A/B/C/D</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'exam_admin.create' ? 'active' : '' }}">
                        <a href="{{ route('exam_admin.create') }}">
                            <span class="leaf-icon">➕</span>
                            <span class="leaf-name">Tạo Đề thi mới</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                </ul>
            </details>

            {{-- NHÁNH 4: BÁN HÀNG, ĐƠN HÀNG & DOANH THU --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">💳</span>
                    <span class="branch-title">4. Đơn Hàng & Doanh Thu</span>
                    <span class="badge-count">3 mục</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ str_starts_with($currentRoute, 'shop_admin.orders') ? 'active' : '' }}">
                        <a href="{{ route('shop_admin.orders.index') }}">
                            <span class="leaf-icon">📄</span>
                            <span class="leaf-name">Quản lý Đơn hàng & Duyệt mua</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ str_starts_with($currentRoute, 'shop_admin.bank') ? 'active' : '' }}">
                        <a href="{{ route('shop_admin.bank.edit') }}">
                            <span class="leaf-icon">⚙️</span>
                            <span class="leaf-name">Cấu hình VietQR & Ngân hàng</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ str_starts_with($currentRoute, 'reports.') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}">
                            <span class="leaf-icon">📊</span>
                            <span class="leaf-name">Thống kê & Báo cáo Doanh thu</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                </ul>
            </details>

            {{-- NHÁNH 5: HỆ THỐNG & TÀI KHOẢN --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">👥</span>
                    <span class="branch-title">5. Hệ Thống & Tài Khoản</span>
                    <span class="badge-count">6 mục cốt lõi</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ $currentRoute === 'users.index' ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <span class="leaf-icon">👤</span>
                            <span class="leaf-name">Danh sách Tài khoản</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'users.create' ? 'active' : '' }}">
                        <a href="{{ route('users.create') }}">
                            <span class="leaf-icon">➕</span>
                            <span class="leaf-name">Thêm Tài khoản mới</span>
                            <span class="badge-tag tag-keep">Mới cập nhật</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'students.index' ? 'active' : '' }}">
                        <a href="{{ route('students.index') }}">
                            <span class="leaf-icon">🎓</span>
                            <span class="leaf-name">Danh sách Học viên</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'students.create' ? 'active' : '' }}">
                        <a href="{{ route('students.create') }}">
                            <span class="leaf-icon">➕</span>
                            <span class="leaf-name">Thêm Học viên mới</span>
                            <span class="badge-tag tag-keep">Mới cập nhật</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'teachers.index' ? 'active' : '' }}">
                        <a href="{{ route('teachers.index') }}">
                            <span class="leaf-icon">👨‍🏫</span>
                            <span class="leaf-name">Danh sách Giảng viên</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                    <li class="tree-leaf {{ $currentRoute === 'teachers.create' ? 'active' : '' }}">
                        <a href="{{ route('teachers.create') }}">
                            <span class="leaf-icon">➕</span>
                            <span class="leaf-name">Thêm Giảng viên mới</span>
                            <span class="badge-tag tag-keep">Mới cập nhật</span>
                        </a>
                    </li>
                </ul>
            </details>

            {{-- NHÁNH 6: QUẢN TRỊ TRUNG TÂM & LỚP HỌC --}}
            <details class="tree-node" open>
                <summary class="tree-branch-header">
                    <span class="branch-icon">🏫</span>
                    <span class="branch-title">6. Trung Tâm & Lớp Học</span>
                    <span class="badge-count">1 mục cốt lõi</span>
                </summary>
                <ul class="tree-leaf-list">
                    <li class="tree-leaf {{ str_starts_with($currentRoute, 'class_rooms.') ? 'active' : '' }}">
                        <a href="{{ route('class_rooms.index') }}">
                            <span class="leaf-icon">🏛️</span>
                            <span class="leaf-name">Quản lý Lớp học & Phòng học</span>
                            <span class="badge-tag tag-keep">Cốt lõi</span>
                        </a>
                    </li>
                </ul>
            </details>

        </div>
    </div>
</div>

<style>
.admin-tree-container {
    background: #ffffff;
    border: 1px solid #dbe3ef;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    margin-bottom: 26px;
    overflow: hidden;
    font-family: inherit;
}
.admin-tree-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #f8fafc;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.admin-tree-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.tree-icon {
    font-size: 20px;
}
.admin-tree-title strong {
    font-size: 14px;
    letter-spacing: 0.03em;
    color: #ffffff;
    display: block;
}
.tree-subtitle {
    font-size: 12px;
    color: #94a3b8;
}
.admin-tree-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.tree-btn-sm {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.tree-btn-sm:hover {
    background: rgba(255, 255, 255, 0.24);
    color: #ffffff;
}
.tree-btn-toggle {
    background: #2563eb;
    border-color: #3b82f6;
}
.admin-tree-body {
    padding: 16px;
    background: #f8fafc;
}
.admin-tree-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 14px;
}
.tree-node {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.15s;
}
.tree-node[open] {
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}
.tree-branch-header {
    padding: 10px 14px;
    font-weight: 700;
    font-size: 13px;
    color: #1e293b;
    background: #f1f5f9;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
}
.tree-branch-header:hover {
    background: #e2e8f0;
}
.branch-title {
    flex-grow: 1;
}
.badge-count {
    font-size: 10px;
    font-weight: 600;
    background: #e2e8f0;
    color: #475569;
    padding: 2px 6px;
    border-radius: 10px;
}
.tree-leaf-list {
    list-style: none;
    margin: 0;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.tree-leaf a, .tree-leaf .leaf-disabled {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 7px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12.5px;
    color: #334155;
    transition: all 0.15s ease;
}
.tree-leaf a:hover {
    background: #eff6ff;
    color: #1d4ed8;
}
.tree-leaf.active a {
    background: #2563eb;
    color: #ffffff;
    font-weight: 700;
}
.tree-leaf.active .badge-tag {
    background: #ffffff;
    color: #1e40af;
}
.leaf-icon {
    font-size: 13px;
    flex-shrink: 0;
}
.leaf-name {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.badge-tag {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    flex-shrink: 0;
    letter-spacing: 0.02em;
}
.tag-keep {
    background: #dbeafe;
    color: #1e40af;
}
.tag-remove {
    background: #fee2e2;
    color: #b91c1c;
}
.tag-warn {
    background: #fef3c7;
    color: #b45309;
}
.tag-review {
    background: #ffedd5;
    color: #c2410c;
}
.text-strike {
    text-decoration: line-through;
    color: #94a3b8;
}
.text-review {
    color: #c2410c;
}
.leaf-disabled {
    cursor: not-allowed;
    opacity: 0.75;
}
@media (max-width: 768px) {
    .admin-tree-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function toggleAllTreeNodes(openState) {
    document.querySelectorAll('.admin-tree-container .tree-node').forEach(node => {
        node.open = openState;
    });
}
function toggleTreePanel() {
    const body = document.getElementById('adminTreeBody');
    const label = document.getElementById('treeToggleLabel');
    if (!body || !label) return;
    if (body.style.display === 'none') {
        body.style.display = 'block';
        label.innerText = 'Ẩn cây';
    } else {
        body.style.display = 'none';
        label.innerText = 'Hiện cây';
    }
}
</script>
