<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    public function __invoke(Request $request, Resource $resource)
    {
        $voter = Voter::getOrCreateVoter($request);

        $resource->votes()->toggle($voter->id);

        return $resource->load('votes', 'category');
        
    }
}
 