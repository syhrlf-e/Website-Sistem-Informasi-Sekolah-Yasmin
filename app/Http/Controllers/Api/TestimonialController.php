<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of active testimonials for public display
     */
    public function index()
    {
        try {
            $testimonials = Testimonial::active()
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data testimoni'
            ], 500);
        }
    }
}
