@extends('layouts.site')

@section('content')
@php
  $cover = $event['image'] ?? '';
  $fromGallery = $event['gallery_images'] ?? [];
  $fromGallery = is_array($fromGallery) ? array_values(array_filter($fromGallery)) : [];
  if ($fromGallery !== []) {
    $images = $fromGallery;
  } elseif ($cover !== '') {
    $images = array_fill(0, 24, $cover);
  } else {
    $images = [];
  }

  $gridClass = function(int $index) {
    $position = $index % 8;
    return match ($position) {
      0 => 'col-span-2 row-span-2',
      5 => 'col-span-1 row-span-2',
      6 => 'col-span-2 row-span-1',
      default => 'col-span-1 row-span-1',
    };
  };

  $photoLabel = count($images) . ' ' . \Illuminate\Support\Str::plural('photo', count($images));
@endphp

<div
  class="min-h-screen bg-white"
  x-data="{
    open: false,
    index: 0,
    images: @js($images),
    altBase: @js(($event['image_alt'] ?? $event['title']) ?: 'Gallery image'),
    show(i) {
      if (!this.images?.length) return;
      this.index = Math.max(0, Math.min(i, this.images.length - 1));
      this.open = true;
      document.body.classList.add('overflow-hidden');
    },
    close() {
      this.open = false;
      document.body.classList.remove('overflow-hidden');
    },
    next() {
      if (!this.images?.length) return;
      this.index = (this.index + 1) % this.images.length;
    },
    prev() {
      if (!this.images?.length) return;
      this.index = (this.index - 1 + this.images.length) % this.images.length;
    },
    onKey(e) {
      if (!this.open) return;
      if (e.key === 'Escape') this.close();
      if (e.key === 'ArrowRight') this.next();
      if (e.key === 'ArrowLeft') this.prev();
    },
  }"
  x-on:keydown.window="onKey($event)"
