<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\View\View;

class PublicTableMenuController extends Controller
{
    public function show(DiningTable $table): View
    {
        abort_unless($table->is_active, 404);

        $categories = MenuCategory::query()
            ->with(['menus' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $menus = Menu::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('public.table-menu', [
            'table' => $table,
            'categories' => $categories,
            'menus' => $menus,
        ]);
    }
}
