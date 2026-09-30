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
  $divisionTitle = \App\Support\SiteData::divisions()[$company['division'] ?? '']['title'] ?? null;
  $services = $company['services'] ?? [];
  $strengths = $company['strengths'] ?? [];

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $phoneIcon = '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />';
  $mailIcon = '<rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />';
@endphp

{{-- Matches src/app/pages/CompanyPage.tsx --}}
<div data-company-detail>
  {{-- CompanyHero --}}
  <section class="relative isolate flex min-h-[min(88svh,760px)] flex-col overflow-hidden bg-ink-950 text-white">
    <div class="absolute inset-0 -z-10">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="{{ $company['name'] }} hero"
          class="h-full w-full object-cover"
          fetchpriority="high"
          decoding="async"
        />
      @else
        <div class="ui-page-hero__glow"></div>
        <div class="ui-grid-texture--light absolute inset-0 [mask-image:radial-gradient(ellipse_at_top_right,black,transparent_70%)]"></div>
      @endif
      <div class="absolute inset-0 bg-ink-950/35"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/55 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/60 to-transparent"></div>
    </div>

    <div class="ui-container flex flex-1 flex-col justify-end pt-32 pb-12 md:pt-40 md:pb-16">
      <div class="site-company-hero max-w-3xl">
        <nav aria-label="Breadcrumb" class="mb-8 md:mb-10">
          <ol class="flex flex-wrap items-center gap-2 text-sm text-white/60">
            <li><a href="{{ route('site.our-companies') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-white">Our Entities</a></li>
            <li aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </li>
            @if($divisionTitle)
              <li class="hidden text-white/60 sm:list-item">{{ $divisionTitle }}</li>
              <li aria-hidden="true" class="hidden sm:list-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </li>
            @endif
            <li class="font-semibold text-white" aria-current="page">{{ $company['name'] }}</li>
          </ol>
        </nav>

        @if($heroLogo)
          <div class="-ml-2 mb-4 flex h-20 items-center sm:h-24 md:mb-6 md:h-32">
            <img
              src="{{ $heroLogo }}"
              alt="{{ $company['name'] }}"
              class="h-full w-auto max-w-[min(100%,300px)] object-contain object-left brightness-0 invert sm:max-w-[360px] md:max-w-[440px]"
              onerror="this.parentElement.hidden=true"
            />
          </div>
        @endif

        @if(!empty($company['category']))
          <span class="ui-eyebrow ui-eyebrow--light">{{ $company['category'] }}</span>
        @endif
        <h1 class="mt-5 text-[2.5rem] leading-[1.04] font-extrabold tracking-[-0.035em] text-white sm:text-6xl md:text-7xl">{{ $company['name'] }}</h1>
        @if(filled($company['tagline'] ?? null))
          <p class="mt-5 max-w-2xl font-serif text-2xl leading-snug text-lagoon-300 italic sm:text-3xl md:mt-6 md:text-[2.4rem]">{{ $company['tagline'] }}</p>
        @endif

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center md:mt-10">
          <a href="tel:{{ $company['hotline'] }}" class="ui-btn ui-btn--light">
            <span class="-ml-2 flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $phoneIcon !!}</svg>
            </span>
            <span class="tabular-nums">{{ $company['hotline'] }}</span>
          </a>
          @if(!empty($company['email']))
            <a href="mailto:{{ $company['email'] }}" class="ui-btn ui-btn--glass">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $mailIcon !!}</svg>
              Email Us
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- AboutCompany — text first on mobile, image below --}}
  <section class="ui-section overflow-x-clip bg-white">
    <div class="ui-container">
      <div
        class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20"
        x-data="siteInViewReveal('aboutInView')"
      >
        <div
          class="site-company-motion-about-left order-1 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="aboutInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:-translate-x-[50px]'"
        >
          <span class="ui-eyebrow">Who we are</span>
          <h2 class="ui-h2 mt-5">
            About <span class="ui-accent text-brand-600">{{ $company['name'] }}</span>
          </h2>
          @if(filled($company['description'] ?? null))
            <p class="mt-6 text-lg leading-relaxed text-ink-700 sm:text-xl">
              {{ $company['description'] }}
            </p>
          @endif
          @if(filled($company['description_secondary'] ?? null))
            <p class="mt-4 text-base leading-relaxed text-ink-500 sm:text-lg">
              {{ $company['description_secondary'] }}
            </p>
          @endif

          <dl class="mt-8 grid grid-cols-1 gap-3 border-t border-ink-100 pt-8 sm:grid-cols-2">
            @if($divisionTitle)
              <div class="rounded-2xl bg-sand-100 px-5 py-4">
                <dt class="text-xs font-bold uppercase tracking-[0.16em] text-ink-400">Division</dt>
                <dd class="mt-1 font-semibold text-ink-900">{{ $divisionTitle }}</dd>
              </div>
            @endif
            @if(!empty($company['category']))
              <div class="rounded-2xl bg-sand-100 px-5 py-4">
                <dt class="text-xs font-bold uppercase tracking-[0.16em] text-ink-400">Sector</dt>
                <dd class="mt-1 font-semibold text-ink-900">{{ $company['category'] }}</dd>
              </div>
            @endif
          </dl>
        </div>

        <div
          class="site-company-motion-about-right relative order-2 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="aboutInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:translate-x-[50px]'"
        >
          <div class="absolute -top-8 -right-8 h-48 w-48 rounded-full bg-lagoon-300/40 blur-3xl" aria-hidden="true"></div>
          <div class="absolute -bottom-10 -left-8 h-48 w-48 rounded-full bg-brand-400/25 blur-3xl" aria-hidden="true"></div>
          @if(filled($aboutImageUrl))
            <div class="ui-zoom group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-gradient-to-br from-ink-800 to-ink-950 shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)] lg:aspect-[5/5]">
              <img
                src="{{ $aboutImageUrl }}"
                alt="{{ $company['name'] }}"
                class="h-full w-full object-cover"
                loading="lazy"
                decoding="async"
                onerror="this.hidden=true"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-ink-950/45 via-transparent to-transparent"></div>
            </div>
          @else
            <div class="ui-bg-ink relative flex aspect-[4/3] flex-col justify-between overflow-hidden rounded-[2rem] p-8 shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)] sm:p-10 lg:aspect-[5/5]">
              <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]" aria-hidden="true"></div>
              <span class="ui-eyebrow ui-eyebrow--light">A LITUS Group entity</span>
              <div>
                @if($heroLogo)
                  <img src="{{ $heroLogo }}" alt="" class="mb-6 h-16 w-auto max-w-[70%] object-contain object-left brightness-0 invert opacity-90 sm:h-20" loading="lazy" onerror="this.hidden=true" />
                @endif
                <p class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $company['name'] }}</p>
                @if(filled($company['tagline'] ?? null))
                  <p class="mt-2 font-serif text-xl text-lagoon-300 italic sm:text-2xl">{{ $company['tagline'] }}</p>
                @endif
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- ServicesSection --}}
  @if(count($services))
    <section class="ui-section relative bg-sand-100" x-data="siteInViewReveal('servicesInView')">
      <div class="ui-container">
        <div
          class="site-company-motion-services-header mb-12 grid grid-cols-1 items-end gap-6 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform] lg:grid-cols-12"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <div class="lg:col-span-7">
            <span class="ui-eyebrow">What we offer</span>
            <h2 class="ui-h2 mt-5">Our <span class="ui-accent text-brand-600">services</span></h2>
          </div>
          <p class="ui-lead lg:col-span-5">Comprehensive solutions tailored to meet your needs</p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
          @foreach($services as $index => $service)
            @php
              $serviceDisplay = \App\Support\CompanyPageIcons::resolveLabeledItem($service);
            @endphp
            <div
              class="site-company-motion-service-card h-full transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
              style="transition-delay: {{ $index * 100 }}ms"
              :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
            >
              <div class="ui-card ui-card--hover group relative flex h-full items-center gap-4 overflow-hidden p-5 sm:min-h-[13rem] sm:flex-col sm:items-stretch sm:justify-between sm:gap-0 sm:p-7">
                <span class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out-expo group-hover:w-full" aria-hidden="true"></span>
                <div class="flex shrink-0 items-start justify-between sm:w-full">
                  <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 ring-1 ring-brand-100 transition-all duration-500 group-hover:bg-white group-hover:shadow-[0_10px_30px_-12px_rgba(31,79,224,0.45)] sm:h-16 sm:w-16">
                    @if(filled($serviceDisplay['icon_url']))
                      <img
                        src="{{ $serviceDisplay['icon_url'] }}"
                        alt=""
                        class="h-8 w-8 object-contain sm:h-10 sm:w-10"
                        loading="lazy"
                        decoding="async"
                      />
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                    @endif
                  </span>
                  <span class="hidden font-serif text-4xl leading-none text-ink-200 italic tabular-nums transition-colors duration-500 group-hover:text-brand-400 sm:block">{{ sprintf('%02d', $index + 1) }}</span>
                </div>
                <h3 class="min-w-0 flex-1 text-base font-bold leading-snug tracking-[-0.01em] text-ink-900 sm:mt-8 sm:flex-none sm:text-xl">{{ $serviceDisplay['label'] }}</h3>
                <span class="font-serif text-2xl leading-none text-ink-200 italic tabular-nums sm:hidden" aria-hidden="true">{{ sprintf('%02d', $index + 1) }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- WhyChoose --}}
  @if(count($strengths))
    <section class="ui-section ui-bg-ink" x-data="siteInViewReveal('whyInView')">
      <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]" aria-hidden="true"></div>
      <div class="ui-container">
        <div
          class="site-company-motion-why-header mx-auto mb-12 max-w-3xl text-center transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
        >
          <span class="ui-eyebrow ui-eyebrow--light ui-eyebrow--center">Why choose us</span>
          <h2 class="ui-h2 mt-5 !text-white">
            Why choose <span class="ui-accent text-lagoon-300">{{ $company['name'] }}</span>
          </h2>
          <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg">
            Experience the difference that sets us apart from the competition
          </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
          @foreach($strengths as $index => $strength)
            @php
              $strengthDisplay = \App\Support\CompanyPageIcons::resolveLabeledItem($strength);
            @endphp
            <div
              class="site-company-motion-why-card h-full transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:will-change-[opacity,transform]"
              style="transition-delay: {{ $index * 100 }}ms"
              :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
            >
              <div class="ui-glass group relative flex h-full flex-col overflow-hidden rounded-3xl p-5 transition-all duration-500 hover:border-white/30 hover:bg-white/[0.1] sm:p-7">
                <div class="absolute -top-16 -right-16 h-36 w-36 rounded-full bg-brand-500/20 opacity-0 blur-3xl transition-opacity duration-500 group-hover:opacity-100" aria-hidden="true"></div>
                <div class="relative flex items-start justify-between">
                  @if(filled($strengthDisplay['icon_url']))
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-ink-700 shadow-lg shadow-brand-600/30 ring-1 ring-white/15 sm:h-14 sm:w-14">
                      <img
                        src="{{ $strengthDisplay['icon_url'] }}"
                        alt=""
                        class="h-7 w-7 object-contain sm:h-9 sm:w-9"
                        loading="lazy"
                        decoding="async"
                      />
                    </span>
                  @else
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-lagoon-500 text-white shadow-lg shadow-brand-600/30 sm:h-14 sm:w-14">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                  @endif
                  <span class="font-serif text-3xl leading-none text-white/20 italic tabular-nums sm:text-4xl">{{ sprintf('%02d', $index + 1) }}</span>
                </div>
                <h3 class="relative mt-8 text-base font-bold leading-snug tracking-tight text-white sm:mt-12 sm:text-xl">{{ $strengthDisplay['label'] }}</h3>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- ContactSection — form first on mobile --}}
  <section class="ui-section relative overflow-hidden bg-white">
    <div class="ui-grid-texture pointer-events-none absolute inset-0 -z-0 [mask-image:radial-gradient(ellipse_at_top_left,black,transparent_60%)] opacity-60" aria-hidden="true"></div>
    <div class="ui-container relative">
      <div
        class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-16"
        x-data="siteInViewReveal('contactInView')"
      >
        <div
          class="site-company-motion-contact-left order-2 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto lg:order-1 lg:col-span-5 md:will-change-[opacity,transform]"
          x-intersect.once.margin.0px.0px.0px.0px="reveal()"
          :class="contactInView ? 'opacity-100 translate-x-0 translate-y-0' : 'opacity-0 max-md:translate-y-[30px] max-md:translate-x-0 md:-translate-x-[50px]'"
        >
          <span class="ui-eyebrow">Contact</span>
          <h2 class="ui-h2 mt-5">Get in <span class="ui-accent text-brand-600">touch</span></h2>
          <p class="mt-6 text-base leading-relaxed text-ink-500 sm:text-lg">
            Have questions or ready to get started? Contact us today and discover how {{ $company['name'] }} can serve you.
          </p>

          <div class="mt-8 space-y-3 sm:mt-10">
            <a href="tel:{{ $company['hotline'] }}" class="ui-card ui-card--hover group flex items-center gap-4 p-4 sm:p-5">
              <span class="ui-icon-tile group-hover:bg-brand-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $phoneIcon !!}</svg>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block text-xs font-bold uppercase tracking-[0.16em] text-ink-400">Hotline</span>
                <span class="mt-0.5 block text-lg font-bold text-ink-900 tabular-nums transition-colors group-hover:text-brand-600">{{ $company['hotline'] }}</span>
              </span>
              <span class="text-ink-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-brand-600">{!! $arrow !!}</span>
            </a>

            @if(!empty($company['email']))
              <a href="mailto:{{ $company['email'] }}" class="ui-card ui-card--hover group flex items-center gap-4 p-4 sm:p-5">
                <span class="ui-icon-tile group-hover:bg-brand-600 group-hover:text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $mailIcon !!}</svg>
                </span>
                <span class="min-w-0 flex-1">
                  <span class="block text-xs font-bold uppercase tracking-[0.16em] text-ink-400">Email</span>
                  <span class="mt-0.5 block break-all text-base font-bold text-ink-900 transition-colors group-hover:text-brand-600 sm:text-lg">{{ $company['email'] }}</span>
                </span>
                <span class="text-ink-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-brand-600">{!! $arrow !!}</span>
              </a>
            @endif
          </div>

          <a href="{{ route('site.our-companies') }}" class="ui-link mt-10 text-sm">
            Explore all LITUS entities
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>

        <div
          class="site-company-motion-contact-right order-1 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto lg:order-2 lg:col-span-7 md:will-change-[opacity,transform]"
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
