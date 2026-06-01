<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateCategory($request);
        $category = MenuCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Kategori berhasil ditambahkan.',
                'category' => $this->categoryPayload($category->fresh('menus')),
            ]);
        }

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(MenuCategory $menuCategory): View
    {
        return view('superadmin.menu-categories.edit', [
            'category' => $menuCategory,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, MenuCategory $menuCategory): RedirectResponse|JsonResponse
    {
        $data = $this->validateCategory($request, $menuCategory->id);
        $menuCategory->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Kategori berhasil diperbarui.',
                'category' => $this->categoryPayload($menuCategory->fresh('menus')),
            ]);
        }

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, MenuCategory $menuCategory): RedirectResponse|JsonResponse
    {
        if ($menuCategory->menus()->count() > 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Kategori yang masih dipakai menu tidak bisa dihapus.'], 422);
            }
            return back()->with('error', 'Kategori yang masih dipakai menu tidak bisa dihapus.');
        }

        $id = $menuCategory->id;
        $menuCategory->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Kategori berhasil dihapus.',
                'id' => $id,
            ]);
        }

        return redirect()->route('superadmin.menu-categories.index')->with('status', 'Kategori berhasil dihapus.');
    }

    public function destroyAll(Request $request): RedirectResponse|JsonResponse
    {
        $deleted = MenuCategory::query()->doesntHave('menus')->delete();
        $usedCount = MenuCategory::query()->has('menus')->count();

        if ($deleted > 0 && $usedCount > 0) {
            $message = "Berhasil hapus {$deleted} kategori yang belum dipakai. {$usedCount} kategori masih dipakai menu, jadi tidak dihapus.";
        } elseif ($deleted > 0) {
            $message = "Semua kategori yang belum dipakai berhasil dihapus ({$deleted}).";
        } elseif ($usedCount > 0) {
            $message = 'Tidak ada kategori kosong untuk dihapus. Semua kategori yang tersisa masih dipakai menu.';
        } else {
            $message = 'Tidak ada kategori untuk dihapus.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('superadmin.menu-categories.index')->with('status', $message);
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

    private function categoryPayload(MenuCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'menus_count' => $category->menus()->count(),
        ];
    }
}
