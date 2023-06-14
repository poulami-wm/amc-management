<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function view(){
        return view('category.create');
    }


    public function category_store (Request $request){
    try {
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string']

    ]);

    $order=  Category::create([
        'name'          => $request['name'],
    ]);

    }catch(Exception $e) {
                return back()->withErrors($e->getMessage());
    }
        return redirect()->route('category.view');
    }
}
