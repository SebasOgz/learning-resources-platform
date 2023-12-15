<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Resources', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'resources' => Resource::with('category')->get(),
        ]);
    }
}
