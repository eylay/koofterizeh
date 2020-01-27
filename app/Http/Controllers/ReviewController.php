<?php

namespace App\Http\Controllers;

use App\Review;
use App\Food;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('only_customer')->only('store');
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rate' => 'required|integer|min:1|max:5',
            'body' => 'nullable',
        ]);

        $food = Food::whereUid($request->food_uid)->firstOrFail();
        $data['food_id'] = $food->id;
        $data['customer_id'] = current_customer('id');

        Review::create($data);
        return back()->withMessage('امتیاز شما با موفقیت در سیستم ثبت شد. از همکاری شما متشکریم.');
    }

    public function show(Review $review)
    {
        //
    }

    public function edit(Review $review)
    {
        //
    }

    public function update(Request $request, Review $review)
    {
        //
    }

    public function destroy(Review $review)
    {
        //
    }
}
