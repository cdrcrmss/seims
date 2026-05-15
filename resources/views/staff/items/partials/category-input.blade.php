@php
    $categoryValue = $value ?? old('category', '');
    $selectId = $inputId ?? 'category';
    $categoryOptions = collect($categories);
    if ($categoryValue !== '' && !$categoryOptions->contains($categoryValue)) {
        $categoryOptions = $categoryOptions->push($categoryValue)->sort()->values();
    }
@endphp
<select name="category"
        id="{{ $selectId }}"
        required
        class="{{ $class ?? 'w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-colors text-sm' }}">
    <option value="">Select category</option>
    @foreach($categoryOptions as $cat)
        <option value="{{ $cat }}" @selected($categoryValue === $cat)>{{ $cat }}</option>
    @endforeach
</select>
