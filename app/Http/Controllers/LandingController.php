<?php

namespace App\Http\Controllers;

use App\Blog;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->take(3)->get();
    	return view('landing.index', compact('blogs'));
    }

    public function new_cook()
    {
        return view('landing.new_cook');
    }

    public function blogs(Request $request)
    {
        // init vars
        $count = Blog::count();

        // prepare query
        $blogs = Blog::query();

        if ($phrase = $request->search) {
            $blogs = $blogs->where(function ($query) use ($phrase) {
                $query->orWhere('title', 'like', "%$phrase%")->orWhere('subtitle', 'like', "%$phrase%")->orWhere('tags', 'like', "%$phrase%");
            });
        }

        $blogs = $blogs->paginate(9);
        return view('landing.blogs', compact('blogs', 'count'));
    }

    public function show_blog($title)
    {
        $blog = Blog::whereTitle(raw($title))->firstOrFail();
        $blog->increment('seens');
        return view('landing.blog', compact('blog'));
    }

    public function message()
    {
        $message = session('message');
        return view('landing.message', compact('message'));
    }
}
