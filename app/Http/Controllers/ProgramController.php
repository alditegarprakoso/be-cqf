<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $programs = Program::when($search, function ($query) use ($search) {
                return $query->where('title', 'like', "%$search%");
            })
                ->paginate($perPage);

            $programs->getCollection()->transform(function ($program) {
                $program->thumbnail = $program->thumbnail ? asset($program->thumbnail) : $program->thumbnail;
                return $program;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data program berhasil diambil',
                'data' => $programs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data program',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $program = Program::findOrFail($id);

            $program->thumbnail = $program->thumbnail ? asset($program->thumbnail) : $program->thumbnail;

            return response()->json([
                'success' => true,
                'message' => 'Data program berhasil diambil',
                'data' => $program,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $programData = $request->only('title', 'subtitle', 'description', 'status', 'thumbnail');

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('uploads/programs', 'public');
                $programData['thumbnail'] = "storage/" . $thumbnailPath;
            }

            $program = Program::create($programData);

            return response()->json($program, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat program',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $program = Program::findOrFail($id);
            $programData = $request->only('title', 'subtitle', 'description', 'status', 'thumbnail');

            if ($request->hasFile('thumbnail')) {
                if ($program->thumbnail) {
                    Storage::disk('public')->delete($program->thumbnail);
                }

                $thumbnailPath = $request->file('thumbnail')->store('uploads/programs', 'public');
                $programData['thumbnail'] = 'storage/' . $thumbnailPath;
            }

            $program->update($programData);

            return response()->json($program, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate program',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $program = Program::findOrFail($id);

            if ($program->thumbnail) {
                $thumbnailPath = str_replace('storage/', '', $program->thumbnail);

                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            $program->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus program',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
