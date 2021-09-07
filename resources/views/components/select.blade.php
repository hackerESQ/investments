@props([
    'disabled' => false,
    'options' => [],
    'itemValue' => 'id',
    'itemText' => 'title',
    'value' => null,
    'blankOption' => true
])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
    @if ($blankOption) 
        <option> </option>
    @endif
    @foreach ($options as $option)
        <option value='{{ $option[$itemValue] }}' {{ $value == $option[$itemValue] ? 'selected' : '' }}> {{ $option[$itemText] }} </option>
    @endforeach
</select>
