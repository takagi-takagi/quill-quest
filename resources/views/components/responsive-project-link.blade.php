@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block p-0 sm:p-2 bg-white shadow sm:rounded-lg w-full border-l-4 border-indigo-400 text-start text-lg font-medium text-indigo-900 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'block p-0 sm:p-2 bg-white shadow sm:rounded-lg w-full border-l-4 border-transparent text-start text-lg font-medium text-gray-800 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>