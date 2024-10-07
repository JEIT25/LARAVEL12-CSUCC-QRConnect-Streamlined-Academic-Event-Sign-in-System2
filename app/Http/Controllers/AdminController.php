<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request) {
        if($request->user()->type == "facilitator") {
            return abort(403, 'Unauthorized action.');
        }

        return inertia('Admin/Index');
    }
}
