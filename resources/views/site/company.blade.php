@extends('layouts.site')

@section('content')
@php
  $company = $company ?? null;
  $heroLogo = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
  $heroImageRaw = $company['hero_image'] ?? null;
  $heroImageUrl = null;
  if (filled($heroImageRaw)) {
    if (str_starts_with($heroImageRaw, 'http://') || str_starts_with($heroImageRaw, 'https://')) {
      $heroImageUrl = $heroImageRaw;
    } elseif (str_starts_with($heroImageRaw, 'companies/')) {
      $heroImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($heroImageRaw);
    }
  }
  $aboutImageRaw = $company['about_image'] ?? null;
  $aboutImageUrl = null;
  if (filled($aboutImageRaw)) {
    if (str_starts_with($aboutImageRaw, 'http://') || str_starts_with($aboutImageRaw, 'https://')) {
      $aboutImageUrl = $aboutImageRaw;
    } elseif (str_starts_with($aboutImageRaw, 'companies/')) {
      $aboutImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($aboutImageRaw);
    }
  }
@endphp

{{-- Matches src/app/pages/CompanyPage.tsx --}}
<div data-company-detail>
  {{-- CompanyHero --}}
  <section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-0 md:py-0 md:pt-36 md:pb-36">
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="{{ $company['name'] }} hero"
          class="h-full w-full object-cover"
          fetchpriority="high"
          decoding="async"
        />
      @else
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900"></div>
      @endif
      <div class="absolute inset-0 bg-gradient-to-b from-blue-950/45 via-blue-900/30 to-blue-950/15 md:bg-gradient-to-r md:from-blue-900/90 md:via-blue-800/80 md:to-transparent"></div>
    </div>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 sm:py-16 md:py-0 lg:px-8">
      <div class="site-company-hero text-center text-white">
        <div class="mx-auto mb-3 flex h-16 items-center justify-center sm:mb-4 sm:h-20 md:mb-4 md:h-28">
          @if($heroLogo)
            <img
              src="{{ $heroLogo }}"
              alt="{{ $company['name'] }}"
              class="h-full w-auto max-w-[min(100%,280px)] object-contain brightness-0 invert scale-[1.618] sm:max-w-[min(100%,360px)] md:max-w-[min(100%,480px)]"
            />
          @endif
        </div>

        <h1 class="mb-2 text-2xl font-bold sm:text-3xl md:text-6xl">{{ $company['name'] }}</h1>
        <p class="mx-auto max-w-3xl px-2 py-3 text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:px-4 sm:py-4 sm:text-lg md:py-5 md:text-2xl md:[text-shadow:none]">{{ $company['tagline'] }}</p>

        <div class="mx-auto mt-6 flex max-w-sm flex-col gap-3 sm:mt-10 sm:max-w-none sm:flex-row sm:justify-center md:mt-12">
          <a
            href="tel:{{ $company['hotline'] }}"
            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-6 py-3.5 text-base font-semibold text-blue-900 shadow-md transition-all hover:bg-gray-100 hover:shadow-lg sm:w-auto sm:px-5 sm:py-2.5"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            {{ $company['hotline'] }}
          </a>
          @if(!empty($company['email']))
            <a
              href="mailto:{{ $company['email'] }}"
              class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-6 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition-all hover:bg-white/20 sm:w-auto sm:px-5 sm:py-2.5 md:backdrop-blur-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                <rect width="20" height="16" x="2" y="4" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
              Email Us
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- AboutCompany — text first on mobile, image below --}}
  <section class="overflow-x-clip bg-white py-14 md:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12"
        x-data="siteInViewReveal('aboutInView')"
      >
        <div
          class="site-company-motion-about-left order-1 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="aboutInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:-translate-x-[50px]'"
        >
          <h2 class="mb-4 text-center text-2xl font-bold text-gray-900 sm:mb-6 sm:text-3xl md:text-left md:text-5xl">
            About {{ $company['name'] }}
          </h2>
          @if(filled($company['description'] ?? null))
            <p class="mb-4 text-center text-base leading-relaxed text-gray-600 sm:mb-6 md:text-left md:text-lg">
              {{ $company['description'] }}
            </p>
          @endif
          @if(filled($company['description_secondary'] ?? null))
            <p class="text-center text-base leading-relaxed text-gray-600 md:text-left md:text-lg">
              {{ $company['description_secondary'] }}
            </p>
          @endif
        </div>

        @if(filled($aboutImageUrl))
          <div
            class="site-company-motion-about-right relative order-2 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
            style="transition-delay: 200ms"
            :class="aboutInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:translate-x-[50px]'"
          >
            <div class="group relative overflow-hidden rounded-2xl shadow-2xl transition-all duration-300 md:hover:-translate-y-1 md:hover:shadow-2xl">
              <img
                src="{{ $aboutImageUrl }}"
                alt="{{ $company['name'] }}"
                class="h-full w-full object-cover transition-transform duration-500 ease-out md:group-hover:scale-105"
                loading="lazy"
                decoding="async"
              />
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>

  {{-- ServicesSection — 2 columns on mobile --}}
  <section class="bg-gray-50 py-14 md:py-24" x-data="siteInViewReveal('servicesInView')">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="site-company-motion-services-header mb-10 text-center transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        x-intersect.once.margin.0px.0px.0px.0px="reveal()"
        :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">Our Services</h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Comprehensive solutions tailored to meet your needs
        </p>
      </div>

      <div class="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-3">
        @foreach(($company['services'] ?? []) as $index => $service)
          @php
            $serviceDisplay = \App\Support\CompanyPageIcons::resolveLabeledItem($service);
          @endphp
          <div
            class="site-company-motion-service-card h-full transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
          >
            <div class="h-full rounded-lg border border-gray-200 bg-white p-3 text-center transition-all sm:rounded-xl sm:p-6 md:hover:border-blue-300 md:hover:shadow-lg">
              @if(filled($serviceDisplay['icon_url']))
                <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 sm:mb-4 sm:h-16 sm:w-16 md:h-[80px] md:w-[80px]">
                  <img
                    src="{{ $serviceDisplay['icon_url'] }}"
                    alt=""
                    class="h-8 w-8 object-contain sm:h-10 sm:w-10 md:h-[50px] md:w-[50px]"
                    loading="lazy"
                    decoding="async"
                  />
                </div>
              @endif
              <h3 class="text-sm font-bold leading-snug text-gray-900 sm:text-lg">{{ $serviceDisplay['label'] }}</h3>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- WhyChoose — 2 columns on mobile --}}
  <section class="bg-white py-14 md:py-24" x-data="siteInViewReveal('whyInView')">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="site-company-motion-why-header mb-10 text-center transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        x-intersect.once.margin.0px.0px.0px.0px="reveal()"
        :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">
          Why Choose {{ $company['name'] }}
        </h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Experience the difference that sets us apart from the competition
        </p>
      </div>

      <div class="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-4 lg:gap-8">
        @foreach(($company['strengths'] ?? []) as $index => $strength)
          @php
            $strengthDisplay = \App\Support\CompanyPageIcons::resolveLabeledItem($strength);
          @endphp
          <div
            class="site-company-motion-why-card text-center transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
          >
            @if(filled($strengthDisplay['icon_url']))
              <div class="mx-auto mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-[#1d4291] sm:mb-4 sm:h-20 sm:w-20 md:h-[80px] md:w-[80px]">
                <img
                  src="{{ $strengthDisplay['icon_url'] }}"
                  alt=""
                  class="h-9 w-9 object-contain sm:h-12 sm:w-12 md:h-[50px] md:w-[50px]"
                  loading="lazy"
                  decoding="async"
                />
              </div>
            @endif
            <h3 class="mb-0 text-sm font-bold leading-snug text-gray-900 sm:mb-2 sm:text-lg md:text-xl">{{ $strengthDisplay['label'] }}</h3>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ContactSection — form first on mobile --}}
  <section class="bg-gray-50 py-14 md:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12"
        x-data="siteInViewReveal('contactInView')"
      >
        <div
          class="site-company-motion-contact-left order-2 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto lg:order-1 md:will-change-[opacity,transform]"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="contactInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:-translate-x-[50px]'"
        >
          <h2 class="mb-4 text-center text-2xl font-bold text-gray-900 sm:mb-6 md:text-left md:text-4xl">Get In Touch</h2>
          <p class="mb-6 text-center text-base leading-relaxed text-gray-600 sm:mb-8 md:text-left md:text-lg">
            Have questions or ready to get started? Contact us today and discover how {{ $company['name'] }} can serve you.
          </p>

          <div class="space-y-5 sm:space-y-6">
            <div class="flex items-start gap-3 sm:gap-4">
              <div class="shrink-0 rounded-lg bg-blue-100 p-2.5 sm:p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
              </div>
              <div class="min-w-0">
                <div class="mb-1 font-semibold text-gray-900">Hotline</div>
                <a
                  href="tel:{{ $company['hotline'] }}"
                  class="inline-block py-1 text-base text-gray-600 transition-colors hover:text-blue-600 sm:text-lg"
                >
                  {{ $company['hotline'] }}
                </a>
              </div>
            </div>

            @if(!empty($company['email']))
              <div class="flex items-start gap-3 sm:gap-4">
                <div class="shrink-0 rounded-lg bg-blue-100 p-2.5 sm:p-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                    <rect width="20" height="16" x="2" y="4" rx="2" />
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="mb-1 font-semibold text-gray-900">Email</div>
                  <a
                    href="mailto:{{ $company['email'] }}"
                    class="inline-block break-all py-1 text-base text-gray-600 transition-colors hover:text-blue-600"
                  >
                    {{ $company['email'] }}
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>

        <div
          class="site-company-motion-contact-right order-1 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto lg:order-2 md:will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="contactInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:translate-x-[50px]'"
        >
          <x-company-contact-form :company-name="$company['name']" :company-id="optional($companyRow)->id" />
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
