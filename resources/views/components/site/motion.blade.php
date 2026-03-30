@props([
    'delay' => 0,
    'duration' => 800,
    'variant' => 'fade-up',
])

@php
    /** Distances / timings align with motion/react + useInView(once, margin: -100px) in src/app/pages/*.tsx */
    $variants = [
        'fade-up' => [
            'from' => 'opacity-0 translate-y-[50px]',
            'to' => 'opacity-100 translate-y-0',
        ],
        'fade-down' => [
            'from' => 'opacity-0 -translate-y-[50px]',
            'to' => 'opacity-100 translate-y-0',
        ],
        'fade-left' => [
            'from' => 'opacity-0 -translate-x-[50px]',
            'to' => 'opacity-100 translate-x-0',
        ],
        'fade-right' => [
            'from' => 'opacity-0 translate-x-[50px]',
            'to' => 'opacity-100 translate-x-0',
        ],
        'fade-left-sm' => [
            'from' => 'opacity-0 -translate-x-[30px]',
            'to' => 'opacity-100 translate-x-0',
        ],
        'fade-right-sm' => [
            'from' => 'opacity-0 translate-x-[30px]',
            'to' => 'opacity-100 translate-x-0',
        ],
        'scale' => [
            'from' => 'opacity-0 scale-90',
            'to' => 'opacity-100 scale-100',
        ],
    ];
    $v = $variants[$variant] ?? $variants['fade-up'];
    $from = $v['from'];
    $to = $v['to'];
    $delayMs = (int) $delay;
    $durationMs = (int) $duration;
@endphp

<div
    data-motion
    x-data="{ shown: false, init() { if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.shown = true } }"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="setTimeout(() => { shown = true }, {{ $delayMs }})"
    x-bind:class="shown ? '{{ $to }}' : '{{ $from }}'"
    class="transition-[opacity,transform] ease-out will-change-[opacity,transform] {{ $attributes->get('class') }}"
    style="transition-duration: {{ $durationMs }}ms"
    {{ $attributes->except('class') }}
>
    {{ $slot }}
</div>
