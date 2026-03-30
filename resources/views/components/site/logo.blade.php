@props([
  'variant' => 'dark', // dark | light — light = on dark bg (invert logo like Navigation on transparent header)
  'size' => 'md',      // sm | md | lg
])

@php
  use App\Support\SiteData;

  $sizes = [
    'sm' => 'h-10',
    'md' => 'h-12',
    'lg' => 'h-16 md:h-20',
  ];
  $h = $sizes[$size] ?? $sizes['md'];
@endphp

<img
  src="{{ SiteData::brandLogoUrl() }}"
  alt="LITUS Group"
  {{ $attributes->merge(['class' => $h . ' w-auto transition-opacity duration-300 ' . ($variant === 'light' ? 'brightness-0 invert' : '')]) }}
/>
