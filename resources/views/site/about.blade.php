@extends('layouts.site')

@section('content')
@php
  $highlights = [
    'Established presence across multiple industries',
    'Committed to quality and customer satisfaction',
    'Innovative solutions and cutting-edge technology',
    'Dedicated team of industry professionals',
  ];
@endphp

{{-- Matches src/app/pages/AboutPage.tsx + src/app/components/About.tsx --}}
<div class="pt-20">
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
          <div class="relative rounded-2xl overflow-hidden shadow-2xl">
            <img
              src="https://images.unsplash.com/photo-1745847768380-2caeadbb3b71?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXNpbmVzcyUyMGhhbmRzaGFrZSUyMHBhcnRuZXJzaGlwfGVufDF8fHx8MTc3MTYyMTc3OXww&ixlib=rb-4.1.0&q=80&w=1080"
              alt="Business partnership"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 to-transparent"></div>
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
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">About LITUS Group</h2>
          <p class="text-lg text-gray-600 mb-6 leading-relaxed">
            LITUS Group is a diversified business conglomerate with a strong presence
            across multiple sectors including hospitality, construction, automotive,
            technology, and trading. Our commitment to excellence drives everything we do.
          </p>
          <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            With a portfolio spanning from luxury hotels and resorts to cutting-edge
            technology solutions, we deliver comprehensive services that meet the evolving
            needs of our clients. Our diverse businesses work in synergy to create value
            and drive sustainable growth.
          </p>

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
                <span class="text-gray-700 text-lg">{{ $highlight }}</span>
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
          class="site-about-motion-mission bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="text-3xl font-bold text-white mb-4">Our Mission</h3>
          <p class="text-lg text-blue-100 leading-relaxed">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction. We strive to be the partner of choice for businesses and individuals seeking excellence.
          </p>
        </div>

        <div
          class="site-about-motion-vision bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <h3 class="text-3xl font-bold text-white mb-4">Our Vision</h3>
          <p class="text-lg text-blue-100 leading-relaxed">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders while contributing to national economic growth.
          </p>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
