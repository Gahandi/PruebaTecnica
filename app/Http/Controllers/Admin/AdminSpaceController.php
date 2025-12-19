<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;


class AdminSpaceController extends Controller
{
    //
    public function index()
    {
        $spaces = Space::with([
                'users' => function ($q) {
                    $q->wherePivotNull('deleted_at')
                      ->with('spaces'); // opcional
                }
            ])
            ->latest()
            ->paginate(15);

        return view('admin.spaces.index', compact('spaces'));
    }
}
