@extends('layouts.site')

@section('content')
@php
  $hotelImage = 'https://images.unsplash.com/photo-1744782996368-dc5b7e697f4c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1600';
  $images = array_fill(0, 24, $hotelImage);

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

<div class="min-h-screen bg-white">
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
        @foreach($images as $i => $img)
          <x-site.motion
            :delay="($i % 8) * 50"
            :duration="400"
            variant="scale"
            class="group cursor-pointer {{ $gridClass($i) }}"
          >
            <div class="relative w-full h-full rounded-lg overflow-hidden bg-gray-100">
              <img
                src="{{ $img }}"
                alt="Gallery image {{ $i + 1 }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
            </div>
          </x-site.motion>
        @endforeach
      </div>

      <x-site.motion class="mt-12 text-center" variant="fade-up" :delay="400" :duration="600">
        <p class="text-gray-500 text-sm">{{ count($images) }} photos from this event</p>
      </x-site.motion>
    </div>
  </section>
</div>
@endsection
