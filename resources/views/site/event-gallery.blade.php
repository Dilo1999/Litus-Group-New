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
  <section class="pt-32 pb-12 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion variant="fade-up" :duration="600">
        <a
          href="{{ route('site.blogs') }}"
          class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-8 transition-colors"
        >
          <span>←</span>
          Back to News & Media
        </a>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">{{ $event['title'] }}</h1>
        <p class="text-xl text-blue-100 mb-2">{{ $event['date'] }}</p>
        <p class="text-lg text-blue-200 max-w-3xl">{{ $event['description'] }}</p>
      </x-site.motion>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 auto-rows-[200px]">
        @forelse($images as $i => $img)
          <x-site.motion
            :delay="($i % 8) * 50"
            :duration="400"
            variant="scale"
            class="group cursor-pointer {{ $gridClass($i) }}"
          >
            <button
              type="button"
              class="block w-full h-full text-left"
              x-on:click="show({{ $i }})"
              aria-label="Open image {{ $i + 1 }}"
            >
              <div class="relative w-full h-full rounded-lg overflow-hidden bg-gray-100">
                <img
                  src="{{ $img }}"
                  alt="{{ ($event['image_alt'] ?? $event['title']) }} — {{ $i + 1 }}"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
              </div>
            </button>
          </x-site.motion>
        @empty
          <p class="col-span-full text-center text-gray-500 py-12">No gallery images have been added for this event yet.</p>
        @endforelse
      </div>

      <x-site.motion class="mt-12 text-center" variant="fade-up" :delay="400" :duration="600">
        @if(count($images) > 0)
          <p class="text-gray-500 text-sm">{{ count($images) }} {{ \Illuminate\Support\Str::plural('photo', count($images)) }} from this event</p>
        @endif
      </x-site.motion>
    </div>
  </section>

  {{-- Lightbox (Google Drive-style viewer) --}}
  <div
    x-cloak
    x-show="open"
    x-transition.opacity.duration.150ms
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
    role="dialog"
    aria-modal="true"
    aria-label="Image viewer"
    x-on:click.self="close()"
  >
    <div class="relative w-full h-full max-w-6xl max-h-[92vh] px-4 sm:px-6 lg:px-8 flex items-center justify-center">
      <button
        type="button"
        class="absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white transition"
        x-on:click="close()"
        aria-label="Close viewer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
      </button>

      <button
        type="button"
        x-show="images.length > 1"
        class="inline-flex absolute left-2 sm:left-3 top-1/2 z-10 -translate-y-1/2 items-center justify-center min-h-[44px] min-w-[44px] w-12 h-12 sm:h-11 sm:w-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 text-white transition touch-manipulation"
        x-on:click.stop="prev()"
        aria-label="Previous image"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </button>

      <div class="w-full h-full flex items-center justify-center">
        <img
          x-bind:src="images[index]"
          x-bind:alt="`${altBase} — ${index + 1}`"
          class="max-h-[92vh] w-auto max-w-full object-contain rounded-md shadow-2xl"
          x-on:click.stop
        />
      </div>

      <button
        type="button"
        x-show="images.length > 1"
        class="inline-flex absolute right-2 sm:right-3 top-1/2 z-10 -translate-y-1/2 items-center justify-center min-h-[44px] min-w-[44px] w-12 h-12 sm:h-11 sm:w-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 text-white transition touch-manipulation"
        x-on:click.stop="next()"
        aria-label="Next image"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </button>

      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/80 text-sm bg-white/10 rounded-full px-3 py-1">
        <span x-text="`${index + 1} / ${images.length}`"></span>
      </div>
    </div>
  </div>
</div>
@endsection
