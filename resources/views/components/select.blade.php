@props([
    'disabled' => false,
    'options' => [],
    'itemValue' => 'id',
    'itemText' => 'title',
    'value' => null,
    'blankOption' => true,
    'multiple' => false
])

<select {{ $disabled ? 'disabled' : '' }} {{ $multiple ? 'multiple' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
    @if ($blankOption && !$multiple) 
        <option> </option>
    @endif
    @foreach ($options as $option)
        <option value='{{ $option[$itemValue] }}' {{ $value === $option[$itemValue] || $value->contains($itemValue, $option[$itemValue]) ? 'selected' : '' }}> {{ $option[$itemText] }} </option>
    @endforeach
</select>