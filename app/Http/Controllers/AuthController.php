<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('learning.login');
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
            if($courseId=$request->session()->pull('pending_cart_course')) {
                $shop=app(\App\Services\CourseShop::class);
                $course=$shop->available()->whereKey($courseId)->first();
                if($course&&!$shop->owned($user,$course->id)){
                    \Illuminate\Support\Facades\DB::table('cart_items')->insertOrIgnore(['user_id'=>$user->id,'course_id'=>$course->id,'created_at'=>now(),'updated_at'=>now()]);
                    $request->session()->put('checkout_token',(string)\Illuminate\Support\Str::uuid());
                    return redirect()->route('cart.index')->with('success','Đã thêm khóa học bạn chọn vào giỏ hàng.');
                }
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
