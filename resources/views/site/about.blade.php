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

  $aboutPartnershipPaths = \App\Models\SiteSetting::aboutPartnershipImagePaths();
  $aboutPartnershipUrls = collect($aboutPartnershipPaths)
    ->map(fn (string $path) => \Illuminate\Support\Facades\Storage::disk('public')->url($path))
    ->values()
    ->all();
  $aboutPartnershipSlideCount = count($aboutPartnershipUrls);

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
@endphp

{{-- Matches src/app/pages/AboutPage.tsx + src/app/components/About.tsx --}}
<div>
  {{-- ============================== HERO ============================== --}}
  <section class="relative isolate flex min-h-[560px] flex-col overflow-hidden bg-ink-950 text-white md:min-h-[680px]">
    <div class="absolute inset-0 -z-10">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="About Us hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
        <div class="absolute inset-0 bg-ink-950/45"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/10"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/70 to-transparent"></div>
      @else
        <div class="ui-page-hero__glow"></div>
        <div class="ui-grid-texture--light absolute inset-0 opacity-60"></div>
      @endif
    </div>

    <div class="ui-container flex flex-1 flex-col justify-end pt-36 pb-12 md:pt-44 md:pb-16">
      <div class="site-blogs-hero max-w-3xl">
        <span class="ui-eyebrow ui-eyebrow--light">About LITUS Group</span>
        <h1 class="ui-h1 mt-6 text-white">
          About <span class="ui-accent text-lagoon-300">us</span>
        </h1>
        <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-200 sm:text-lg md:mt-8 md:text-xl">
          Learn about LITUS Group, our values, and the diverse businesses we grow together
        </p>
      </div>

      <div class="site-blogs-hero mt-12 flex flex-col gap-6 border-t border-white/15 pt-6 sm:flex-row sm:items-end sm:justify-between md:mt-16 md:pt-8">
        <dl class="flex items-end gap-4">
          <dd class="text-4xl font-extrabold tracking-tight text-white tabular-nums md:text-5xl">16+</dd>
          <dt class="pb-1 text-sm leading-snug text-white/65">Companies under<br class="hidden sm:block" /> one group</dt>
        </dl>
        <div class="flex flex-wrap gap-2">
          <span class="ui-chip ui-chip--dark">Hospitality</span>
          <span class="ui-chip ui-chip--dark">Construction</span>
          <span class="ui-chip ui-chip--dark">Automotive</span>
          <span class="ui-chip ui-chip--dark">Technology</span>
          <span class="ui-chip ui-chip--dark">Trading</span>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================== STORY ============================== --}}
  <section id="about" class="ui-section relative overflow-hidden bg-white">
    <div class="ui-grid-texture pointer-events-none absolute inset-y-0 right-0 hidden w-1/3 opacity-70 [mask-image:linear-gradient(to_left,#000,transparent)] lg:block" aria-hidden="true"></div>
    <div class="ui-container relative">
      <div
        class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12 lg:gap-16"
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
          class="site-about-motion-right order-1 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:order-2 lg:col-span-6 md:will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 max-md:translate-y-[30px] md:translate-x-[50px]'"
        >
          <span class="ui-eyebrow">Our story</span>
          <h2 class="ui-h2 mt-5">About LITUS <span class="ui-accent text-brand-600">Group</span></h2>
          <p class="mt-6 text-base leading-relaxed text-ink-600 md:text-lg">{!! nl2br(e($aboutIntro1)) !!}</p>
          <p class="mt-4 text-base leading-relaxed text-ink-500 md:text-lg">{!! nl2br(e($aboutIntro2)) !!}</p>

          <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach($highlights as $index => $highlight)
              <div
                class="site-about-motion-hl flex items-start gap-3 rounded-2xl border border-ink-100 bg-sand-50 p-4 transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
                style="transition-delay: {{ 400 + $index * 100 }}ms"
                :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-5'"
              >
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5" />
                  </svg>
                </span>
                <span class="text-sm leading-snug font-medium text-ink-800 sm:text-[0.95rem]">{{ $highlight }}</span>
              </div>
            @endforeach
          </div>

          <div class="mt-10 flex">
            <a
              href="{{ route('site.home') }}#companies"
              class="site-about-motion-cta site-cta-btn ui-btn ui-btn--dark w-full transition-opacity duration-[800ms] sm:w-auto"
              style="transition-delay: 800ms"
              :class="inView ? 'opacity-100' : 'opacity-0'"
            >
              Explore Our Companies
              {!! $arrow !!}
            </a>
          </div>
        </div>

        {{-- Image: second on mobile, left column on desktop --}}
        <div
          class="site-about-motion-left relative order-2 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:order-1 lg:col-span-6 md:will-change-[opacity,transform]"
          :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 max-md:translate-y-[30px] md:-translate-x-[50px]'"
        >
          <div class="pointer-events-none absolute -top-6 -left-6 hidden h-40 w-40 rounded-[2rem] bg-gradient-to-br from-brand-100 to-lagoon-100 lg:block" aria-hidden="true"></div>
          <div
            @if($aboutPartnershipSlideCount > 1)
              x-data="aboutPartnershipSlider(@js($aboutPartnershipUrls))"
            @endif
            class="group relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-ink-100 via-sand-100 to-brand-50 shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)] ring-1 ring-ink-900/5"
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
                        class="aspect-[4/5] h-full w-full object-cover sm:aspect-[5/4] lg:aspect-[4/5]"
                        :fetchpriority="slideIdx === 0 ? 'high' : 'low'"
                        loading="lazy"
                        decoding="async"
                      />
                    </div>
                  </template>
                </div>
              </div>
              <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-ink-950/70 via-ink-950/20 to-transparent"></div>

              <div class="absolute inset-x-4 bottom-4 z-10 flex items-center justify-between gap-3 sm:inset-x-5 sm:bottom-5">
                <div class="flex items-center gap-1 rounded-full border border-white/15 bg-ink-950/40 p-1 backdrop-blur-md">
                  <button
                    type="button"
                    class="ui-icon-btn text-white hover:bg-white hover:text-ink-900 focus-visible:outline-2 focus-visible:outline-white"
                    aria-label="Previous slide"
                    @click="goTo((activeIndex - 1 + slides.length) % slides.length)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                  </button>
                  <span class="min-w-[4.25rem] text-center text-xs font-semibold text-white tabular-nums">
                    <span x-text="String(activeIndex + 1).padStart(2, '0')"></span>
                    <span class="text-white/50">/ <span x-text="String(slides.length).padStart(2, '0')"></span></span>
                  </span>
                  <button
                    type="button"
                    class="ui-icon-btn text-white hover:bg-white hover:text-ink-900 focus-visible:outline-2 focus-visible:outline-white"
                    aria-label="Next slide"
                    @click="goTo((activeIndex + 1) % slides.length)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                  </button>
                </div>
                <div class="hidden items-center gap-1.5 sm:flex">
                  <template x-for="(_, dotIdx) in slides" :key="'dot-' + dotIdx">
                    <button
                      type="button"
                      class="h-1.5 rounded-full transition-all duration-300"
                      :class="activeIndex === dotIdx ? 'w-6 bg-lagoon-300' : 'w-1.5 bg-white/50 hover:bg-white/80'"
                      :aria-label="'Go to slide ' + (dotIdx + 1)"
                      :aria-current="activeIndex === dotIdx ? 'true' : 'false'"
                      @click="goTo(dotIdx)"
                    ></button>
                  </template>
                </div>
              </div>
            @elseif($aboutPartnershipSlideCount === 1)
              <img
                src="{{ $aboutPartnershipUrls[0] }}"
                alt="Business partnership"
                class="aspect-[4/5] h-full w-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-105 sm:aspect-[5/4] lg:aspect-[4/5]"
                loading="lazy"
                decoding="async"
              />
              <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-ink-950/50 to-transparent"></div>
            @else
              <div
                class="aspect-[4/5] w-full sm:aspect-[5/4] lg:aspect-[4/5]"
                role="img"
                aria-label="Business partnership image"
              ></div>
            @endif
          </div>

          <div
            class="site-about-motion-stat absolute -top-8 -right-6 z-10 hidden rounded-3xl border border-ink-100 bg-white p-6 shadow-[0_30px_60px_-20px_rgba(6,22,52,0.3)] transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform] lg:block"
            style="transition-delay: 300ms"
            :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
          >
            <div class="flex items-center gap-4">
              <span class="ui-icon-tile">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" /><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" /><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" /><path d="M10 6h4" /><path d="M10 10h4" /><path d="M10 14h4" /><path d="M10 18h4" /></svg>
              </span>
              <div>
                <div class="text-4xl leading-none font-extrabold tracking-tight text-ink-900 tabular-nums">16+</div>
                <div class="mt-1 text-sm font-medium text-ink-500">Companies</div>
              </div>
            </div>
          </div>
          <div
            class="site-about-motion-stat relative z-10 mt-4 flex w-full items-center gap-4 rounded-3xl border border-ink-100 bg-white px-5 py-4 shadow-[0_20px_40px_-20px_rgba(6,22,52,0.35)] transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto lg:hidden"
            style="transition-delay: 300ms"
            :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
          >
            <span class="ui-icon-tile">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" /><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" /><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" /><path d="M10 6h4" /><path d="M10 10h4" /><path d="M10 14h4" /><path d="M10 18h4" /></svg>
            </span>
            <div>
              <div class="text-3xl leading-none font-extrabold tracking-tight text-ink-900 tabular-nums">16+</div>
              <div class="mt-1 text-sm font-medium text-ink-500">Companies</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================== MISSION & VISION ============================== --}}
  {{-- visionRef on Mission card only; both use visionInView --}}
  <section class="ui-section ui-bg-ink">
    <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 opacity-50 [mask-image:radial-gradient(ellipse_at_center,#000,transparent_75%)]" aria-hidden="true"></div>
    <div class="ui-container">
      <x-site.motion class="mb-12 flex flex-col gap-6 md:mb-16 lg:flex-row lg:items-end lg:justify-between" variant="fade-up">
        <div class="max-w-2xl">
          <span class="ui-eyebrow ui-eyebrow--light">Mission &amp; Vision</span>
          <h2 class="ui-h2 mt-5 !text-white">What <span class="ui-accent text-lagoon-300">drives</span> us</h2>
        </div>
        <p class="max-w-md text-base leading-relaxed text-ink-300 sm:text-lg">
          Our purpose and ambition, shared by every business in the LITUS family.
        </p>
      </x-site.motion>

      <div
        class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6"
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
          class="site-about-motion-mission ui-glass group relative cursor-default overflow-hidden rounded-3xl p-7 transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-10 md:will-change-[opacity,transform] md:hover:-translate-y-1 md:hover:border-white/30 md:hover:bg-white/[0.1]"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <span class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out group-hover:w-full" aria-hidden="true"></span>
          <div class="flex items-start justify-between gap-4">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-[0_12px_30px_-10px_rgba(31,79,224,0.8)]">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="6" /><circle cx="12" cy="12" r="2" /></svg>
            </span>
            <span class="font-serif text-5xl leading-none text-white/15 italic">01</span>
          </div>
          <h3 class="mt-8 text-2xl font-bold tracking-tight text-white sm:text-3xl">Our Mission</h3>
          <p class="mt-4 text-base leading-relaxed text-ink-200 md:text-lg">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction. We strive to be the partner of choice for businesses and individuals seeking excellence.
          </p>
        </div>

        <div
          class="site-about-motion-vision ui-glass group relative cursor-default overflow-hidden rounded-3xl p-7 transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-10 md:will-change-[opacity,transform] md:hover:-translate-y-1 md:hover:border-white/30 md:hover:bg-white/[0.1]"
          style="transition-delay: 200ms"
          :class="visionInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          data-about-vision
        >
          <span class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out group-hover:w-full" aria-hidden="true"></span>
          <div class="flex items-start justify-between gap-4">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-lagoon-400 to-lagoon-500 text-ink-950 shadow-[0_12px_30px_-10px_rgba(20,195,208,0.7)]">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" /><circle cx="12" cy="12" r="3" /></svg>
            </span>
            <span class="font-serif text-5xl leading-none text-white/15 italic">02</span>
          </div>
          <h3 class="mt-8 text-2xl font-bold tracking-tight text-white sm:text-3xl">Our Vision</h3>
          <p class="mt-4 text-base leading-relaxed text-ink-200 md:text-lg">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders while contributing to national economic growth.
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================== CTA ============================== --}}
  <section class="bg-sand-100 py-16 md:py-24">
    <div class="ui-container">
      <x-site.motion variant="fade-up">
        <div class="relative isolate overflow-hidden rounded-[2rem] bg-white px-6 py-12 text-center shadow-[0_30px_60px_-30px_rgba(6,22,52,0.25)] ring-1 ring-ink-100 sm:px-12 md:py-16">
          <div class="ui-grid-texture pointer-events-none absolute inset-0 -z-10 opacity-70 [mask-image:radial-gradient(ellipse_at_center,#000,transparent_70%)]" aria-hidden="true"></div>
          <span class="ui-eyebrow ui-eyebrow--center">Grow with us</span>
          <h2 class="ui-h2 mx-auto mt-5 max-w-3xl">Discover the businesses we <span class="ui-accent text-brand-600">grow together</span></h2>
          <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('site.our-companies') }}" class="ui-btn ui-btn--primary">
              Explore Our Entities
              {!! $arrow !!}
            </a>
            <a href="{{ route('site.contact') }}" class="ui-btn ui-btn--outline">
              Contact Us
            </a>
          </div>
        </div>
      </x-site.motion>
    </div>
  </section>
</div>
@endsection
