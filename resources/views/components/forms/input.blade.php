@props([
    'type' => 'text',
    'placeholder' => '',
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => '',
    'disabled' => false
])

<div class="w-100">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        class="form-control bg-neutral-7"
        placeholder="{{ $placeholder }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        @if($autofocus) autofocus @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    >

    <x-forms.error-message name="{{ $name }}" />
</div>
