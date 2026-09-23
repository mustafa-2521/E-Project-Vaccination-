<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    return back()
        ->withInput($request->only('email'))
        ->withErrors([
            'email' => 'The email or password is incorrect.',
        ]);
}
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(Request $r)
    {
        $d = $r->validate(['name' => 'required|max:100', 'email' => 'required|email|unique:users', 'password' => 'required|min:6|confirmed', 'phone' => 'nullable|max:30', 'address' => 'nullable|max:255']);
        $d['role'] = 'parent';
        $u = User::create($d);
        Auth::login($u);
        return redirect()->route('parent.dashboard');
    }
    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
    public function dashboard()
    {
        return redirect()->route(Auth::user()->role . '.dashboard');
    }
}
