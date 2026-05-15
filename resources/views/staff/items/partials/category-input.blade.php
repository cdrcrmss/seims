@php
    $categoryValue = $value ?? old('category', '');
    $inputId = $inputId ?? 'category';
    $listId = $listId ?? 'item-category-options';
@endphp
<input type="text"
       name="category"
       id="{{ $inputId }}"
       list="{{ $listId }}"
       value="{{ $categoryValue }}"
       required
       autocomplete="off"
       placeholder="Type or select a category"
       class="{{ $class ?? 'w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm' }}">
<datalist id="{{ $listId }}">
    @foreach($categories as $cat)
        <option value="{{ $cat }}"></option>
    @endforeach
</datalist>
