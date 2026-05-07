@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-eazy focus:ring-eazy rounded-lg shadow-sm']) }}>
