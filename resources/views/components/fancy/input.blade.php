@props(['type' => 'text', 'label', 'name', 'required' => false])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-600 mb-1">
        {{ $label }}
    </label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
           {{ $required ? 'required' : '' }}
           class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" />
</div>
