<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminMenuCategoryController extends Controller
{
    private const FIXED_CATEGORIES = [
        'makanan',
        'minuman',
        'paket',
    ];

    public function index(): View
    {
        $this->ensureFixedCategories();

        $categories = MenuCategory::query()
            ->withCount('menus')
            ->whereIn('name', self::FIXED_CATEGORIES)
            ->get()
            ->sortBy(fn (MenuCategory $category) => array_search($category->name, self::FIXED_CATEGORIES, true))
            ->values();

        return view('superadmin.menu-categories.index', [
            'categories' => $categories,
            'fixedCategories' => self::FIXED_CATEGORIES,
        ]);
    }

    public function create(): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    public function store(Request $request): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    public function edit(MenuCategory $menuCategory): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    public function update(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    public function destroy(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    public function destroyAll(Request $request): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    private function ensureFixedCategories(): void
    {
        foreach (self::FIXED_CATEGORIES as $name) {
            MenuCategory::firstOrCreate(
                ['name' => $name],
                ['slug' => $name]
            );
        }

        MenuCategory::query()
            ->whereNotIn('name', self::FIXED_CATEGORIES)
            ->delete();
    }

    private function readOnlyResponse(): JsonResponse
    {
        if (! request()->expectsJson()) {
            abort(403, 'Kategori menu dikunci. Hanya tiga kategori tetap yang boleh dipakai.');
        }

        return response()->json([
            'message' => 'Kategori menu dikunci. Hanya tiga kategori tetap yang boleh dipakai.',
        ], 403);
    }
}
