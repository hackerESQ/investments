@props([
    'disabled' => false,
    'options' => [],
    'itemValue' => 'id',
    'itemText' => 'title',
    'value' => null,
    'blankOption' => true,
    'multiple' => false,
    'mandatorySelection' => [],
])

<select {{ $disabled ? 'disabled' : '' }} {{ $multiple ? 'multiple' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
    @if ($blankOption && !$multiple) 
        <option> </option>
    @endif


    @foreach ($options as $option)
        @if (evaluateMandatory($value, $option, $itemValue, $mandatorySelection))
            <optgroup label="{{ $option[$itemText] }} {{ $mandatorySelection[2] ?? '' }}"></optgroup>
        @else
            <option value='{{ $option[$itemValue] }}' {{ evaluateSelected($value, $option, $itemValue) ? 'selected' : '' }}> {{ $option[$itemText] }} </option>
        @endif
    @endforeach
</select>

@php
    function evaluateMandatory($value, $option, $itemValue, $mandatorySelection) {
        if ($value instanceof \Illuminate\Support\Collection && isset($mandatorySelection[0]) && isset($mandatorySelection [1])) {
            return $value->where($itemValue, $option[$itemValue])->contains($mandatorySelection[0], $mandatorySelection[1]);
        } else {
            return false;
        }
    }

    function evaluateSelected($value, $option, $itemValue) {
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->contains($itemValue, $option[$itemValue]);
        } else {
            return $value === $option[$itemValue];
        }
    }
@endphp