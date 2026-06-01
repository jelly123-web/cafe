<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Support\CroppedImageStore;
use Illuminate\Http\JsonResponse;
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
            ->where('code', '!=', 'A01')
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('superadmin.menus.index', [
            'menus' => $menus,
            'categories' => MenuCategory::query()->orderBy('name')->get(),
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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateMenu($request);
        $menu = new Menu();
        $menu->fill($data);
        $menu->image_path = $this->storeImage($request);
        $menu->save();

        if ($request->expectsJson() || $request->ajax()) {
            $menu->load('category');
            return response()->json([
                'message' => 'Menu berhasil ditambahkan.',
                'menu' => $this->menuPayload($menu),
            ]);
        }

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

    public function update(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        $data = $this->validateMenu($request, $menu->id);
        $menu->fill($data);

        if ($request->filled('cropped_image') || $request->hasFile('image')) {
            $this->deleteImageIfExists($menu->image_path);
            $menu->image_path = $this->storeImage($request);
        }

        $menu->save();

        if ($request->expectsJson() || $request->ajax()) {
            $menu->load('category');
            return response()->json([
                'message' => 'Menu berhasil diperbarui.',
                'menu' => $this->menuPayload($menu),
            ]);
        }

        return redirect()->route('superadmin.menus.index')->with('status', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse|JsonResponse
    {
        $this->deleteImageIfExists($menu->image_path);
        $menu->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['message' => 'Menu berhasil dihapus.']);
        }

        return redirect()->route('superadmin.menus.index')->with('status', 'Menu berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        $menus = Menu::query()->get(['id', 'image_path']);
        foreach ($menus as $menu) {
            $this->deleteImageIfExists($menu->image_path);
        }
        Menu::query()->delete();

        return redirect()->route('superadmin.menus.index')->with('status', 'Semua menu berhasil dihapus.');
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
            'image' => ['nullable', 'image'],
            'cropped_image' => ['nullable', 'string'],
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        if ($request->filled('cropped_image')) {
            return CroppedImageStore::store($request->string('cropped_image')->toString(), 'menus', 'menu');
        }

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

    private function menuPayload(Menu $menu): array
    {
        return [
            'id' => $menu->id,
            'code' => $menu->code,
            'name' => $menu->name,
            'menu_category_id' => $menu->menu_category_id,
            'category_name' => $menu->category?->name ?? 'Tanpa kategori',
            'selling_price' => (float) $menu->selling_price,
            'cost_price' => (float) $menu->cost_price,
            'image_url' => $menu->image_path && Storage::disk('public')->exists($menu->image_path)
                ? Storage::url($menu->image_path)
                : asset('images/menu-placeholder.svg'),
        ];
    }
}
