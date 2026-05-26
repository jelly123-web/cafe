<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminMenuCategoryController extends Controller
{
    public function index(): View
    {
        return view('superadmin.menu-categories.index', [
            'categories' => MenuCategory::query()->withCount('menus')->orderBy('name')->paginate(8),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.menu-categories.create', [
            'category' => new MenuCategory(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);
        MenuCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(MenuCategory $menuCategory): View
    {
        return view('superadmin.menu-categories.edit', [
            'category' => $menuCategory,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, MenuCategory $menuCategory): RedirectResponse
    {
        $data = $this->validateCategory($request, $menuCategory->id);
        $menuCategory->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(MenuCategory $menuCategory): RedirectResponse
    {
        if ($menuCategory->menus()->count() > 0) {
            return back()->with('error', 'Kategori yang masih dipakai menu tidak bisa dihapus.');
        }

        $menuCategory->delete();

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil dihapus.');
    }

    private function validateCategory(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menu_categories', 'name')->ignore($ignoreId),
            ],
        ]);
    }
}
