<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    public function index(){
        $catedories = Category::all();
        return $this->response->collection($catedories,new CategoryTransformer());
    }
}