>
  <section class="ui-page-hero">
    <div class="absolute inset-0 -z-10" aria-hidden="true">
      @if($cover !== '')
        <img src="{{ $cover }}" alt="" class="h-full w-full object-cover opacity-50" fetchpriority="high" decoding="async" />
      @endif
      <div class="ui-page-hero__glow"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/65 to-ink-950/30"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-900 via-ink-900/60 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/70 to-transparent"></div>
    </div>
    <div class="ui-container">
      <x-site.motion variant="fade-up" :duration="600">
        <a
          href="{{ route('site.blogs') }}"
          class="group mb-10 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-white/70 transition-colors hover:text-white"
        >
          <span class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-md transition-all group-hover:-translate-x-0.5 group-hover:bg-white/20" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </span>
          Back to News & Media
        </a>

        <div class="grid grid-cols-1 items-end gap-8 lg:grid-cols-12">
          <div class="lg:col-span-8">
            <span class="ui-eyebrow ui-eyebrow--light">Event gallery</span>
            <h1 class="mt-6 text-[2.4rem] leading-[1.05] font-extrabold tracking-[-0.035em] text-white [overflow-wrap:anywhere] sm:text-5xl md:text-6xl lg:text-7xl">{{ $event['title'] }}</h1>
            @if(filled($event['description'] ?? null))
              <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg md:text-xl">{{ $event['description'] }}</p>
            @endif
          </div>
          <div class="flex flex-wrap gap-2 lg:col-span-4 lg:justify-end">
            @if(filled($event['date'] ?? null))
              <span class="ui-chip ui-chip--dark !py-1.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-lagoon-300" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                {{ $event['date'] }}
              </span>
            @endif
            @if(count($images) > 0)
              <span class="ui-chip ui-chip--dark !py-1.5 tabular-nums">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lagoon-300" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                {{ $photoLabel }}
              </span>
            @endif
          </div>
        </div>
      </x-site.motion>
    </div>
  </section>

  <section class="bg-sand-100 py-12 md:py-20">
    <div class="ui-container">
      <div class="grid grid-flow-dense grid-cols-2 auto-rows-[150px] gap-3 sm:auto-rows-[200px] md:grid-cols-4 md:gap-4 lg:auto-rows-[240px]">
        @forelse($images as $i => $img)
          <x-site.motion
            :delay="($i % 8) * 50"
            :duration="400"
            variant="scale"
            class="group cursor-pointer {{ $gridClass($i) }}"
          >
            <button
              type="button"
              class="block h-full w-full rounded-2xl text-left focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500 md:rounded-3xl"
              x-on:click="show({{ $i }})"
              aria-label="Open image {{ $i + 1 }}"
            >
              <div class="ui-zoom relative h-full w-full overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-lagoon-100 shadow-[0_1px_2px_rgba(6,22,52,0.05),0_12px_30px_-16px_rgba(6,22,52,0.35)] md:rounded-3xl">
                <img
                  src="{{ $img }}"
                  alt="{{ ($event['image_alt'] ?? $event['title']) }} — {{ $i + 1 }}"
                  class="h-full w-full object-cover"
                  loading="lazy"
                  decoding="async"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-ink-950/60 via-ink-950/0 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <span class="absolute right-3 bottom-3 flex h-10 w-10 translate-y-2 items-center justify-center rounded-full bg-white/90 text-ink-900 opacity-0 shadow-lg backdrop-blur-md transition-all duration-500 ease-out-expo group-hover:translate-y-0 group-hover:opacity-100" aria-hidden="true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M9 21H3v-6"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
                </span>
              </div>
            </button>
          </x-site.motion>
        @empty
          <div class="col-span-full row-span-2 flex flex-col items-center justify-center rounded-3xl border border-ink-100 bg-white px-6 py-16 text-center">
            <div class="ui-icon-tile">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
            <p class="mt-5 text-ink-500">No gallery images have been added for this event yet.</p>
          </div>
        @endforelse
      </div>

      <x-site.motion class="mt-12 flex flex-col items-center gap-5 text-center md:mt-16" variant="fade-up" :delay="400" :duration="600">
        @if(count($images) > 0)
          <p class="text-sm text-ink-500 tabular-nums">{{ $photoLabel }} from this event</p>
        @endif
        <a href="{{ route('site.blogs') }}#gallery" class="ui-btn ui-btn--outline ui-btn--sm min-h-11">
          More events
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </x-site.motion>
    </div>
  </section>

  {{-- Lightbox (Google Drive-style viewer) --}}
  <div
    x-cloak
    x-show="open"
    x-transition.opacity.duration.150ms
    class="fixed inset-0 z-[70] flex items-center justify-center bg-ink-950/95 backdrop-blur-md"
    role="dialog"
    aria-modal="true"
    aria-label="Image viewer"
    x-on:click.self="close()"
  >
    <div class="relative flex h-full max-h-[92vh] w-full max-w-6xl items-center justify-center px-4 sm:px-6 lg:px-8">
      <button
        type="button"
        class="absolute top-4 right-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-lagoon-300"
        x-on:click="close()"
        aria-label="Close viewer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
      </button>

      <button
        type="button"
        x-show="images.length > 1"
        class="absolute top-1/2 left-2 z-10 inline-flex h-12 min-h-[44px] w-12 min-w-[44px] -translate-y-1/2 touch-manipulation items-center justify-center rounded-full border border-white/15 bg-white/10 text-white backdrop-blur-md transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-lagoon-300 active:bg-white/30 sm:left-3 sm:h-11 sm:w-11"
        x-on:click.stop="prev()"
        aria-label="Previous image"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </button>

      <div class="flex h-full w-full items-center justify-center">
        <img
          x-bind:src="images[index]"
          x-bind:alt="`${altBase} — ${index + 1}`"
          class="max-h-[82vh] w-auto max-w-full rounded-2xl object-contain shadow-[0_40px_120px_-20px_rgba(0,0,0,0.8)]"
          x-on:click.stop
        />
      </div>

      <button
        type="button"
        x-show="images.length > 1"
        class="absolute top-1/2 right-2 z-10 inline-flex h-12 min-h-[44px] w-12 min-w-[44px] -translate-y-1/2 touch-manipulation items-center justify-center rounded-full border border-white/15 bg-white/10 text-white backdrop-blur-md transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-lagoon-300 active:bg-white/30 sm:right-3 sm:h-11 sm:w-11"
        x-on:click.stop="next()"
        aria-label="Next image"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </button>

      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-sm font-semibold text-white/85 tabular-nums backdrop-blur-md">
        <span x-text="`${index + 1} / ${images.length}`"></span>
      </div>
    </div>
  </div>
</div>
@endsection
