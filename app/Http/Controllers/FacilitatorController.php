<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FacilitatorController extends Controller
{
    public function index(Request $request) {
        if($request->user()->type == "admin") {
            return abort(403, 'Unauthorized action.');
        }

        return inertia('Facilitator/Dashboard');
    }
}
