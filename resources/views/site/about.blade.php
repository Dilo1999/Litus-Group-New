@extends('layouts.site')

@section('content')
@php
  $highlights = [
    'Established presence across multiple industries',
    'Committed to quality and customer satisfaction',
    'Innovative solutions and cutting-edge technology',
    'Dedicated team of industry professionals',
  ];
  $heroImagePath = \App\Models\SiteSetting::getValue('about.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('about.hero.position_y', 50);
  $aboutIntro1 = \App\Models\SiteSetting::getValue(
    'about.intro.paragraph_1',
    'LITUS Group is a diversified business conglomerate with a strong presence across multiple sectors including hospitality, construction, automotive, technology, and trading. Our commitment to excellence drives everything we do.'
  );
  $aboutIntro2 = \App\Models\SiteSetting::getValue(
    'about.intro.paragraph_2',
    'With a portfolio spanning from luxury hotels and resorts to cutting-edge technology solutions, we deliver comprehensive services that meet the evolving needs of our clients. Our diverse businesses work in synergy to create value and drive sustainable growth.'
  );
@endphp

{{-- Matches src/app/pages/AboutPage.tsx + src/app/components/About.tsx --}}
<div>
  <section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-[640px]">
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="About Us hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
      @else
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900"></div>
      @endif
      <div class="absolute inset-0 bg-gradient-to-b from-blue-950/45 via-blue-900/30 to-blue-950/15 md:bg-gradient-to-r md:from-blue-900/90 md:via-blue-800/80 md:to-transparent"></div>
    </div>
    <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-20 md:py-24 lg:px-8">
      <div class="site-blogs-hero mx-auto max-w-3xl">
        <h1 class="mb-4 text-3xl font-bold text-white max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-6xl md:[text-shadow:none]">About Us</h1>
        <p class="mx-auto text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:text-lg md:max-w-3xl md:text-2xl md:[text-shadow:none]">
          Learn about LITUS Group, our values, and the diverse businesses we grow together
        </p>
      </div>
    </div>
  </section>

  <section id="about" class="bg-gray-50 py-14 md:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12"
        x-data="{
          inView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
            else if (window.matchMedia('(max-width: 767px)').matches) this.inView = true;
          }
        }"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
        data-about-hero
      >
        {{-- Content: first on mobile, right column on desktop --}}
        <div
          class="site-about-motion-right order-1 text-center transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:order-2 md:text-left md:will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 max-md:translate-y-[30px] md:translate-x-[50px]'"
        >
          <h2 class="mb-4 text-2xl font-bold text-gray-900 sm:mb-6 sm:text-3xl md:text-5xl">About LITUS Group</h2>
          <p class="mb-4 text-base leading-relaxed text-gray-600 sm:mb-6 md:text-lg">{!! nl2br(e($aboutIntro1)) !!}</p>
          <p class="mb-6 text-base leading-relaxed text-gray-600 sm:mb-8 md:text-lg">{!! nl2br(e($aboutIntro2)) !!}</p>

          <div class="mb-6 space-y-3 text-left sm:mb-8 sm:space-y-4">
            @foreach($highlights as $index => $highlight)
              <div
                class="site-about-motion-hl flex items-start gap-2.5 transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto sm:gap-3 md:will-change-[opacity,transform]"
                style="transition-delay: {{ 400 + $index * 100 }}ms"
                :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-5'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                  <circle cx="12" cy="12" r="10" />
                  <path d="m9 12 2 2 4-4" />
                </svg>
                <span class="text-sm text-gray-700 sm:text-base md:text-lg">{{ $highlight }}</span>
              </div>
            @endforeach
          </div>

          <div class="flex justify-center md:justify-start">
            <a
              href="{{ route('site.home') }}#companies"
              class="site-about-motion-cta site-cta-btn inline-flex w-full max-w-sm items-center justify-center rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg transition-opacity duration-[800ms] ease-out hover:bg-blue-700 hover:shadow-xl md:w-auto md:px-8 md:py-4 md:text-lg"
              style="transition-delay: 800ms"
              :class="inView ? 'opacity-100' : 'opacity-0'"
            >
              Explore Our Companies
            </a>
          </div>
        </div>

        {{-- Image: second on mobile, left column on desktop --}}
        <div
          class="site-about-motion-left relative order-2 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:order-1 md:will-change-[opacity,transform]"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 max-md:translate-y-[30px] md:-translate-x-[50px]'"
        >
          @php
            $aboutPartnershipPaths = \App\Models\SiteSetting::aboutPartnershipImagePaths();
            $aboutPartnershipUrls = collect($aboutPartnershipPaths)
              ->map(fn (string $path) => \Illuminate\Support\Facades\Storage::disk('public')->url($path))
              ->values()
              ->all();
            $aboutPartnershipSlideCount = count($aboutPartnershipUrls);
          @endphp
          <div
            @if($aboutPartnershipSlideCount > 1)
              x-data="aboutPartnershipSlider(@js($aboutPartnershipUrls))"
            @endif
            class="group relative overflow-hidden rounded-2xl shadow-2xl transition-all duration-300 md:hover:-translate-y-1 md:hover:shadow-2xl"
          >
            @if($aboutPartnershipSlideCount > 1)
              <div class="overflow-hidden">
                <div
                  class="about-partnership-slider-track flex"
                  :style="{ transform: slideTransform }"
                  role="group"
                  aria-roledescription="carousel"
                  :aria-label="'Business partnership images, slide ' + (activeIndex + 1) + ' of ' + slides.length"
                >
                  <template x-for="(src, slideIdx) in slides" :key="slideIdx">
                    <div class="about-partnership-slider-slide relative shrink-0 grow-0 basis-full">
                      <img
                        :src="src"
                        alt=""
                        class="h-full min-h-[220px] w-full object-cover sm:min-h-[280px] md:min-h-[420px]"
                        :fetchpriority="slideIdx === 0 ? 'high' : 'low'"
                        loading="lazy"
                        decoding="async"
                      />
                    </div>
                  </template>
                </div>
              </div>
              <div class="absolute bottom-4 left-0 right-0 z-10 flex justify-center gap-2 pointer-events-none">
                <template x-for="(_, dotIdx) in slides" :key="'dot-' + dotIdx">
                  <button
                    type="button"
                    class="pointer-events-auto h-2 rounded-full transition-all duration-300"
                    :class="activeIndex === dotIdx ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
                    :aria-label="'Go to slide ' + (dotIdx + 1)"
                    :aria-current="activeIndex === dotIdx ? 'true' : 'false'"
                    @click="goTo(dotIdx)"
                  ></button>
                </template>
              </div>
            @elseif($aboutPartnershipSlideCount === 1)
              <img
                src="{{ $aboutPartnershipUrls[0] }}"
                alt="Business partnership"
                class="h-full min-h-[220px] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105 sm:min-h-[280px] md:min-h-[420px]"
                loading="lazy"
                decoding="async"
              />
            @else
              <div
                class="min-h-[220px] w-full bg-gradient-to-br from-gray-200 to-gray-300 sm:min-h-[280px] md:min-h-[420px]"
                role="img"
                aria-label="Business partnership image"
              ></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 to-transparent pointer-events-none"></div>
          </div>
          <div
            class="site-about-motion-stat absolute -bottom-8 -right-8 hidden rounded-xl bg-white p-6 shadow-xl transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform] lg:block"
            style="transition-delay: 300ms"
            :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
          >
            <div class="mb-1 text-4xl font-bold text-blue-600">16+</div>
            <div class="font-medium text-gray-600">Companies</div>
          </div>
          <div
            class="site-about-motion-stat mx-auto mt-4 max-w-xs rounded-xl bg-white px-6 py-4 text-center shadow-xl transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:hidden"
            style="transition-delay: 300ms"
            :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
          >
            <div class="text-3xl font-bold text-blue-600">16+</div>
            <div class="text-sm font-medium text-gray-600">Companies</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Vision & Mission: visionRef on Mission card only; both use visionInView --}}
  <section class="bg-blue-600 py-14 md:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-12"
        x-data="{
          visionInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.visionInView = true;
            else if (window.matchMedia('(max-width: 767px)').matches) this.visionInView = true;
          }
        }"
      >
        <div
          x-intersect.once.margin.-100px.-100px.-100px.-100px="visionInView = true"
          class="site-about-motion-mission cursor-default rounded-2xl border border-white/20 bg-white/10 p-6 text-center backdrop-blur-sm transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-8 md:text-left md:will-change-[opacity,transform] md:hover:-translate-y-1 md:hover:border-white/40 md:hover:bg-white/15 md:hover:shadow-xl"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="mb-3 text-xl font-bold text-white sm:mb-4 sm:text-2xl md:text-3xl">Our Mission</h3>
          <p class="text-base leading-relaxed text-blue-100 md:text-lg">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction. We strive to be the partner of choice for businesses and individuals seeking excellence.
          </p>
        </div>

        <div
          class="site-about-motion-vision cursor-default rounded-2xl border border-white/20 bg-white/10 p-6 text-center backdrop-blur-sm transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-8 md:text-left md:will-change-[opacity,transform] md:hover:-translate-y-1 md:hover:border-white/40 md:hover:bg-white/15 md:hover:shadow-xl"
          style="transition-delay: 200ms"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="mb-3 text-xl font-bold text-white sm:mb-4 sm:text-2xl md:text-3xl">Our Vision</h3>
          <p class="text-base leading-relaxed text-blue-100 md:text-lg">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders while contributing to national economic growth.
          </p>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
