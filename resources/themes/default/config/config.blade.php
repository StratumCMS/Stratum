@foreach($fields as $name => $field)
    <div class="mb-4">
        <label class="block font-semibold mb-1">{{ $field['label'] }}</label>

        @if($field['type'] === 'text')
            <input type="text" name="{{ $name }}" value="{{ old($name, $values[$name] ?? $field['default']) }}"
                   class="input w-full">
        @elseif($field['type'] === 'color')
            <input type="color" name="{{ $name }}" value="{{ old($name, $values[$name] ?? $field['default']) }}">
        @elseif($field['type'] === 'checkbox')
            <input type="checkbox" name="{{ $name }}" value="1"
                {{ (old($name, $values[$name] ?? $field['default']) ? 'checked' : '') }}>
        @endif
    </div>
@endforeach
