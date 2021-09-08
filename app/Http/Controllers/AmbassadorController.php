<?php

namespace App\Http\Controllers;

use App\Models\User;

class AmbassadorController extends Controller
{
    public function index()
    {
        return User::ambassadors()->get();
    }
}
