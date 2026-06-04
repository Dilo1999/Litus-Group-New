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
    x-data="{
      shown: false,
      _fallback: null,
      reveal() {
        this.shown = true;
        if (this._fallback) {
          clearTimeout(this._fallback);
          this._fallback = null;
        }
      },
      init() {
        const mq = window.matchMedia;
        if (mq('(prefers-reduced-motion: reduce)').matches || mq('(max-width: 767px)').matches) {
          this.reveal();
          return;
        }
        this._fallback = setTimeout(() => this.reveal(), 1200);
      },
      destroy() {
        if (this._fallback) clearTimeout(this._fallback);
      }
    }"
    x-intersect.once.margin.0px.0px.0px.0px="reveal()"
    x-bind:class="shown ? '{{ $to }}' : '{{ $from }}'"
    class="transition-[opacity,transform] ease-out {{ $attributes->get('class') }}"
    style="transition-duration: {{ $durationMs }}ms; transition-delay: {{ $delayMs }}ms"
    {{ $attributes->except('class') }}
>
    {{ $slot }}
</div>
