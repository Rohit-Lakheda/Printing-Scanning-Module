<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperatorHomeController extends Controller
{
    /**
     * Display the operator home page with Printing and Scanning options
     */
    public function index()
    {
        return view('operator.home');
    }
}
