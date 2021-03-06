<?php

namespace App\Http\Controllers;

use App\Food;
use App\Cook;
use App\Cat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FoodController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('active_cook_or_master');
    }

    public function index(Request $request)
    {
        $cooks = Cook::all();
        $foods = Food::query();

        if (master()) {
            if ($request->cooks && count($request->cooks)) {
                $foods = $foods->whereIn('cook_id', $request->cooks);
            }
            if ($request->confirmed !== null) {
                $foods = $foods->whereConfirmed($request->confirmed);
            }
        }else {
            $foods = $foods->where('cook_id', current_cook()->id);
        }

        if ($phrase = $request->title) {
            $foods = $foods->where('title', 'like', "%$phrase%");
        }

        if ($type = $request->t) {
            $foods = $foods->whereType($type);
        }

        $foods = $foods->latest()->paginate(20);
        $pendings = Food::whereConfirmed(0)->count();

        return view('dashboard.foods.index', compact('foods', 'cooks', 'type', 'pendings'));
    }

    public function create(Request $request)
    {
        $request->validate([
            't' => ['required', Rule::in(['food','product'])]
        ]);
        $food = new Food;
        $type = $request->t;
        $word = $type == 'food' ? 'غذا' : 'محصول خانگی';
        $cats = Cat::whereType($type)->get();
        return view('dashboard.foods.form', compact('food', 'type', 'word', 'cats'));
    }

    public function store(Request $request)
    {
        $data = self::validation(new Food);
        $data['uid'] = rs();
        $data['cook_id'] = cook() ? current_cook()->id : 0;
        Food::create($data);
        return redirect()->route('food.index')->withMessage('غذای مورد نظر با موفقیت در سیستم ثبت شد. منتظر تایید ناظر باشید.');
    }

    public function show(Food $food)
    {
        cook_check($food);
        return view('dashboard.foods.show', compact('food'));
    }

    public function edit(Food $food)
    {
        cook_check($food);
        $type = $food->type;
        $word = $type == 'food' ? 'غذا' : 'محصول خانگی';
        $cats = Cat::whereType($type)->get();
        return view('dashboard.foods.form', compact('food', 'type', 'word', 'cats'));
    }

    public function update(Request $request, Food $food)
    {
        cook_check($food);
        $data = self::validation($food);
        $food->update($data);
        return redirect()->route('food.index')->withMessage(__('SUCCESS'));
    }

    public function confirm(Food $food)
    {
        $food->confirmed = !$food->confirmed;
        $food->save();
        return back()->withMessage('غذا مورد نظر تایید شد');
    }

    public function confirm_all()
    {
        Food::whereConfirmed(0)->update([
            'confirmed' => 1
        ]);
        return back()->withMessage('همه غذا ها تایید شدند');
    }

    public function destroy(Food $food)
    {
        cook_check($food);
        delete_file($food->image);
        $food->delete();
        return back()->withMessage(__('SUCCESS'));
    }

    public static function validation($food)
    {
        $data = request()->validate([
            'title' => 'required|string|max:190',
            'cat_id' => 'nullable|exists:cats,id',
            'price' => 'required|integer',
            'discount' => 'nullable|integer|min:0|max:99',
            'min' => 'nullable|integer|min:1',
            'material' => 'required',
            'type' => [
                'required',
                Rule::requiredIf(!$food->id),
                Rule::in([
                    'food', 'product'
                ])
            ],
            'image' => [
                Rule::requiredIf(!$food->id),
                'image',
                'max:3000',
            ]
        ]);

        if ($data['cat_id'] === null) {
            $data['cat_id'] = 0;
        }

        if ( isset($data['image']) && $data['image'] ) {
            $data['image'] = upload($data['image'], $food->image);
        }

        if (master()) {
            $data['confirmed'] = request('confirmed') ?? false;
        }else {
            $data['confirmed'] = 0;
        }

        // replace - with _
        if (strpos($data['title'], '-') !== false) {
            $data['title'] = str_replace('-', '_', $data['title']);
        }

        return $data;
    }
}
