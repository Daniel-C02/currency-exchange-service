@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'placeholder' => 'Please select an option...',
    'selected' => null,
    'required' => false
])

<div class="w-100">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        class="form-select bg-neutral-7"
        @if($required) required @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Loop through the options array --}}
        @foreach ($options as $key => $value)
            {{-- This handles both simple arrays and key-value pair arrays --}}
            @php
                $optionValue = is_int($key) ? $value : $key;
                $optionDisplay = $value;
            @endphp
            <option value="{{ $optionValue }}" @if($optionValue == old($name, $selected)) selected @endif>
                {{ $optionDisplay }}
            </option>
        @endforeach
    </select>

    <x-forms.error-message name="{{ $name }}" />
</div>
