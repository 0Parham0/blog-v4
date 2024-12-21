<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class FetchSokanDataController extends Controller
{
    public function index()
    {
        $response = Http::get('https://api.sokanacademy.com/api/announcements/blog-index-header');

        if ($response->successful()) {
            $data = collect($response->json()['data']);

            $formattedData = $data->mapToGroups(function ($item) {
                $blog = $item['all'];
                return [
                    $blog['category_name'] => [
                        $blog['title'] => $blog['views_count']
                    ]
                ];
            });

            return response()->json($formattedData);
        }

        return response()->json(['error' => 'Unable to fetch data'], 500);
    }
}
