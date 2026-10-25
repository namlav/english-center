<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Tạo tài khoản thành công.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ];

        // Nếu là tài khoản Admin, bảo vệ nghiêm ngặt: không cho phép thay đổi vai trò
        if ($user->isAdmin()) {
            $rules['role'] = ['required', Rule::in(['admin'])];
        }

        $validated = $request->validate($rules, [
            'role.in' => 'Không thể thay đổi vai trò của tài khoản Quản trị viên hệ thống.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Tài khoản Admin luôn duy trì trạng thái hoạt động (không thể khóa)
        if ($user->isAdmin()) {
            $validated['is_active'] = true;
            $validated['role'] = 'admin';
        } else {
            $validated['is_active'] = $request->has('is_active');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        // Chặn tuyệt đối xóa tài khoản Quản trị viên (Admin)
        if ($user->isAdmin()) {
            return redirect()->route('users.index')->with('error', 'Không thể xóa tài khoản Quản trị viên tối cao của hệ thống để tránh mất quyền điều hành.');
        }

        return DB::transaction(function () use ($user) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            if ((Schema::hasTable('orders') && DB::table('orders')->where('user_id',$user->id)->exists())
                || (Schema::hasTable('payments') && DB::table('payments')->where('confirmed_by',$user->id)->exists())) {
                return redirect()->route('users.index')->with('error','Tài khoản có đơn hàng hoặc lịch sử xác nhận thanh toán mô phỏng. Hãy khóa tài khoản để giữ lịch sử.');
            }
            foreach (['courseEnrollments', 'lessonProgress', 'examAttempts', 'authoredExamPapers', 'flashcardDecks', 'flashcardReviews', 'courseReviews', 'examComments', 'courseLearningProfiles', 'taughtOnlineCourses'] as $relation) {
                if ($user->{$relation}()->exists()) {
                    return redirect()->route('users.index')->with('error', 'Tài khoản có nội dung hoặc lịch sử học online. Hãy khóa tài khoản để giữ dữ liệu.');
                }
            }
            $user->delete();

            return redirect()->route('users.index')->with('success', 'Đã xóa tài khoản.');
        });
    }
}
