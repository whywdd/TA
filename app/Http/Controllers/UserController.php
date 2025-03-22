<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('User', compact('users'));
    }

    public function getData()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function create()
    {
        return view('TambahAkun');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:penggunas'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'tipe_pengguna' => ['required', 'in:owner,karyawan']
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'tipe_pengguna.required' => 'Tipe pengguna harus dipilih',
            'tipe_pengguna.in' => 'Tipe pengguna tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipe_pengguna' => $request->tipe_pengguna
            ]);

            return redirect()->route('User.index')
                ->with('success', 'Akun berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan akun. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('EditAkun', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:penggunas,email,'.$id],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'tipe_pengguna' => ['required', 'in:owner,karyawan']
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'tipe_pengguna.required' => 'Tipe pengguna harus dipilih',
            'tipe_pengguna.in' => 'Tipe pengguna tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::findOrFail($id);
            
            $data = [
                'nama' => $request->nama,
                'email' => $request->email,
                'tipe_pengguna' => $request->tipe_pengguna
            ];

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('User.index')
                ->with('success', 'Akun berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui akun. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('User.index')
                ->with('success', 'Akun berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus akun. ' . $e->getMessage());
        }
    }
}