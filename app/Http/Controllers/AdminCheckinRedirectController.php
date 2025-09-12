<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCheckinRedirectController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('staff.checkins.index');
        }
        return redirect()->route('admin.checkins.index');
    }
    
    public function show($checkin)
    {
        if (auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('staff.checkins.show', $checkin);
        }
        return redirect()->route('admin.checkins.show', $checkin);
    }
    
    public function create()
    {
        if (auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('staff.checkins.create');
        }
        return redirect()->route('admin.checkins.create');
    }
}