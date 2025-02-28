<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');

            $donations = Donation::with('donationCategory')
                ->withCount(['donatureLists as total_collected' => function ($query) {
                    $query->select(DB::raw('COALESCE(SUM(total_donation), 0)')); // Biar kalau belum ada donasi, hasilnya tetap 0, bukan null.
                }])
                ->when($search, function ($query) use ($search) {
                    return $query->where('title', 'like', "%$search%");
                })
                ->paginate($perPage);

            $donations->getCollection()->transform(function ($donation) {
                $donation->thumbnail = $donation->thumbnail ? asset($donation->thumbnail) : $donation->thumbnail;
                $donation->is_target_reached = $donation->total_collected >= $donation->target_amount;
                return $donation;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data donasi berhasil diambil',
                'data' => $donations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data donasi',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $donation = Donation::with('donationCategory')->findOrFail($id);

            $donation->thumbnail = $donation->thumbnail ? asset($donation->thumbnail) : $donation->thumbnail;

            return response()->json([
                'success' => true,
                'message' => 'Data donasi berhasil diambil',
                'data' => $donation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Donasi tidak ditemukan',
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
                'description' => 'nullable|string|max:255',
                'target_amount' => 'required|string',
                'bank_account' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $donationData = $request->only('category_id', 'title', 'description', 'target_amount', 'bank_account', 'status', 'thumbnail');

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('uploads/donation', 'public');
                $donationData['thumbnail'] = "storage/" . $thumbnailPath;
            }

            $donation = Donation::create($donationData);

            return response()->json($donation, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat donasi',
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
                'description' => 'nullable|string|max:255',
                'target_amount' => 'required|string',
                'bank_account' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $donation = Donation::findOrFail($id);
            $donationData = $request->only('category_id', 'title', 'description', 'target_amount', 'bank_account', 'status', 'thumbnail');

            if ($request->hasFile('thumbnail')) {
                if ($donation->thumbnail) {
                    Storage::disk('public')->delete($donation->thumbnail);
                }

                $thumbnailPath = $request->file('thumbnail')->store('uploads/donation', 'public');
                $donationData['thumbnail'] = 'storage/' . $thumbnailPath;
            }

            $donation->update($donationData);

            return response()->json($donation, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate donasi',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $donation = Donation::findOrFail($id);

            if ($donation->thumbnail) {
                $thumbnailPath = str_replace('storage/', '', $donation->thumbnail);

                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }

            $donation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil dihapus',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus donasi',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
