<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $users = User::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
                ->paginate($perPage);

            $users->getCollection()->transform(function ($user) {
                $user->photo = $user->photo ? asset($user->photo) : $user->photo;
                return $user;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->photo = $user->photo ? asset($user->photo) : $user->photo;

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'position' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $userData = $request->only('name', 'email', 'password', 'position', 'status', 'photo');

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('uploads/user', 'public');
                $userData['photo'] = "storage/" . $photoPath;
            }

            $userData['password'] = Hash::make($userData['password']);
            $user = User::create($userData);

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'position' => 'sometimes|string|max:255',
                'status' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user = User::findOrFail($id);
            $userData = $request->only('name', 'email', 'position', 'status', 'photo');

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                $photoPath = $request->file('photo')->store('uploads/user', 'public');
                $userData['photo'] = 'storage/' . $photoPath;
            }

            $user->update($userData);

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->photo) {
                $photoPath = str_replace('storage/', '', $user->photo);

                if (Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
