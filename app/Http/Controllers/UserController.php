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
        return view('User');
    }

    public function getData(Request $request)
    {
        $query = User::select('id', 'nama', 'email', 'tipe_pengguna');

        // Apply type filter
        if ($request->has('type') && !empty($request->type)) {
            $query->where('tipe_pengguna', $request->type);
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        return response()->json($users);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
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
                ->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('EditAkun', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('User.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:penggunas,email,'.$id],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'tipe_pengguna' => ['required', 'in:owner,karyawan']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        try {
            $user = User::findOrFail($id);
            
            $data = [
                'nama' => $request->nama,
                'email' => $request->email,
                'tipe_pengguna' => $request->tipe_pengguna
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('User.index')
                ->with('success', 'Data user berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
}