<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('learning.login');
    }

    public function showRegisterForm()
    {
        return view('learning.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng trên hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role' => 'student',
                'is_active' => true,
            ]);

            $maxId = (int) Student::max('id');
            $code = sprintf('HV%04d', $maxId + 1);
            while (Student::where('student_code', $code)->exists()) {
                $maxId++;
                $code = sprintf('HV%04d', $maxId + 1);
            }

            Student::create([
                'user_id' => $user->id,
                'student_code' => $code,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        if ($courseId = $request->session()->pull('pending_cart_course')) {
            $shop = app(\App\Services\CourseShop::class);
            $course = $shop->available()->whereKey($courseId)->first();
            if ($course && !$shop->owned($user, $course->id)) {
                DB::table('cart_items')->insertOrIgnore([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $request->session()->put('checkout_token', (string) Str::uuid());
                return redirect()->route('cart.index')->with('success', 'Đăng ký thành công! Đã thêm khóa học bạn chọn vào giỏ hàng.');
            }
        }

        return redirect()->route('home')->with('success', 'Chào mừng ' . $user->name . '! Đăng ký tài khoản thành công.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Tài khoản của bạn đã bị khóa.');
            }

            $request->session()->regenerate();
            if (!$user->isAdmin()) {
                if($courseId=$request->session()->pull('pending_cart_course')) {
                    $shop=app(\App\Services\CourseShop::class);
                    $course=$shop->available()->whereKey($courseId)->first();
                    if($course&&!$shop->owned($user,$course->id)){
                        \Illuminate\Support\Facades\DB::table('cart_items')->insertOrIgnore(['user_id'=>$user->id,'course_id'=>$course->id,'created_at'=>now(),'updated_at'=>now()]);
                        $request->session()->put('checkout_token',(string)\Illuminate\Support\Str::uuid());
                        return redirect()->route('cart.index')->with('success','Đã thêm khóa học bạn chọn vào giỏ hàng.');
                    }
                }
            } else {
                $request->session()->forget('pending_cart_course');
                return redirect()->route('admin.dashboard')
                                 ->with('success', 'Đăng nhập thành công! Chào mừng Quản trị viên ' . $user->name);
            }
            return redirect()->intended(route('class_rooms.index'))
                             ->with('success', 'Đăng nhập thành công! Xin chào ' . $user->name);
        }

        return back()->withInput($request->only('email'))
                     ->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }
}
