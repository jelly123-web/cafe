<?php

namespace App\Http\Controllers;

use App\Models\FoodPackage;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Support\CroppedImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminMenuController extends Controller
{
    private const FIXED_CATEGORIES = [
        'makanan',
        'minuman',
        'paket',
    ];

    public function index(): View|JsonResponse
    {
        $this->ensureFixedCategories();

        $search = request()->string('search')->toString();
        $categoryId = request()->get('category_id');
        $allowedCategoryIds = $this->fixedCategoryIds();

        $query = Menu::query()
            ->with('category')
            ->where('code', '!=', 'A01')
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                if ($categoryId !== 'all' && $categoryId !== '') {
                    $query->where('menu_category_id', $categoryId);
                }
            })
            ->latest();

        if (request()->ajax()) {
            $menus = $query->get();
            return response()->json([
                'menus' => $menus->map(fn($m) => [
                    'id' => $m->id,
                    'code' => $m->code,
                    'name' => $m->name,
                    'selling_price' => (float) $m->selling_price,
                    'cost_price' => (float) $m->cost_price,
                    'menu_category_id' => $m->menu_category_id,
                    'category_name' => $m->category?->name ?? 'Tanpa kategori',
                    'image_url' => $m->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($m->image_path)
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($m->image_path)
                        : asset('images/menu-placeholder.svg'),
                ]),
                'total' => $menus->count(),
            ]);
        }

        $menus = $query->paginate(12)->withQueryString();

        $categories = MenuCategory::query()
            ->whereIn('id', $allowedCategoryIds)
            ->withCount('menus')
            ->orderBy('name')
            ->get()
            ->map(function (MenuCategory $category) {
                $category->display_count = $category->name === 'paket'
                    ? FoodPackage::query()->count()
                    : $category->menus_count;

                return $category;
            });

        return view('superadmin.menus.index', [
            'menus' => $menus,
            'categories' => $categories,
            'total_menus' => Menu::where('code', '!=', 'A01')->count(),
        ]);
    }

    public function create(): View
    {
        $this->ensureFixedCategories();

        return view('superadmin.menus.create', [
            'menu' => new Menu(),
            'categories' => MenuCategory::query()
                ->whereIn('name', self::FIXED_CATEGORIES)
                ->orderBy('name')
                ->get(),
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
        $this->ensureFixedCategories();

        return view('superadmin.menus.edit', [
            'menu' => $menu,
            'categories' => MenuCategory::query()
                ->whereIn('name', self::FIXED_CATEGORIES)
                ->orderBy('name')
                ->get(),
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
        DB::transaction(function () use ($menu): void {
            $this->deleteImageIfExists($menu->image_path);
            $menu->foodPackages()->detach();
            $menu->promos()->detach();
            $menu->delete();
        });

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
        $allowedCategoryIds = $this->fixedCategoryIds();

        return $request->validate([
            'menu_category_id' => ['nullable', Rule::in($allowedCategoryIds)],
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
                ? Storage::disk('public')->url($menu->image_path)
                : asset('images/menu-placeholder.svg'),
        ];
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

    private function fixedCategoryIds(): array
    {
        $this->ensureFixedCategories();

        return MenuCategory::query()
            ->whereIn('name', self::FIXED_CATEGORIES)
            ->orderBy('name')
            ->pluck('id')
            ->all();
    }
}
