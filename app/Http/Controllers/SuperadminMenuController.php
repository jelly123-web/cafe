<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminMenuController extends Controller
{
    public function index(): View
    {
        $search = request()->string('search')->toString();

        $menus = Menu::query()
            ->with('category')
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $categories = MenuCategory::query()
            ->withCount('menus')
            ->orderBy('name')
            ->get();

        return view('superadmin.menus.index', [
            'menus' => $menus,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.menus.create', [
            'menu' => new Menu(),
            'categories' => MenuCategory::query()->orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMenu($request);
        $menu = new Menu();
        $menu->fill($data);
        $menu->image_path = $this->storeImage($request);
        $menu->save();

        return redirect()->route('superadmin.menus.index')->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        return view('superadmin.menus.edit', [
            'menu' => $menu,
            'categories' => MenuCategory::query()->orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $this->validateMenu($request, $menu->id);
        $menu->fill($data);

        if ($request->hasFile('image')) {
            $this->deleteImageIfExists($menu->image_path);
            $menu->image_path = $this->storeImage($request);
        }

        $menu->save();

        return redirect()->route('superadmin.menus.index')->with('status', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->deleteImageIfExists($menu->image_path);
        $menu->delete();

        return redirect()->route('superadmin.menus.index')->with('status', 'Menu berhasil dihapus.');
    }

    private function validateMenu(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'menu_category_id' => ['nullable', 'exists:menu_categories,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('menus', 'code')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('menus', 'public');
    }

    private function deleteImageIfExists(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
