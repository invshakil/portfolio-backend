<?php

namespace App\Http\Controllers;


use function view;

class ApplicationController extends Controller
{
    public function index()
    {
        return view('admin');
    }
}
