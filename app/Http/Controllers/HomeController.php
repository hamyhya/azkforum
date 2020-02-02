<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\forum;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        return redirect('forum.index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $forums = Forum::paginate(5); 
        return view('forum.index', compact('forums'));
    }
}
