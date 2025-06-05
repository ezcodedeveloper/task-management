<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class ApiController extends Controller
{
    public function getQuote(){
        try {
        $response = Http::withoutVerifying()->get('https://api.quotable.io/random');
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'quote' => $response->json(),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch quote.',
        ], $response->status());
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
    }
}
