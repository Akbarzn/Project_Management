@props(['active' => false])

@php
$base_classes = 'flex items-center mb-2 gap-2 p-2 rounded-lg text-white transition duration-150 ease-in-out w-full';
$active_classes = 'text-indigo-800 bg-indigo-800 font-semibold';
$inactive_classes = 'text-white hover:bg-indigo-50/70 hover:text-indigo-800 ';
$classes = $base_classes . '' . (($active ?? false) ? $active_classes : $inactive_classes);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} >
    {{$slot}}
</a>