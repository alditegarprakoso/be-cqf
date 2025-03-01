<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request, DonationController $donationController, ProgramController $programController, KajianController $kajianController, GroupController $groupController, DonationCategoryController $donationCategoryController, KajianCategoryController $kajianCategoryController)
    {
        // Request parameters
        $categoryDonationId = $request->query('category_donation_id', '');
        $categoryKajianId = $request->query('category_kajian_id', '');

        $categoryDonatioin = $donationCategoryController->homepage();
        $donations = $donationController->homepage($categoryDonationId);
        $programs = $programController->homepage();
        $categoryKajian = $kajianCategoryController->homepage();
        $kajians = $kajianController->homepage($categoryKajianId);
        $groups = $groupController->homepage();

        return response()->json([
            'success' => true,
            'message' => 'Data donasi berhasil diambil',
            'data' => [
                'categoryDonation' => $categoryDonatioin,
                'donations' => $donations,
                'programs' => $programs,
                'categoryKajian' => $categoryKajian,
                'kajians' => $kajians,
                'groups' => $groups,
            ],
        ]);
    }
}
