<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:categories',
            'description' => 'nullable|string',
        ]);
        Category::create($data + ['is_active' => true]);
        return back()->with('success', 'ເພີ່ມໝວດໝູ່ "' . $data['name'] . '" ສຳເລັດ');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $category->update($data);
        return back()->with('success', 'ແກ້ໄຂໝວດໝູ່ ສຳເລັດ');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'ລຶບໝວດໝູ່ ສຳເລັດ');
    }
}
