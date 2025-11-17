<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index(Request $request)
    {
        $query = ExpenseCategory::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'description' => $category->description,
                    'accounting_code' => $category->accounting_code,
                    'requires_justification' => $category->requires_justification,
                    'is_active' => $category->is_active,
                    'sort_order' => $category->sort_order,
                ];
            }),
        ]);
    }

    /**
     * Display the specified expense category.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        return response()->json([
            'id' => $expenseCategory->id,
            'name' => $expenseCategory->name,
            'code' => $expenseCategory->code,
            'icon' => $expenseCategory->icon,
            'color' => $expenseCategory->color,
            'description' => $expenseCategory->description,
            'accounting_code' => $expenseCategory->accounting_code,
            'requires_justification' => $expenseCategory->requires_justification,
            'is_active' => $expenseCategory->is_active,
            'sort_order' => $expenseCategory->sort_order,
        ]);
    }
}
