<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminProfileViewsTest extends TestCase
{
    use RefreshDatabase;

    public static function resources(): array
    {
        return [['students'], ['teachers'], ['users']];
    }

    #[DataProvider('resources')]
    public function test_admin_can_open_create_show_and_edit_pages(string $resource): void
    {
        $admin = User::factory()->admin()->create();
        $record = match ($resource) {
            'students' => Student::factory()->create(),
            'teachers' => Teacher::factory()->create(),
            default => User::factory()->create(),
        };

        $this->actingAs($admin)->get(route($resource.'.create'))->assertOk();
        $this->get(route($resource.'.show', $record))->assertOk();
        $this->get(route($resource.'.edit', $record))->assertOk();
    }
}
