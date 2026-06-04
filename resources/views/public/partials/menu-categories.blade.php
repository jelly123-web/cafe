<button class="category-btn {{ ($activeFilter ?? 'all') === 'all' ? 'active' : '' }}" type="button" data-filter="all">Semua</button>
@foreach ($categories as $category)
    <button class="category-btn {{ ($activeFilter ?? '') === ('cat-' . $category->id) ? 'active' : '' }}" type="button" data-filter="cat-{{ $category->id }}">{{ $category->name }}</button>
@endforeach
