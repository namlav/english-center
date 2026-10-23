<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Student $student;
    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create(['name' => 'Giáo Viên Phụ Trách']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'teacher_code' => 'GV001',
            'specialization' => 'IELTS',
        ]);
        $course = Course::create([
            'code' => 'IELTS-01',
            'name' => 'Luyện thi IELTS',
            'duration_hours' => 60,
            'is_active' => true,
        ]);
        $classRoom = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'code' => 'IELTS-A01',
            'name' => 'Lớp IELTS A01',
            'start_date' => '2026-10-01',
            'schedule' => 'Thứ 2, 4, 6',
            'tuition_fee' => 5000000,
            'max_students' => 20,
            'status' => 'ongoing',
        ]);

        $studentUser = User::factory()->student()->create(['name' => 'Học Viên Thanh Toán']);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'student_code' => 'HV001',
        ]);
        $this->enrollment = Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $this->student->id,
            'enrollment_date' => '2026-09-25',
            'paid_tuition' => 5000000,
            'payment_status' => 'paid',
        ]);
    }

    public function test_admin_can_view_invoice_list_with_related_information(): void
    {
        $response = $this->actingAs($this->admin)->get(route('invoices.index'));

        $response->assertOk();
        $response->assertSee($this->enrollment->invoice_code);
        $response->assertSee('Học Viên Thanh Toán');
        $response->assertSee('Giáo Viên Phụ Trách');
        $response->assertSee('Lớp IELTS A01');
        $response->assertSee('Không có');
        $response->assertSee('5.000.000');
        $response->assertSee('data-live-search', false);
        $response->assertSee('data-search-form', false);
        $response->assertSee('data-search-results', false);
        $response->assertSee('member-one-search.js');
    }

    public function test_admin_can_view_invoice_detail(): void
    {
        $response = $this->actingAs($this->admin)->get(route('invoices.show', $this->enrollment));

        $response->assertOk();
        $response->assertSee($this->enrollment->invoice_code);
        $response->assertSee('HV001');
        $response->assertSee('GV001');
        $response->assertSee('IELTS-A01');
        $response->assertSee('Tổng tiền hóa đơn');
    }

    public function test_invoice_list_can_be_filtered_by_payment_status(): void
    {
        $otherStudent = Student::factory()->create();
        Enrollment::factory()->create([
            'class_room_id' => $this->enrollment->class_room_id,
            'student_id' => $otherStudent->id,
            'paid_tuition' => 0,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('invoices.index', ['status' => 'unpaid']));

        $response->assertOk();
        $response->assertSee($otherStudent->user->name);
        $response->assertDontSee('Học Viên Thanh Toán');
    }

    public function test_invoice_list_can_be_searched_by_invoice_code(): void
    {
        $response = $this->actingAs($this->admin)->get(route('invoices.index', ['keyword' => $this->enrollment->invoice_code]));

        $response->assertOk();
        $response->assertSee('Học Viên Thanh Toán');
        $response->assertSee($this->enrollment->invoice_code);
    }

    public function test_invoice_live_search_returns_json_partial_without_full_page(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('invoices.index', ['keyword' => 'Học Viên Thanh Toán']));

        $response->assertOk()->assertJsonPath('total', 1);
        $html = $response->json('html');
        $this->assertStringContainsString($this->enrollment->invoice_code, $html);
        $this->assertStringNotContainsString('<html', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_invoice_list_can_be_filtered_by_day_month_and_year(): void
    {
        $this->enrollment->forceFill(['created_at' => '2025-03-15 10:00:00'])->save();
        $other = $this->createInvoice('2024-11-02 09:00:00', 'Học Viên Khác', 2000000);

        $this->actingAs($this->admin)->get(route('invoices.index', ['year' => 2025]))
            ->assertOk()->assertSee('Học Viên Thanh Toán')->assertDontSee($other->invoice_code);

        $this->actingAs($this->admin)->get(route('invoices.index', ['month' => 11]))
            ->assertOk()->assertSee('Học Viên Khác')->assertDontSee($this->enrollment->invoice_code);

        $this->actingAs($this->admin)->get(route('invoices.index', ['day' => 15]))
            ->assertOk()->assertSee('Học Viên Thanh Toán')->assertDontSee($other->invoice_code);
    }

    public function test_invoice_list_can_be_sorted_by_date_and_total_amount(): void
    {
        $this->enrollment->forceFill(['created_at' => '2025-03-15 10:00:00'])->save();
        $other = $this->createInvoice('2026-01-01 09:00:00', 'Học Viên Mới', 2000000);

        $this->actingAs($this->admin)->get(route('invoices.index', ['sort' => 'oldest']))
            ->assertOk()->assertSeeInOrder([$this->enrollment->invoice_code, $other->invoice_code]);

        $this->actingAs($this->admin)->get(route('invoices.index', ['sort' => 'newest']))
            ->assertOk()->assertSeeInOrder([$other->invoice_code, $this->enrollment->invoice_code]);

        $this->actingAs($this->admin)->get(route('invoices.index', ['sort' => 'total_asc']))
            ->assertOk()->assertSeeInOrder([$other->invoice_code, $this->enrollment->invoice_code]);

        $this->actingAs($this->admin)->get(route('invoices.index', ['sort' => 'total_desc']))
            ->assertOk()->assertSeeInOrder([$this->enrollment->invoice_code, $other->invoice_code]);
    }

    public function test_default_pagination_uses_bootstrap_markup(): void
    {
        $html = (new LengthAwarePaginator(range(1, 20), 20, 15))->links()->toHtml();

        $this->assertStringContainsString('page-item', $html);
        $this->assertStringNotContainsString('<svg', $html);
    }

    public function test_students_cannot_manage_invoices(): void
    {
        $response = $this->actingAs($this->student->user)->get(route('invoices.index'));

        $response->assertForbidden();
    }

    private function createInvoice(string $createdAt, string $studentName, float $amount): Enrollment
    {
        $student = Student::factory()->create();
        $student->user->update(['name' => $studentName]);

        $invoice = Enrollment::create([
            'class_room_id' => $this->enrollment->class_room_id,
            'student_id' => $student->id,
            'enrollment_date' => '2026-09-25',
            'paid_tuition' => $amount,
            'payment_status' => 'paid',
        ]);
        $invoice->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        return $invoice;
    }
}
