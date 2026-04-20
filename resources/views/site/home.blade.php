@extends('layouts.site')

@section('content')
@php
  $highlights = $heroSpotlightHighlights ?? [];
  $displayCompanies = array_slice($companies ?? [], 0, 8);

  $heroImagePath = \App\Models\SiteSetting::getValue('home.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
@endphp

<div>
  {{-- Hero: matches src/app/pages/HomePage.tsx HeroSection --}}
  <section
    id="home"
    class="relative min-h-screen flex items-center justify-center overflow-hidden"
  >
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Modern corporate building"
          class="w-full h-full object-cover"
          fetchpriority="high"
          decoding="async"
        />
      @endif
      <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-800/80 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
      <div class="max-w-3xl">
        <h1 class="site-hero-title text-4xl md:text-7xl font-bold text-white mb-6 leading-tight">
          Taking Diversification
          <br />
          <span class="text-blue-300">To A Whole New Level</span>
        </h1>

        <p class="site-hero-lead text-lg md:text-2xl text-gray-200 mb-10 leading-relaxed">
          From hospitality to construction, automotive to technology –
          LITUS Group delivers world-class services across 16 diverse brands.
        </p>

        @if(count($highlights) > 0)
          {{-- Rotating spotlight: all companies with featured=true (DB order); Alpine cycles when 2+ --}}
          <div
            class="site-hero-card bg-white/10 border border-white/20 rounded-2xl p-6 mb-10 md:backdrop-blur-md"
          >
            <div
              class="min-h-[5.5rem] flex flex-col justify-center"
              x-data="heroSpotlight(@js($highlights))"
            >
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
                <div class="min-w-0 w-full text-center sm:flex-1 sm:text-center">
                  <div
                    class="text-white font-extrabold leading-tight tracking-tight text-[1.7rem] sm:text-3xl"
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

        <div class="site-hero-ctas flex flex-col sm:flex-row gap-4">
          <a
            href="{{ route('site.our-companies') }}"
            class="group bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
          >
            Explore Our Companies
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 group-hover:translate-x-1 transition-transform" aria-hidden="true">
              <path d="M5 12h14"></path>
              <path d="m12 5 7 7-7 7"></path>
            </svg>
          </a>
          <a
            href="{{ route('site.contact') }}"
            class="bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all border border-white/30 md:backdrop-blur-sm flex items-center justify-center text-center"
          >
            Contact Us
          </a>
        </div>
      </div>
    </div>

    <div class="site-scroll-indicator absolute bottom-10 left-1/2 -translate-x-1/2 z-10">
      <div class="site-scroll-indicator__mouse w-6 h-10 border-2 border-white/50 rounded-full flex items-start justify-center p-2">
        <div class="w-1 h-2 bg-white rounded-full"></div>
      </div>
    </div>
  </section>

  {{-- CompaniesOverview — matches HomePage.tsx (single useInView ref on header, shared isInView for grid + CTA) --}}
  <section
    id="companies"
    class="py-24 bg-white"
    data-companies-overview
    x-data="{
      companiesInView: false,
      init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.companiesInView = true;
      }
    }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-companies-overview-header text-center mb-16 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="companiesInView = true"
        :class="companiesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Our Companies</h2>
        <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
          16 specialized companies delivering excellence across multiple industries
        </p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6 mb-12">
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
              <div class="bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 p-6 rounded-xl transition-all cursor-pointer group text-center h-full flex flex-col items-center justify-center min-h-[180px]">
                @if($companyLogoSrc)
                  <div class="w-full h-24 flex items-center justify-center mb-2">
                    <img
                      src="{{ $companyLogoSrc }}"
                      alt="{{ $company['name'] }}"
                      class="max-w-full max-h-full object-contain"
                      loading="lazy"
                      decoding="async"
                    />
                  </div>
                @else
                  <div class="bg-white group-hover:bg-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 transition-all">
                    <x-site.lucide-icon
                      :name="$company['icon'] ?? 'building2'"
                      class="w-8 h-8 text-gray-600 group-hover:text-blue-600 transition-colors"
                    />
                  </div>
                @endif
                <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors text-sm">
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
          class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2"
        >
          View All Companies
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <x-site.motion variant="fade-left" :duration="800">
          <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">Why Choose LITUS Group</h2>
          <p class="text-lg text-gray-600 mb-6 leading-relaxed">
            LITUS Group stands as a beacon of diversification and excellence in the Maldives business landscape. With 16 specialized companies spanning multiple industries, we deliver comprehensive solutions that drive growth and create lasting value.
          </p>
          <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            Our commitment to quality, innovation, and customer satisfaction has made us a trusted partner for businesses and individuals alike.
          </p>
          <a
            href="{{ route('site.about') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
          >
            Learn More About Us
            <span>→</span>
          </a>
        </x-site.motion>

        @php
          $whyChooseImagePath = \App\Models\SiteSetting::getValue('home.why_choose.image_path');
          $whyChooseImageUrl = $whyChooseImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($whyChooseImagePath) : null;
        @endphp

        @if($whyChooseImageUrl)
          <x-site.motion variant="fade-right" :delay="200" :duration="800">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl">
              <img
                src="{{ $whyChooseImageUrl }}"
                alt="Why Choose LITUS Group"
                class="w-full h-full object-cover"
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
  <section class="py-24 bg-blue-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-12"
        data-home-mission-vision
        x-data="{
          mvInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.mvInView = true;
          }
        }"
      >
        <div
          x-intersect.once.margin.-100px.-100px.-100px.-100px="mvInView = true"
          class="bg-white/10 border border-white/20 rounded-2xl p-8 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform] md:backdrop-blur-sm"
          :class="mvInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Our Mission</h3>
          <p class="text-base md:text-lg text-blue-100 leading-relaxed">
            To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction.
          </p>
        </div>
        <div
          class="bg-white/10 border border-white/20 rounded-2xl p-8 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform] md:backdrop-blur-sm"
          style="transition-delay: 200ms"
          :class="mvInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Our Vision</h3>
          <p class="text-base md:text-lg text-blue-100 leading-relaxed">
            To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="text-center mb-16" variant="fade-up" :duration="800">
        <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Our Core Values</h2>
        <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
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

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        @foreach($values as $i => $v)
          <x-site.motion :delay="$i * 100" :duration="500" variant="fade-up">
            <div class="bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl border border-blue-100 text-center h-full">
              <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl font-bold text-white">{{ $v['letter'] }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $v['title'] }}</h3>
              <p class="text-gray-600">{{ $v['description'] }}</p>
            </div>
          </x-site.motion>
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-24 bg-blue-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="text-center mb-16" variant="fade-up" :duration="800">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Our Leadership</h2>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-8">
          Meet the visionary leaders driving LITUS Group's success across all sectors
        </p>
        <a
          href="{{ route('site.team') }}"
          class="bg-white hover:bg-gray-100 text-blue-900 px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
        >
          View Full Team
          <span>→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-24 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-site.motion class="text-center mb-16" variant="fade-up" :duration="800">
        <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">News & Media</h2>
        <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
          Stay updated with the latest stories and insights from across the LITUS Group
        </p>
      </x-site.motion>

      <x-site.motion class="text-center" variant="fade-up" :delay="200" :duration="800">
        <a
          href="{{ route('site.blogs') }}"
          class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
        >
          Read More
          <span>→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-24 bg-blue-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <x-site.motion variant="fade-up" :duration="800">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Join Our Team</h2>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-10">
          Build your career with LITUS Group and be part of a dynamic team that's shaping the future across 16 diverse companies
        </p>
        <a
          href="{{ route('site.careers') }}"
          class="bg-white hover:bg-gray-100 text-blue-900 px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
        >
          Explore Careers
          <span>→</span>
        </a>
      </x-site.motion>
    </div>
  </section>

  <section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
        <x-site.motion class="flex-1" variant="fade-left-sm" :duration="800">
          <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">Let's Connect</h2>
          <p class="text-lg md:text-xl text-gray-600 leading-relaxed">
            Have questions or interested in our services? Get in touch with us today
          </p>
        </x-site.motion>
        <x-site.motion class="flex-shrink-0" variant="fade-right-sm" :delay="200" :duration="800">
          <a
            href="{{ route('site.contact') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
          >
            Contact Us
            <span>→</span>
          </a>
        </x-site.motion>
      </div>
    </div>
  </section>
</div>
@endsection
