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
  <section class="relative min-h-[520px] md:min-h-[640px] flex items-center justify-center overflow-hidden">
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
      <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-blue-800/80 to-blue-900/90"></div>
      <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
        <div class="absolute top-20 right-10 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
      </div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <div class="site-blogs-hero">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">About Us</h1>
        <p class="text-lg md:text-2xl text-blue-100 max-w-3xl mx-auto">
          Learn about LITUS Group, our values, and the diverse businesses we grow together
        </p>
      </div>
    </div>
  </section>

  <section id="about" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"
        x-data="{
          inView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
          }
        }"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
        data-about-hero
      >
        {{-- Image side (ref + isInView drives whole block) --}}
        <div
          class="site-about-motion-left relative transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[50px]'"
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
            class="relative rounded-2xl overflow-hidden shadow-2xl"
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
                        class="w-full min-h-[280px] md:min-h-[420px] h-full object-cover"
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
                class="w-full min-h-[280px] md:min-h-[420px] h-full object-cover"
                loading="lazy"
                decoding="async"
              />
            @else
              <div
                class="w-full min-h-[280px] md:min-h-[420px] bg-gradient-to-br from-gray-200 to-gray-300"
                role="img"
                aria-label="Business partnership image"
              ></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 to-transparent pointer-events-none"></div>
          </div>
          <div
            class="site-about-motion-stat absolute -bottom-8 -right-8 bg-white p-6 rounded-xl shadow-xl hidden lg:block transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
            style="transition-delay: 300ms"
            :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
          >
            <div class="text-4xl font-bold text-blue-600 mb-1">16+</div>
            <div class="text-gray-600 font-medium">Companies</div>
          </div>
        </div>

        {{-- Content side --}}
        <div
          class="site-about-motion-right transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[50px]'"
        >
          <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">About LITUS Group</h2>
          <p class="text-base md:text-lg text-gray-600 mb-6 leading-relaxed">{!! nl2br(e($aboutIntro1)) !!}</p>
          <p class="text-base md:text-lg text-gray-600 mb-8 leading-relaxed">{!! nl2br(e($aboutIntro2)) !!}</p>

          <div class="space-y-4 mb-8">
            @foreach($highlights as $index => $highlight)
              <div
                class="site-about-motion-hl flex items-start gap-3 transition-[opacity,transform] duration-500 ease-out will-change-[opacity,transform]"
                style="transition-delay: {{ 400 + $index * 100 }}ms"
                :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-5'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 shrink-0 mt-1" aria-hidden="true">
                  <circle cx="12" cy="12" r="10" />
                  <path d="m9 12 2 2 4-4" />
                </svg>
                <span class="text-gray-700 text-base md:text-lg">{{ $highlight }}</span>
              </div>
            @endforeach
          </div>

          <a
            href="{{ route('site.home') }}#companies"
            class="site-about-motion-cta inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-opacity duration-[800ms] ease-out shadow-lg hover:shadow-xl"
            style="transition-delay: 800ms"
            :class="inView ? 'opacity-100' : 'opacity-0'"
          >
            Explore Our Companies
          </a>
        </div>
      </div>
    </div>
  </section>

  {{-- Vision & Mission: visionRef on Mission card only; both use visionInView --}}
  <section class="py-24 bg-blue-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-12"
        x-data="{
          visionInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.visionInView = true;
          }
        }"
      >
        <div
          x-intersect.once.margin.-100px.-100px.-100px.-100px="visionInView = true"
          class="site-about-motion-mission bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 will-change-[opacity,transform] hover:bg-white/15 hover:border-white/40 hover:shadow-xl hover:-translate-y-1 cursor-default"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Our Mission</h3>
          <p class="text-base md:text-lg text-blue-100 leading-relaxed">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction. We strive to be the partner of choice for businesses and individuals seeking excellence.
          </p>
        </div>

        <div
          class="site-about-motion-vision bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 will-change-[opacity,transform] hover:bg-white/15 hover:border-white/40 hover:shadow-xl hover:-translate-y-1 cursor-default"
          style="transition-delay: 200ms"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Our Vision</h3>
          <p class="text-base md:text-lg text-blue-100 leading-relaxed">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders while contributing to national economic growth.
          </p>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
