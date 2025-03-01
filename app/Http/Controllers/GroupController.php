<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $groups = Group::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%");
            })
                ->paginate($perPage);

            $groups->getCollection()->transform(function ($group) {
                $group->logo = $group->logo ? asset($group->logo) : $group->logo;
                return $group;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data group berhasil diambil',
                'data' => $groups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data group',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $group = Group::findOrFail($id);

            $group->logo = $group->logo ? asset($group->logo) : $group->logo;

            return response()->json([
                'success' => true,
                'message' => 'Data group berhasil diambil',
                'data' => $group,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $groupData = $request->only('name', 'status', 'logo');

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('uploads/groups', 'public');
                $groupData['logo'] = "storage/" . $logoPath;
            }

            $group = Group::create($groupData);

            return response()->json($group, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat group',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $group = Group::findOrFail($id);
            $groupData = $request->only('name', 'status', 'logo');

            if ($request->hasFile('logo')) {
                if ($group->logo) {
                    Storage::disk('public')->delete($group->logo);
                }

                $logoPath = $request->file('logo')->store('uploads/groups', 'public');
                $groupData['logo'] = 'storage/' . $logoPath;
            }

            $group->update($groupData);

            return response()->json($group, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate group',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $group = Group::findOrFail($id);

            if ($group->logo) {
                $logoPath = str_replace('storage/', '', $group->logo);

                if (Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
            }

            $group->delete();

            return response()->json([
                'success' => true,
                'message' => 'Group berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus group',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function homepage()
    {
        $groups = Group::where('status', 'Aktif')->get();

        $groups->transform(function ($group) {
            $group->logo = $group->logo ? asset($group->logo) : $group->logo;
            return $group;
        });

        return $groups;
    }
}
