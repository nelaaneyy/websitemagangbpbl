<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'kepala_desa' => redirect()->route('kepaladesa.index'),
                'instansi'    => redirect()->route('dinasesdm.index'),
                default       => redirect('/'),
            };
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = Auth::user();

        // Cek status verifikasi akun
        if ($user->status === 'pending') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda masih dalam proses verifikasi oleh pihak Instansi (Dinas ESDM).'])->onlyInput('email');
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            return back()->withErrors(['email' => 'Pendaftaran akun Anda ditolak oleh pihak Instansi (Dinas ESDM).'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return match ($user->role) {
            'kepala_desa' => redirect()->route('kepaladesa.index'),
            'instansi'    => redirect()->route('dinasesdm.index'),
            default       => redirect('/'),
        };
    }

    public function showRegisterDesa()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register-desa');
    }

    public function registerDesa(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'nipd'     => 'nullable|string|max:50',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'desa'     => 'required|string|max:255',
            'no_hp'    => 'nullable|string|max:20',
            'sk_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'desa.required'     => 'Nama desa wajib diisi.',
            'sk_file.mimes'     => 'File SK harus berformat PDF, JPG, JPEG, atau PNG.',
            'sk_file.max'       => 'Ukuran file SK maksimal 5MB.',
        ]);

        $skPath = null;
        if ($request->hasFile('sk_file')) {
            $skPath = $request->file('sk_file')->store('sk_files', 'public');
        }

        User::create([
            'name'     => $validated['name'],
            'nipd'     => $validated['nipd'] ?? null,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'kepala_desa',
            'desa'     => $validated['desa'],
            'no_hp'    => $validated['no_hp'] ?? null,
            'sk_file'  => $skPath,
            'status'   => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang dalam proses verifikasi oleh Dinas ESDM. Silakan login kembali setelah akun disetujui.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
