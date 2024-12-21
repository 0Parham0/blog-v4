<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public static function storeTagsAndReturnTagIdsCollection($tagsInRequest)
    {
        $tags = collect($tagsInRequest)->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag])->id;
        });

        return $tags;
    }
}
