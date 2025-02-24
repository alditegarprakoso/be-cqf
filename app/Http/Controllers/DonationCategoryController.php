<?php

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use Illuminate\Http\Request;

class DonationCategoryController extends Controller
{

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $categories = DonationCategory::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%");
            })->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = DonationCategory::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'icon' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ]);

            $payload = $request->only('name', 'description', 'icon', 'status');

            $category = DonationCategory::create($payload);

            return response()->json($category, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'icon' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ]);

            $category = DonationCategory::findOrFail($id);
            $payload = $request->only('name', 'description', 'icon', 'status');

            $category->update($payload);

            return response()->json($category, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = DonationCategory::findOrFail($id);

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
