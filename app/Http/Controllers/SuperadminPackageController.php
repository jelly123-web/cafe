<?php

namespace App\Http\Controllers;

use App\Models\FoodPackage;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperadminPackageController extends Controller
{
    private const PACKAGE_CATEGORY = 'paket';

    public function index(): View
    {
        $search = request()->string('search')->toString();

        $packages = FoodPackage::query()
            ->with(['menus', 'category'])
            ->when($search, function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('superadmin.packages.index', [
            'packages' => $packages,
            'menus_all' => Menu::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.packages.create', [
            'package' => new FoodPackage(),
            'menus' => Menu::query()->orderBy('name')->get(),
            'selectedMenus' => [],
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        \Log::info('Store package request:', $request->all());
        $data = $this->validatePackage($request);
        $packageCategoryId = $this->packageCategoryId();
        
        $syncData = [];
        foreach ($data['menus'] as $menuId) {
            $quantity = $request->input("menu_quantities.{$menuId}", 1);
            $syncData[$menuId] = ['quantity' => $quantity];
        }

        $selectedMenuNames = $this->selectedMenuNames($syncData);

        $package = new FoodPackage();
        $package->fill($data);
        $package->description = $selectedMenuNames;
        $package->notes = $data['notes'] ?? null;
        $package->free_item = $data['free_item'] ?? null;
        $package->menu_category_id = $packageCategoryId;
        
        $package->cost_price = $data['cost_price'] ?? 0;
        
        if (empty($package->code)) {
            $package->code = 'PKG-' . strtoupper(bin2hex(random_bytes(3)));
        }
        
        $package->image_path = $this->storeImage($request);
        $package->save();
        $package->menus()->sync($syncData);

        if ($request->expectsJson() || $request->ajax()) {
            $package->load('menus');

            return response()->json([
                'message' => 'Paket berhasil ditambahkan.',
                'package' => $this->packagePayload($package),
            ]);
        }

        return redirect()->route('superadmin.packages.index')->with('status', 'Paket berhasil ditambahkan.');
    }

    public function edit(FoodPackage $package): View
    {
        return view('superadmin.packages.edit', [
            'package' => $package->load('menus'),
            'menus' => Menu::query()->orderBy('name')->get(),
            'selectedMenus' => $package->menus->pluck('id')->all(),
            'menuQuantities' => $package->menus->pluck('pivot.quantity', 'id')->all(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, FoodPackage $package): RedirectResponse|JsonResponse
    {
        $data = $this->validatePackage($request, $package->id);
        $packageCategoryId = $this->packageCategoryId();
        
        $syncData = [];
        foreach ($data['menus'] as $menuId) {
            $quantity = $request->input("menu_quantities.{$menuId}", 1);
            $syncData[$menuId] = ['quantity' => $quantity];
        }

        $selectedMenuNames = $this->selectedMenuNames($syncData);

        $package->fill($data);
        $package->description = $selectedMenuNames;
        $package->notes = $data['notes'] ?? null;
        $package->free_item = $data['free_item'] ?? null;
        $package->menu_category_id = $packageCategoryId;
        
        $package->cost_price = $data['cost_price'] ?? $package->cost_price ?? 0;
        
        if (empty($package->code)) {
            $package->code = 'PKG-' . strtoupper(bin2hex(random_bytes(3)));
        }

        if ($request->hasFile('image')) {
            $this->deleteImageIfExists($package->image_path);
            $package->image_path = $this->storeImage($request);
        }

        $package->save();
        $package->menus()->sync($syncData);

        if ($request->expectsJson() || $request->ajax()) {
            $package->load('menus');

            return response()->json([
                'message' => 'Paket berhasil diperbarui.',
                'package' => $this->packagePayload($package),
            ]);
        }

        return redirect()->route('superadmin.packages.index')->with('status', 'Paket berhasil diperbarui.');
    }

    public function destroy(FoodPackage $package): RedirectResponse|JsonResponse
    {
        $this->deleteImageIfExists($package->image_path);
        $package->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['message' => 'Paket berhasil dihapus.']);
        }

        return redirect()->route('superadmin.packages.index')->with('status', 'Paket berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        $packages = FoodPackage::query()->get(['id', 'image_path']);
        foreach ($packages as $package) {
            $this->deleteImageIfExists($package->image_path);
        }
        FoodPackage::query()->delete();

        return redirect()->route('superadmin.packages.index')->with('status', 'Semua paket berhasil dihapus.');
    }

    private function validatePackage(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('food_packages', 'code')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'cropped_image' => ['nullable', 'string'],
            'menus' => ['required', 'array', 'min:1'],
            'menus.*' => ['integer', 'exists:menus,id'],
            'menu_quantities' => ['nullable', 'array'],
            'menu_quantities.*' => ['integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'free_item' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function selectedMenuNames(array $syncData): string
    {
        $menuIds = array_keys($syncData);
        $menus = Menu::query()
            ->whereIn('id', $menuIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $lines = [];
        foreach ($syncData as $id => $pivot) {
            if (isset($menus[$id])) {
                $qty = $pivot['quantity'] ?? 1;
                $lines[] = "({$qty}x) " . $menus[$id]->name;
            }
        }

        return implode(PHP_EOL, $lines);
    }

    private function storeImage(Request $request): ?string
    {
        if ($request->filled('cropped_image')) {
            return \App\Support\CroppedImageStore::store($request->string('cropped_image')->toString(), 'packages', 'package');
        }

        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('packages', 'public');
    }

    private function deleteImageIfExists(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function packagePayload(FoodPackage $package): array
    {
        return [
            'id' => $package->id,
            'code' => $package->code,
            'name' => $package->name,
            'description' => $package->description,
            'notes' => $package->notes,
            'free_item' => $package->free_item,
            'menu_category_id' => $package->menu_category_id,
            'category_name' => $package->category?->name ?? 'Tanpa kategori',
            'selling_price' => (float) $package->selling_price,
            'cost_price' => (float) $package->cost_price,
            'image_url' => $package->image_path && Storage::disk('public')->exists($package->image_path)
                ? Storage::disk('public')->url($package->image_path)
                : asset('images/menu-placeholder.svg'),
            'menus' => $package->menus->map(fn (Menu $menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
                'quantity' => $menu->pivot->quantity ?? 1,
            ])->all(),
        ];
    }

    private function packageCategoryId(): ?int
    {
        return \App\Models\MenuCategory::query()
            ->where('name', self::PACKAGE_CATEGORY)
            ->value('id');
    }
}
