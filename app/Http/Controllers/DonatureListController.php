<?php

namespace App\Http\Controllers;

use App\Models\DonatureList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonatureListController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $donatures = DonatureList::with('donation')
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('donature_name', 'like', "%$search%");
                    });
                })->paginate($perPage);

            $donatures->getCollection()->transform(function ($donature) {
                $donature->attachment = $donature->attachment ? asset($donature->attachment) : $donature->attachment;
                return $donature;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data donatur berhasil diambil',
                'data' => $donatures,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data donatur',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $donature = DonatureList::with('donation')->findOrFail($id);

            $donature->attachment = $donature->attachment ? asset($donature->attachment) : $donature->attachment;

            return response()->json([
                'success' => true,
                'message' => 'Data donatur berhasil diambil',
                'data' => $donature,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Donatur tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'donation_id' => 'required|string|max:255',
                'donature_name' => 'required|string|max:255',
                'email' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'total_donation' => 'required|string',
                'info' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'attachment' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $donatureData = $request->only('donation_id', 'donature_name', 'email', 'phone', 'total_donation', 'info', 'status', 'attachment');

            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('uploads/donatures', 'public');
                $donatureData['attachment'] = "storage/" . $attachmentPath;
            }

            $donature = DonatureList::create($donatureData);

            return response()->json($donature, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat donatur',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'donation_id' => 'required|string|max:255',
                'donature_name' => 'required|string|max:255',
                'email' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'total_donation' => 'required|string',
                'info' => 'nullable|string|max:255',
                'status' => 'required|string|max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $donature = DonatureList::findOrFail($id);
            $donatureData = $request->only('donation_id', 'donature_name', 'email', 'phone', 'total_donation', 'info', 'status', 'attachment');

            if ($request->hasFile('attachment')) {
                if ($donature->attachment) {
                    Storage::disk('public')->delete($donature->attachment);
                }

                $attachmentPath = $request->file('attachment')->store('uploads/donatures', 'public');
                $donatureData['attachment'] = 'storage/' . $attachmentPath;
            }

            $donature->update($donatureData);

            return response()->json($donature, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate donatur',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $donature = DonatureList::findOrFail($id);

            if ($donature->attachment) {
                $attachmentPath = str_replace('storage/', '', $donature->attachment);

                if (Storage::disk('public')->exists($attachmentPath)) {
                    Storage::disk('public')->delete($attachmentPath);
                }
            }

            $donature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Donatur berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus donatur',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
