<?php

namespace App\Http\Controllers;

use App\Models\Kajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KajianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $kajians = Kajian::with('kajianCategory')
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%$search%");
                    });
                })->paginate($perPage);

            $kajians->getCollection()->transform(function ($kajian) {
                $kajian->thumbnail = $kajian->thumbnail ? asset($kajian->thumbnail) : $kajian->thumbnail;
                return $kajian;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data kajian berhasil diambil',
                'data' => $kajians,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kajian',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kajian = Kajian::with('kajianCategory')->findOrFail($id);

            $kajian->thumbnail = $kajian->thumbnail ? asset($kajian->thumbnail) : $kajian->thumbnail;

            return response()->json([
                'success' => true,
                'message' => 'Data kajian berhasil diambil',
                'data' => $kajian,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kajian tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'datetime' => 'required|date',
                'is_live' => 'required|string',
                'url' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ]);

            $kajianData = $request->only('category_id', 'title', 'subtitle', 'description', 'datetime', 'is_live', 'url', 'status', 'thumbnail');
            $kajianData['is_live'] = $request->is_live === 'Live' ? 1 : 0;

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('uploads/kajian', 'public');
                $kajianData['thumbnail'] = "storage/" . $thumbnailPath;
            }

            $kajian = Kajian::create($kajianData);

            return response()->json($kajian, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kajian',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'category_id' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'datetime' => 'required|date',
                'is_live' => 'required|string',
                'url' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $kajian = Kajian::findOrFail($id);
            $kajianData = $request->only('category_id', 'title', 'subtitle', 'description', 'datetime', 'is_live', 'url', 'status', 'thumbnail');
            $kajianData['is_live'] = $request->is_live === 'Live' ? 1 : 0;

            if ($request->hasFile('thumbnail')) {
                if ($kajian->thumbnail) {
                    Storage::disk('public')->delete($kajian->thumbnail);
                }

                $thumbnailPath = $request->file('thumbnail')->store('uploads/kajian', 'public');
                $kajianData['thumbnail'] = 'storage/' . $thumbnailPath;
            }

            $kajian->update($kajianData);

            return response()->json($kajian, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kajian',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kajian = Kajian::findOrFail($id);

            if ($kajian->thumbnail) {
                $thumbnailPath = str_replace('storage/', '', $kajian->thumbnail);

                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            $kajian->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kajian berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kajian',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function homepage($categoryId = null)
    {
        $kajians = Kajian::where('status', 'Aktif')
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->get();

        $kajians->transform(function ($kajian) {
            $kajian->thumbnail = $kajian->thumbnail ? asset($kajian->thumbnail) : $kajian->thumbnail;
            return $kajian;
        });

        return $kajians;
    }
}
