@extends('layouts.site')

@section('content')
@php
  $highlights = $heroSpotlightHighlights ?? [];
  $displayCompanies = array_slice($companies ?? [], 0, 8);

  $leadershipTeam = array_values(array_filter(
    \App\Support\SiteData::team(),
    fn ($m) => filled($m['image'] ?? null)
  ));

  $homeBlogPreviews = array_slice(\App\Support\SiteData::blogPosts(), 0, 4);

  $heroImagePath = \App\Models\SiteSetting::getValue('home.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
@endphp

<div>
  {{-- Hero: matches src/app/pages/HomePage.tsx HeroSection --}}
  <section
    id="home"
    class="relative flex min-h-[min(82svh,700px)] items-center justify-center overflow-hidden md:min-h-screen"
    @if(count($highlights) > 0)
      x-data="heroSpotlight(@js($highlights), @js($heroImageUrl))"
    @endif
  >
    <div class="absolute inset-0 z-0 overflow-hidden">
      @if(count($highlights) > 0)
        <div
          class="hero-bg-slider-track flex h-full"
          :style="heroTrackStyle"
          x-show="heroSlides.length > 0"
        >
          <template x-for="(src, slideIdx) in heroSlides" :key="slideIdx">
            <div class="hero-bg-slider-slide relative h-full shrink-0 grow-0 basis-full">
              <img
                :src="src"
                alt=""
                class="absolute inset-0 h-full w-full object-cover"
                :fetchpriority="slideIdx === 0 ? 'high' : 'low'"
                decoding="async"
              />
            </div>
          </template>
        </div>
      @elseif(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Modern corporate building"
          class="h-full w-full object-cover"
          fetchpriority="high"
          decoding="async"
        />
      @endif
      <div class="absolute inset-0 bg-gradient-to-b from-blue-950/45 via-blue-900/30 to-blue-950/15 md:bg-gradient-to-r md:from-blue-900/90 md:via-blue-800/80 md:to-transparent"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 pb-12 pt-20 sm:px-6 sm:pb-14 sm:pt-24 md:py-32 md:pb-[max(5rem,env(safe-area-inset-bottom))] lg:px-8">
      <div class="mx-auto max-w-3xl text-center md:mx-0 md:text-left">
        <h1 class="site-hero-title mb-4 text-3xl font-bold leading-tight text-white max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-7xl md:[text-shadow:none]">
          Taking Diversification
          <br />
          <span class="text-blue-300">To A Whole New Level</span>
        </h1>

        <p class="site-hero-lead mb-6 text-base leading-relaxed text-gray-200 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:mb-8 sm:text-lg md:mb-10 md:text-2xl md:[text-shadow:none]">
          From hospitality to construction, automotive to technology –
          LITUS Group delivers world-class services across 16 diverse brands.
        </p>

        @if(count($highlights) > 0)
          {{-- Rotating spotlight: all companies with featured=true (DB order); Alpine cycles when 2+ --}}
          <div
            class="site-hero-card mx-auto mb-6 w-full max-w-sm rounded-2xl border border-white/20 bg-white/15 p-4 sm:mb-8 sm:max-w-none sm:bg-white/10 sm:p-6 md:mx-0 md:mb-10 md:backdrop-blur-md"
          >
            <div class="flex min-h-[4.5rem] flex-col justify-center sm:min-h-[5.5rem]">
              <div
                x-show="visible"
                x-transition:enter="hero-spotlight-tx-enter"
                x-transition:enter-start="hero-spotlight-from-below"
                x-transition:enter-end="hero-spotlight-at-rest"
                x-transition:leave="hero-spotlight-tx-leave"
                x-transition:leave-start="hero-spotlight-at-rest"
                x-transition:leave-end="hero-spotlight-to-above"
                class="flex flex-col items-center gap-4 sm:flex-row sm:items-center sm:justify-between"
              >
                <div class="min-w-0 w-full text-center sm:flex-1 sm:text-left">
                  <div
                    class="break-words text-[1.35rem] font-bold leading-tight tracking-tight text-white sm:text-3xl"
                    x-text="items[idx].company"
                  ></div>
                </div>
                <a
                  x-show="items[idx].hotline && String(items[idx].hotline).trim().length"
                  class="inline-flex shrink-0 items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full font-semibold transition-all shadow-md shadow-blue-950/20 ring-1 ring-white/10 w-full sm:w-auto"
                  :href="'tel:' + String(items[idx].hotline).replace(/\s/g, '')"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                  <span class="whitespace-nowrap" x-text="items[idx].hotline"></span>
                </a>
              </div>
            </div>
          </div>
        @endif

        <div class="site-hero-ctas mx-auto flex w-full max-w-sm flex-col items-center gap-3 md:mx-0 md:max-w-none md:items-stretch sm:flex-row sm:items-center sm:justify-between sm:gap-4">
          <a
            href="{{ route('site.our-companies') }}"
            class="group flex w-full items-center justify-center gap-2 rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg transition-all hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
          >
            Explore Our Companies
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 group-hover:translate-x-1 transition-transform" aria-hidden="true">
              <path d="M5 12h14"></path>
              <path d="m12 5 7 7-7 7"></path>
            </svg>
          </a>
          <a
            href="{{ route('site.contact') }}"
            class="flex w-full items-center justify-center rounded-full border border-white/30 bg-white/15 px-6 py-3.5 text-center text-base font-semibold text-white transition-all hover:bg-white/20 sm:ml-auto sm:w-auto sm:px-8 sm:py-4 sm:text-lg md:backdrop-blur-sm"
          >
            Contact Us
          </a>
        </div>
      </div>
    </div>

    <div class="site-scroll-indicator absolute bottom-6 left-1/2 z-10 hidden -translate-x-1/2 md:block md:bottom-10">
      <div class="site-scroll-indicator__mouse w-6 h-10 border-2 border-white/50 rounded-full flex items-start justify-center p-2">
        <div class="w-1 h-2 bg-white rounded-full"></div>
      </div>
    </div>
  </section>

  {{-- CompaniesOverview — matches HomePage.tsx (single useInView ref on header, shared isInView for grid + CTA) --}}
  <section
    id="companies"
    class="py-14 bg-white md:py-24"
    data-companies-overview
    x-data="{
      companiesInView: false,
      init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.companiesInView = true;
        else if (window.matchMedia('(max-width: 767px)').matches) this.companiesInView = true;
      }
    }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-companies-overview-header mb-10 text-center transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="companiesInView = true"
        :class="companiesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">Our Companies</h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          16 specialized companies delivering excellence across multiple industries
        </p>
      </div>

      <div class="mb-10 grid grid-cols-2 gap-3 sm:gap-6 md:mb-12 md:grid-cols-4 lg:grid-cols-4">
        @foreach($displayCompanies as $index => $company)
          @php
            $companyLogoSrc = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
          @endphp
          <div
            class="site-companies-overview-card h-full transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 50 }}ms"
            :class="companiesInView ? 'opacity-100 scale-100' : 'opacity-0 scale-90'"
          >
            <a href="{{ route('site.company', ['slug' => $company['slug']]) }}" class="block h-full">
              <div class="group flex h-full min-h-[128px] cursor-pointer flex-col items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-3 text-center transition-all hover:border-blue-300 hover:bg-blue-50 sm:min-h-[180px] sm:p-6">
                @if($companyLogoSrc)
                  <div class="mb-1 flex h-16 w-full items-center justify-center sm:mb-2 sm:h-24">
                    <img
                      src="{{ $companyLogoSrc }}"
                      alt="{{ $company['name'] }}"
                      class="max-w-full max-h-full object-contain"
                      loading="lazy"
                      decoding="async"
                    />
                  </div>
                @else
                  <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-xl bg-white transition-all group-hover:bg-blue-100 sm:mb-4 sm:h-16 sm:w-16">
                    <x-site.lucide-icon
                      :name="$company['icon'] ?? 'building2'"
                      class="h-6 w-6 text-gray-600 transition-colors group-hover:text-blue-600 sm:h-8 sm:w-8"
                    />
                  </div>
                @endif
                <h3 class="text-xs font-bold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 sm:text-sm">
                  {{ $company['name'] }}
                </h3>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <div
        class="site-companies-overview-cta text-center transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
        style="transition-delay: 400ms"
        :class="companiesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
      >
        <a
          href="{{ route('site.our-companies') }}"
          class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
        >
          View All Companies
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="site-cta-btn__icon" aria-hidden="true">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <section class="py-14 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12">
        <x-site.motion class="text-center lg:text-left" variant="fade-up" :duration="800">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 sm:mb-6 sm:text-3xl md:text-5xl">Why Choose LITUS Group</h2>
          <p class="mb-4 text-base leading-relaxed text-gray-600 sm:mb-6 sm:text-lg">
            LITUS Group stands as a beacon of diversification and excellence in the Maldives business landscape. With 16 specialized companies spanning multiple industries, we deliver comprehensive solutions that drive growth and create lasting value.
          </p>
          <p class="mb-6 text-base leading-relaxed text-gray-600 sm:mb-8 sm:text-lg">
            Our commitment to quality, innovation, and customer satisfaction has made us a trusted partner for businesses and individuals alike.
          </p>
          <div class="flex justify-center lg:justify-start">
            <a
              href="{{ route('site.about') }}"
              class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
            >
              Learn More About Us
              <span class="site-cta-btn__icon" aria-hidden="true">→</span>
            </a>
          </div>
        </x-site.motion>

        @php
          $whyChooseImagePath = \App\Models\SiteSetting::getValue('home.why_choose.image_path');
          $whyChooseImageUrl = $whyChooseImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($whyChooseImagePath) : null;
        @endphp

        @if($whyChooseImageUrl)
          <x-site.motion variant="fade-up" :delay="200" :duration="800">
            <div class="group relative overflow-hidden rounded-2xl shadow-2xl transition-all duration-300 md:hover:-translate-y-1 md:hover:shadow-2xl">
              <img
                src="{{ $whyChooseImageUrl }}"
                alt="Why Choose LITUS Group"
                class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                loading="lazy"
                decoding="async"
              />
            </div>
          </x-site.motion>
        @endif
      </div>
    </div>
  </section>

  {{-- MissionVision: single useInView ref on Mission card (HomePage.tsx) --}}
  <section class="py-14 bg-blue-600 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-12"
        data-home-mission-vision
        x-data="{
          mvInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.mvInView = true;
            else if (window.matchMedia('(max-width: 767px)').matches) this.mvInView = true;
          }
        }"
      >
        <div
          x-intersect.once.margin.-100px.-100px.-100px.-100px="mvInView = true"
          class="cursor-default rounded-2xl border border-white/20 bg-white/10 p-6 text-center transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-8 md:text-left md:will-change-[opacity,transform] md:backdrop-blur-sm md:hover:-translate-y-1 md:hover:border-white/40 md:hover:bg-white/15 md:hover:shadow-xl"
          :class="mvInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <h3 class="mb-3 text-xl font-bold text-white sm:mb-4 sm:text-2xl md:text-3xl">Our Mission</h3>
          <p class="text-base leading-relaxed text-blue-100 md:text-lg">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction.
          </p>
        </div>
        <div
          class="cursor-default rounded-2xl border border-white/20 bg-white/10 p-6 text-center transition-[opacity,transform,box-shadow,background-color,border-color] duration-[800ms] ease-out hover:duration-300 max-md:will-change-auto sm:p-8 md:text-left md:will-change-[opacity,transform] md:backdrop-blur-sm md:hover:-translate-y-1 md:hover:border-white/40 md:hover:bg-white/15 md:hover:shadow-xl"
          style="transition-delay: 200ms"
          :class="mvInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <h3 class="mb-3 text-xl font-bold text-white sm:mb-4 sm:text-2xl md:text-3xl">Our Vision</h3>
          <p class="text-base leading-relaxed text-blue-100 md:text-lg">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-14 bg-white md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="mb-10 text-center md:mb-16" variant="fade-up" :duration="800">
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">Our Core Values</h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          The LITUS principles that guide everything we do
        </p>
      </x-site.motion>

      @php
        $values = [
          ['letter' => 'L', 'title' => 'Leadership', 'description' => 'Leading by example in every industry we serve'],
          ['letter' => 'I', 'title' => 'Innovation', 'description' => 'Embracing new ideas and cutting-edge solutions'],
          ['letter' => 'T', 'title' => 'Trust', 'description' => 'Building lasting relationships through reliability'],
          ['letter' => 'U', 'title' => 'Unity', 'description' => 'Working together towards common goals'],
          ['letter' => 'S', 'title' => 'Service', 'description' => 'Delivering excellence in every interaction'],
        ];
      @endphp

      <div class="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-5">
        @foreach($values as $i => $v)
          <x-site.motion :delay="$i * 100" :duration="500" variant="fade-up" class="h-full">
            <div class="group flex h-full cursor-default flex-col rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-4 text-center transition-all duration-300 sm:p-6 lg:p-8 md:hover:-translate-y-1 md:hover:border-blue-300 md:hover:shadow-xl">
              <div class="mx-auto mb-3 flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-600 transition-transform duration-300 group-hover:scale-110 group-hover:shadow-lg sm:mb-4 sm:h-16 sm:w-16">
                <span class="text-2xl font-bold text-white sm:text-3xl">{{ $v['letter'] }}</span>
              </div>
              <h3 class="mb-1.5 text-sm font-bold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 sm:mb-2 sm:text-xl">{{ $v['title'] }}</h3>
              <p class="text-xs leading-snug text-gray-600 sm:text-base">{{ $v['description'] }}</p>
            </div>
          </x-site.motion>
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-14 bg-blue-900 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="mb-10 text-center md:mb-16" variant="fade-up" :duration="800">
        <h2 class="mb-3 text-2xl font-bold text-white sm:mb-4 sm:text-3xl md:text-5xl">Our Leadership</h2>
        <p class="mx-auto max-w-2xl text-base text-blue-100 sm:text-lg md:text-xl">
          Meet the visionary leaders driving LITUS Group's success across all sectors
        </p>
      </x-site.motion>

      @if(count($leadershipTeam) > 0)
        <div class="mb-10 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 md:mb-16 lg:grid-cols-4 lg:gap-8">
          @foreach($leadershipTeam as $index => $member)
            <x-site.motion :delay="$index * 80" :duration="600" variant="fade-up">
              <article class="group text-center">
                <div class="relative mx-auto mb-3 aspect-square max-w-[9.5rem] overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl shadow-black/30 ring-1 ring-white/10 sm:mb-4 sm:max-w-[220px] md:max-w-[240px]">
                  <img
                    src="{{ $member['image'] }}"
                    alt="{{ $member['name'] }}"
                    class="h-full w-full object-cover object-center transition duration-500 ease-out group-hover:scale-105"
                    loading="lazy"
                    decoding="async"
                  />
                  @if(!empty($member['linkedin_url']) || !empty($member['email']))
                    <div class="absolute inset-0 flex items-end justify-center bg-gradient-to-t from-blue-950/95 via-blue-900/35 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 pb-4">
                      <div class="flex gap-2">
                        @if(!empty($member['linkedin_url']))
                          <a
                            href="{{ $member['linkedin_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-full bg-white p-2.5 text-blue-900 shadow-md transition-transform hover:scale-110 hover:bg-blue-50"
                            aria-label="LinkedIn — {{ $member['name'] }}"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                              <rect width="4" height="12" x="2" y="9" />
                              <circle cx="4" cy="4" r="2" />
                            </svg>
                          </a>
                        @endif
                        @if(!empty($member['email']))
                          <a
                            href="mailto:{{ $member['email'] }}"
                            class="rounded-full bg-white p-2.5 text-blue-900 shadow-md transition-transform hover:scale-110 hover:bg-blue-50"
                            aria-label="Email — {{ $member['name'] }}"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                              <rect width="20" height="16" x="2" y="4" rx="2" />
                              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                          </a>
                        @endif
                      </div>
                    </div>
                  @endif
                </div>
                <h3 class="text-base font-bold leading-snug text-white sm:text-lg">
                  {{ $member['name'] }}
                </h3>
                @if(!empty($member['role']))
                  <p class="mt-1 text-xs font-semibold text-blue-200 sm:text-sm">
                    {{ $member['role'] }}
                  </p>
                @endif
              </article>
            </x-site.motion>
          @endforeach
        </div>
      @endif

      <x-site.motion class="text-center" variant="fade-up" :delay="200" :duration="800">
        <a
          href="{{ route('site.team') }}"
          class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-white px-6 py-3.5 text-base font-semibold text-blue-900 shadow-lg hover:bg-gray-100 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
        >
          View Full Team
          <span class="site-cta-btn__icon" aria-hidden="true">→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-14 bg-gray-100 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="mb-10 text-center md:mb-16" variant="fade-up" :duration="800">
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">News & Media</h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Stay updated with the latest stories and insights from across the LITUS Group
        </p>
      </x-site.motion>

      @if(count($homeBlogPreviews) > 0)
        <div class="mb-10 grid grid-cols-2 gap-3 sm:mb-12 sm:gap-5 md:mb-16 lg:grid-cols-4">
          @foreach($homeBlogPreviews as $i => $post)
            <x-site.motion :delay="$i * 80" :duration="600" variant="fade-up" class="h-full">
              <a
                href="{{ route('site.blog-article', ['slug' => $post['slug']]) }}"
                class="group flex h-full w-full flex-col overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm transition-all duration-300 md:hover:-translate-y-0.5 md:hover:border-blue-200 md:hover:shadow-lg"
              >
                <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-blue-50 via-gray-50 to-blue-100 sm:aspect-video">
                  @if(filled($post['image'] ?? null))
                    <img
                      src="{{ $post['image'] }}"
                      alt="{{ $post['title'] }}"
                      class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-105"
                      loading="lazy"
                      decoding="async"
                    />
                  @else
                    <div class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                      <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="text-blue-200/80">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                        <circle cx="9" cy="9" r="2" />
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                      </svg>
                    </div>
                  @endif
                </div>
                <div class="flex flex-1 flex-col p-2.5 text-left sm:p-4">
                  <h3 class="line-clamp-3 text-xs font-bold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 sm:line-clamp-none sm:text-base">
                    {{ $post['title'] }}
                  </h3>
                  @if(filled($post['date'] ?? null))
                    <p class="mt-1 text-[0.65rem] text-gray-500 sm:mt-1.5 sm:text-sm">
                      {{ $post['date'] }}
                    </p>
                  @endif
                </div>
              </a>
            </x-site.motion>
          @endforeach
        </div>
      @endif

      <x-site.motion class="text-center" variant="fade-up" :delay="200" :duration="800">
        <a
          href="{{ route('site.blogs') }}"
          class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
        >
          Read More
          <span class="site-cta-btn__icon" aria-hidden="true">→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-14 bg-blue-900 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <x-site.motion variant="fade-up" :duration="800">
        <h2 class="mb-4 text-2xl font-bold text-white sm:mb-6 sm:text-3xl md:text-5xl">Join Our Team</h2>
        <p class="mx-auto mb-8 max-w-2xl text-base text-blue-100 sm:mb-10 sm:text-lg md:text-xl">
          Build your career with LITUS Group and be part of a dynamic team that's shaping the future across 16 diverse companies
        </p>
        <a
          href="{{ route('site.careers') }}"
          class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-white px-6 py-3.5 text-base font-semibold text-blue-900 shadow-lg hover:bg-gray-100 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
        >
          Explore Careers
          <span class="site-cta-btn__icon" aria-hidden="true">→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-12 bg-white md:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-center lg:justify-between">
        <x-site.motion class="flex-1 text-center lg:text-left" variant="fade-up" :duration="800">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 sm:mb-6 sm:text-3xl md:text-5xl">Let's Connect</h2>
          <p class="text-base leading-relaxed text-gray-600 sm:text-lg md:text-xl">
            Have questions or interested in our services? Get in touch with us today
          </p>
        </x-site.motion>
        <x-site.motion class="flex shrink-0 justify-center lg:justify-end" variant="fade-up" :delay="200" :duration="800">
          <a
            href="{{ route('site.contact') }}"
            class="site-cta-btn group inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-full bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
          >
            Contact Us
            <span class="site-cta-btn__icon" aria-hidden="true">→</span>
          </a>
        </x-site.motion>
      </div>
    </div>
  </section>
</div>
@endsection
