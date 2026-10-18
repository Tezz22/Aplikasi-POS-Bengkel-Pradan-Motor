<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    public function index()
    {
        return view('pages.kasir.profil');
    }
}
