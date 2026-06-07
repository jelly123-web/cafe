<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\FoodPackage;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SuperadminPromoController extends Controller
{
    public function index(): View|JsonResponse
    {
        $query = Promo::with(['menus', 'foodPackages'])->latest();

        if (request()->ajax()) {
            $promos = $query->get();
            return response()->json([
                'promos' => $promos->map(fn($p) => $this->promoPayload($p)),
                'total' => $promos->count(),
            ]);
        }

        $promos = $query->paginate(10);
        
        return view('superadmin.promos.index', [
            'promos' => $promos,
            'menus' => Menu::query()->orderBy('name')->get(),
            'packages' => FoodPackage::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()
            ->route('superadmin.promos.index')
            ->with('status', 'Form promo dibuka dari halaman daftar promo.');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatePromo($request);
        
        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('promos', 'public');
        }

        $promo = Promo::create($data);

        if ($data['type'] === 'buy_x_get_y') {
            $this->syncBuyXGetYTargets($promo, $data);
        } elseif ($data['applies_to'] === 'specific') {
            $promo->menus()->sync($request->input('menu_ids', []));
            $promo->foodPackages()->sync($request->input('package_ids', []));
            $promo->update([
                'buy_qty' => 0,
                'get_qty' => 0,
                'buy_targets' => [],
                'get_targets' => [],
            ]);
        } else {
            $promo->menus()->detach();
            $promo->foodPackages()->detach();
            $promo->update([
                'buy_qty' => 0,
                'get_qty' => 0,
                'buy_targets' => [],
                'get_targets' => [],
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Promo berhasil ditambahkan.',
                'promo' => $this->promoPayload($promo->load(['menus', 'foodPackages'])),
            ]);
        }

        return redirect()->route('superadmin.promos.index')->with('status', 'Promo berhasil ditambahkan.');
    }

    public function edit(Promo $promo): RedirectResponse
    {
        return redirect()
            ->route('superadmin.promos.index')
            ->with('status', "Edit promo {$promo->name} dilakukan dari halaman daftar promo.");
    }

    public function update(Request $request, Promo $promo): RedirectResponse|JsonResponse
    {
        $data = $this->validatePromo($request, $promo->id);

        if ($request->hasFile('banner')) {
            if ($promo->banner_path) {
                Storage::disk('public')->delete($promo->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('promos', 'public');
        }

        $promo->update($data);

        if ($data['type'] === 'buy_x_get_y') {
            $this->syncBuyXGetYTargets($promo, $data);
        } elseif ($data['applies_to'] === 'specific') {
            $promo->menus()->sync($request->input('menu_ids', []));
            $promo->foodPackages()->sync($request->input('package_ids', []));
            $promo->update([
                'buy_qty' => 0,
                'get_qty' => 0,
                'buy_targets' => [],
                'get_targets' => [],
            ]);
        } else {
            $promo->menus()->detach();
            $promo->foodPackages()->detach();
            $promo->update([
                'buy_qty' => 0,
                'get_qty' => 0,
                'buy_targets' => [],
                'get_targets' => [],
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Promo berhasil diperbarui.',
                'promo' => $this->promoPayload($promo->load(['menus', 'foodPackages'])),
            ]);
        }

        return redirect()->route('superadmin.promos.index')->with('status', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse|JsonResponse
    {
        if ($promo->banner_path) {
            Storage::disk('public')->delete($promo->banner_path);
        }
        $promo->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Promo berhasil dihapus.']);
        }

        return redirect()->route('superadmin.promos.index')->with('status', 'Promo berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        $promos = Promo::all();
        foreach ($promos as $promo) {
            if ($promo->banner_path) {
                Storage::disk('public')->delete($promo->banner_path);
            }
            $promo->delete();
        }

        return redirect()->route('superadmin.promos.index')->with('status', 'Semua promo berhasil dihapus.');
    }

    private function promoPayload(Promo $promo): array
    {
        $buyTargets = $this->hydratePromoTargets($promo->buy_targets ?? []);
        $getTargets = $this->hydratePromoTargets($promo->get_targets ?? []);

        return [
            'id' => $promo->id,
            'name' => $promo->name,
            'description' => $promo->description,
            'type' => $promo->type,
            'applies_to' => $promo->applies_to,
            'value' => (float) $promo->value,
            'min_spend' => (float) $promo->min_spend,
            'buy_qty' => (int) $promo->buy_qty,
            'get_qty' => (int) $promo->get_qty,
            'buy_targets' => $buyTargets,
            'get_targets' => $getTargets,
            'is_active' => $promo->is_active,
            'start_at' => $promo->start_at?->format('Y-m-d'),
            'end_at' => $promo->end_at?->format('Y-m-d'),
            'start_at_label' => $promo->start_at?->format('d/m/y'),
            'end_at_label' => $promo->end_at?->format('d/m/y'),
            'banner_url' => $promo->banner_path ? Storage::disk('public')->url($promo->banner_path) : null,
            'menu_ids' => $promo->menus->pluck('id')->all(),
            'package_ids' => $promo->foodPackages->pluck('id')->all(),
            'menu_count' => $promo->menus->count(),
            'package_count' => $promo->foodPackages->count(),
            'items' => array_values(array_merge($buyTargets, $getTargets)),
        ];
    }

    private function validatePromo(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:percentage,fixed_discount,buy_x_get_y'],
            'applies_to' => ['required', 'in:all,specific'],
            'value' => ['required_if:type,percentage,fixed_discount', 'nullable', 'numeric', 'min:0'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'buy_qty' => ['nullable', 'integer', 'min:0'],
            'get_qty' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'banner' => ['nullable', 'image', 'max:2048'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['exists:menus,id'],
            'package_ids' => ['nullable', 'array'],
            'package_ids.*' => ['exists:food_packages,id'],
            'buy_targets' => ['nullable', 'array'],
            'buy_targets.*.kind' => ['nullable', 'in:menu,package'],
            'buy_targets.*.id' => ['nullable', 'integer'],
            'buy_targets.*.qty' => ['nullable', 'integer', 'min:0'],
            'get_targets' => ['nullable', 'array'],
            'get_targets.*.kind' => ['nullable', 'in:menu,package'],
            'get_targets.*.id' => ['nullable', 'integer'],
            'get_targets.*.qty' => ['nullable', 'integer', 'min:0'],
        ]);

        $selectedMenus = collect($request->input('menu_ids', []))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values();

        $selectedPackages = collect($request->input('package_ids', []))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values();

        if (
            ($data['type'] ?? null) !== 'buy_x_get_y'
            && ($data['applies_to'] ?? null) === 'specific'
            && $selectedMenus->isEmpty()
            && $selectedPackages->isEmpty()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'menu_ids' => 'Pilih minimal 1 menu atau paket untuk promo produk tertentu.',
            ]);
        }

        $buyTargets = $this->normalizeTargets($request->input('buy_targets', []));
        $getTargets = $this->normalizeTargets($request->input('get_targets', []));

        if (($data['type'] ?? null) === 'buy_x_get_y') {
            if (empty($buyTargets) || empty($getTargets)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'buy_targets' => 'Untuk promo beli x gratis y, isi minimal 1 item beli dan 1 item gratis.',
                ]);
            }

            $data['applies_to'] = 'specific';
            $data['value'] = 0;
            $data['min_spend'] = 0;
        }

        if (($data['type'] ?? null) === 'fixed_discount') {
            $data['min_spend'] = 0;
        }

        $data['value'] = isset($data['value']) && $data['value'] !== null ? (float) $data['value'] : 0;
        $data['min_spend'] = isset($data['min_spend']) && $data['min_spend'] !== null ? (float) $data['min_spend'] : 0;
        $data['buy_qty'] = ($data['type'] ?? null) === 'buy_x_get_y'
            ? collect($buyTargets)->sum('qty')
            : (isset($data['buy_qty']) && $data['buy_qty'] !== null ? (int) $data['buy_qty'] : 0);
        $data['get_qty'] = ($data['type'] ?? null) === 'buy_x_get_y'
            ? collect($getTargets)->sum('qty')
            : (isset($data['get_qty']) && $data['get_qty'] !== null ? (int) $data['get_qty'] : 0);
        $data['buy_targets'] = $buyTargets;
        $data['get_targets'] = $getTargets;
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        return $data;
    }

    private function normalizeTargets(array $targets): array
    {
        return collect($targets)
            ->map(function ($target) {
                $kind = $target['kind'] ?? null;
                $id = isset($target['id']) ? (int) $target['id'] : 0;
                $qty = isset($target['qty']) ? (int) $target['qty'] : 0;

                if (!in_array($kind, ['menu', 'package'], true) || $id <= 0 || $qty <= 0) {
                    return null;
                }

                return [
                    'kind' => $kind,
                    'id' => $id,
                    'qty' => $qty,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function syncBuyXGetYTargets(Promo $promo, array $data): void
    {
        $buyTargets = collect($data['buy_targets'] ?? []);

        $promo->menus()->sync(
            $buyTargets->where('kind', 'menu')->pluck('id')->values()->all()
        );

        $promo->foodPackages()->sync(
            $buyTargets->where('kind', 'package')->pluck('id')->values()->all()
        );

        $promo->update([
            'buy_qty' => (int) collect($data['buy_targets'] ?? [])->sum('qty'),
            'get_qty' => (int) collect($data['get_targets'] ?? [])->sum('qty'),
            'buy_targets' => $data['buy_targets'] ?? [],
            'get_targets' => $data['get_targets'] ?? [],
        ]);
    }

    private function hydratePromoTargets(array $targets): array
    {
        $targetCollection = collect($targets)->filter(fn ($target) => is_array($target));
        if ($targetCollection->isEmpty()) {
            return [];
        }

        $menuMap = Menu::query()
            ->whereIn('id', $targetCollection->where('kind', 'menu')->pluck('id')->all())
            ->get(['id', 'name'])
            ->keyBy('id');

        $packageMap = FoodPackage::query()
            ->whereIn('id', $targetCollection->where('kind', 'package')->pluck('id')->all())
            ->get(['id', 'name'])
            ->keyBy('id');

        return $targetCollection
            ->map(function ($target) use ($menuMap, $packageMap) {
                $id = (int) ($target['id'] ?? 0);
                $kind = $target['kind'] ?? 'menu';
                $source = $kind === 'package' ? $packageMap->get($id) : $menuMap->get($id);

                return [
                    'kind' => $kind,
                    'id' => $id,
                    'qty' => (int) ($target['qty'] ?? 0),
                    'name' => $source?->name ?? 'Item dihapus',
                ];
            })
            ->values()
            ->all();
    }
}
