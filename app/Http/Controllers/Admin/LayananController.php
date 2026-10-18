<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jasa;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Jasa::latest()->get();

        return view('pages.admin.layanan', compact('layanans'));
    }
}
