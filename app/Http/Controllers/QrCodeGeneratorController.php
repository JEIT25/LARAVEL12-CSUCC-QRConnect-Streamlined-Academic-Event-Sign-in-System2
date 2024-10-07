<?php

namespace App\Http\Controllers;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class QrCodeGeneratorController extends Controller
{
    public function show(User $user)
    {


        return inertia('QrGenerator/Result', [
            'user' => $user
        ]);
    }
}
