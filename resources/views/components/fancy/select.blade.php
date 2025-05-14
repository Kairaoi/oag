@props(['label', 'name', 'options' => [], 'required' => false, 'default' => null, 'placeholder' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-600 mb-1">
        {{ $label }}
    </label>
    <select name="{{ $name }}" id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if($default)
            <option value="{{ $default }}">Me (Default)</option>
        @endif
        @foreach($options as $option)
            <option value="{{ $option->id }}">{{ $option->name }}</option>
        @endforeach
    </select>
</div>
