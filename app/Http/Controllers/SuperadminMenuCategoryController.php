<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperadminMenuCategoryController extends Controller
{
    public function index(): View
    {
        $categories = MenuCategory::query()
            ->withCount([
                'menus' => function ($query) {
                    $query->where('code', '!=', 'A01');
                },
                'packages',
            ])
            ->get()
            ->map(function (MenuCategory $category) {
                $category->display_count = $category->name === 'paket'
                    ? $category->packages_count
                    : $category->menus_count;

                $category->display_label = $category->name === 'paket' ? 'paket' : 'menu';

                return $category;
            });

        return view('superadmin.menu-categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.menu-categories.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:menu_categories,name',
            'description' => 'nullable|string|max:255',
        ]);
        
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        MenuCategory::create($data);

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(MenuCategory $menuCategory): View
    {
        return view('superadmin.menu-categories.edit', ['menuCategory' => $menuCategory]);
    }

    public function update(Request $request, MenuCategory $menuCategory): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:menu_categories,name,' . $menuCategory->id,
            'description' => 'nullable|string|max:255',
        ]);
        
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        $menuCategory->update($data);

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, MenuCategory $menuCategory): \Illuminate\Http\RedirectResponse
    {
        $menuCategory->delete();

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil dihapus.');
    }

    public function destroyAll(Request $request): \Illuminate\Http\RedirectResponse
    {
        MenuCategory::query()->delete();

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Semua kategori berhasil dihapus.');
    }
}
