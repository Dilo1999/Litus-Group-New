@extends('layouts.site')

@section('content')
@php
  use App\Support\SiteData;

  $highlights = $heroSpotlightHighlights ?? [];
  $allCompanies = $companies ?? [];
  $displayCompanies = array_slice($allCompanies, 0, 8);

  $leadershipTeam = array_values(array_filter(
    SiteData::team(),
    fn ($m) => filled($m['image'] ?? null)
  ));

  $homeBlogPreviews = array_slice(SiteData::blogPosts(), 0, 4);
  $featuredPost = $homeBlogPreviews[0] ?? null;
  $morePosts = array_slice($homeBlogPreviews, 1, 3);

  $heroImagePath = \App\Models\SiteSetting::getValue('home.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;

  $whyChooseImagePath = \App\Models\SiteSetting::getValue('home.why_choose.image_path');
  $whyChooseImageUrl = $whyChooseImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($whyChooseImagePath) : null;

  $marqueeLogos = array_values(array_filter(array_map(
    fn ($c) => ['name' => $c['name'], 'slug' => $c['slug'], 'src' => SiteData::companyLogoUrl($c['logo'] ?? null)],
    $allCompanies
  ), fn ($l) => filled($l['src'])));

  $businessDivisions = array_filter(SiteData::divisions(), fn ($k) => $k !== 'corporate', ARRAY_FILTER_USE_KEY);

  $stats = [
    ['value' => count($allCompanies), 'label' => 'Entities under one group'],
    ['value' => count($businessDivisions), 'label' => 'Business divisions'],
    ['value' => 5, 'label' => 'LITUS core values'],
  ];

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $arrowUpRight = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>';
@endphp

<div>
  {{-- ============================== HERO ============================== --}}
  <section
    id="home"
    class="relative isolate flex min-h-[100svh] flex-col overflow-hidden bg-ink-950 text-white"
    @if(count($highlights) > 0)
      x-data="heroSpotlight(@js($highlights), @js($heroImageUrl))"
    @endif
  >
    <div class="absolute inset-0 -z-10 overflow-hidden">
      @if(count($highlights) > 0)
        <div class="hero-bg-slider-track flex h-full" :style="heroTrackStyle" x-show="heroSlides.length > 0">
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
        <img src="{{ $heroImageUrl }}" alt="" class="h-full w-full object-cover" fetchpriority="high" decoding="async" />
      @endif
      <div class="absolute inset-0 bg-ink-950/35"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/55 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/60 to-transparent"></div>
    </div>

    <div class="ui-container flex flex-1 flex-col justify-end pt-32 pb-10 md:pt-40 md:pb-14">
      <div class="grid grid-cols-1 items-end gap-10 lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-8">
          <div class="site-hero-title mb-6 inline-flex items-center gap-2.5 rounded-full border border-white/15 bg-white/10 py-1.5 pr-4 pl-1.5 text-xs font-semibold text-white/90 backdrop-blur-md sm:text-sm">
            <span class="rounded-full bg-lagoon-400 px-2.5 py-0.5 text-[0.7rem] font-bold uppercase tracking-wider text-ink-950">Maldives</span>
            A diversified business group
          </div>

          <h1 class="ui-h1 site-hero-title text-white">
            Taking Diversification
            <span class="block">
              <span class="ui-accent text-lagoon-300">to a whole</span>
              new level
            </span>
          </h1>

          <p class="site-hero-lead mt-6 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg md:mt-8 md:text-xl">
            From hospitality to construction, automotive to technology –
            LITUS Group delivers world-class services across 16 diverse brands.
          </p>

          <div class="site-hero-ctas mt-8 flex flex-col gap-3 sm:flex-row sm:items-center md:mt-10">
            <a href="{{ route('site.our-companies') }}" class="ui-btn ui-btn--primary">
              Explore Our Entities
              {!! $arrow !!}
            </a>
            <a href="{{ route('site.contact') }}" class="ui-btn ui-btn--glass">
              Contact Us
            </a>
          </div>
        </div>

        @if(count($highlights) > 0)
          {{-- Rotating spotlight: all companies with featured=true (DB order); Alpine cycles when 2+ --}}
          <div class="site-hero-card lg:col-span-4">
            <div class="ui-glass overflow-hidden rounded-3xl">
              <div class="flex items-center justify-between border-b border-white/10 px-5 py-3.5">
                <span class="flex items-center gap-2 text-[0.7rem] font-bold uppercase tracking-[0.2em] text-white/70">
                  <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-lagoon-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-lagoon-400"></span>
                  </span>
                  Featured entity
                </span>
                @if(count($highlights) > 1)
                  <span class="text-xs font-semibold tabular-nums text-white/60">
                    <span x-text="String(idx + 1).padStart(2, '0')"></span> / {{ sprintf('%02d', count($highlights)) }}
                  </span>
                @endif
              </div>
              <div class="relative min-h-[8.5rem] px-5 py-5">
                <div
                  x-show="visible"
                  x-transition:enter="hero-spotlight-tx-enter"
                  x-transition:enter-start="hero-spotlight-from-below"
                  x-transition:enter-end="hero-spotlight-at-rest"
                  x-transition:leave="hero-spotlight-tx-leave"
                  x-transition:leave-start="hero-spotlight-at-rest"
                  x-transition:leave-end="hero-spotlight-to-above"
                >
                  <div class="break-words text-2xl font-bold leading-tight tracking-tight text-white sm:text-[1.7rem]" x-text="items[idx].company"></div>
                  <a
                    x-show="items[idx].hotline && String(items[idx].hotline).trim().length"
                    class="group mt-4 inline-flex items-center gap-3 rounded-full bg-white py-1.5 pr-5 pl-1.5 text-sm font-semibold text-ink-900 transition-all hover:bg-lagoon-100"
                    :href="'tel:' + String(items[idx].hotline).replace(/\s/g, '')"
                  >
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-white">
                      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                      </svg>
                    </span>
                    <span class="whitespace-nowrap tabular-nums" x-text="items[idx].hotline"></span>
                  </a>
                  @if(count($highlights) > 1)
                    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-white/10">
                      <div class="ui-progress h-full bg-gradient-to-r from-lagoon-400 to-brand-400"></div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>

      <dl class="site-hero-ctas mt-12 grid grid-cols-3 gap-4 border-t border-white/15 pt-6 md:mt-16 md:pt-8">
        @foreach($stats as $stat)
          <div class="flex min-w-0 flex-col">
            <dt class="order-2 mt-1 text-xs leading-snug text-white/60 sm:text-sm">{{ $stat['label'] }}</dt>
            <dd class="order-1 text-3xl font-extrabold tracking-tight text-white tabular-nums sm:text-4xl md:text-5xl">
              {{ sprintf('%02d', $stat['value']) }}
            </dd>
          </div>
        @endforeach
      </dl>
    </div>
  </section>

  {{-- ============================== LOGO MARQUEE ============================== --}}
  @if(count($marqueeLogos) > 0)
    <section class="border-b border-ink-100 bg-white py-6 md:py-7" aria-label="Our brands">
      <p class="ui-container mb-5 text-center text-xs font-bold uppercase tracking-[0.22em] text-ink-400">The LITUS family of brands</p>
      <div class="ui-marquee overflow-hidden">
        <div class="ui-marquee__track items-center gap-14 md:gap-20">
          @foreach([0, 1] as $loopCopy)
            @foreach($marqueeLogos as $logo)
              <a
                href="{{ route('site.company', ['slug' => $logo['slug']]) }}"
                class="flex h-20 w-44 shrink-0 items-center justify-center opacity-60 grayscale transition-all duration-300 hover:opacity-100 hover:grayscale-0 md:h-24 md:w-56"
                @if($loopCopy === 1) aria-hidden="true" tabindex="-1" @endif
              >
                <img src="{{ $logo['src'] }}" alt="{{ $loopCopy === 0 ? $logo['name'] : '' }}" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async" />
              </a>
            @endforeach
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- ============================== ENTITIES ============================== --}}
  <section id="companies" class="ui-section bg-white" data-companies-overview>
    <div class="ui-container">
      <x-site.motion class="site-companies-overview-header mb-12 grid grid-cols-1 items-end gap-6 md:mb-16 lg:grid-cols-12" variant="fade-up">
        <div class="lg:col-span-7">
          <span class="ui-eyebrow">Our Entities</span>
          <h2 class="ui-h2 mt-5">
            A family of <span class="ui-accent text-brand-600">specialised</span> companies
          </h2>
        </div>
        <div class="lg:col-span-5">
          <p class="ui-lead">16 specialized companies delivering excellence across multiple industries.</p>
        </div>
      </x-site.motion>

      <div class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-4">
        @foreach($displayCompanies as $index => $company)
          @php
            $companyLogoSrc = SiteData::companyLogoUrl($company['logo'] ?? null);
          @endphp
          <x-site.motion class="site-companies-overview-card h-full" variant="fade-up" :delay="$index * 60" :duration="700">
            <a href="{{ route('site.company', ['slug' => $company['slug']]) }}" class="ui-card ui-card--hover group flex h-full flex-col p-3 sm:p-4">
              <div class="relative flex h-28 items-center justify-center overflow-hidden rounded-2xl bg-sand-50 p-5 transition-colors duration-500 group-hover:bg-brand-50 sm:h-36">
                @if($companyLogoSrc)
                  <img
                    src="{{ $companyLogoSrc }}"
                    alt="{{ $company['name'] }}"
                    class="max-h-full max-w-full object-contain transition-transform duration-700 ease-out-expo group-hover:scale-105"
                    loading="lazy"
                    decoding="async"
                  />
                @else
                  <x-site.lucide-icon :name="$company['icon'] ?? 'building2'" class="h-10 w-10 text-ink-400 transition-colors group-hover:text-brand-600" />
                @endif
                <span class="absolute top-2.5 right-2.5 flex h-8 w-8 translate-y-1 items-center justify-center rounded-full bg-ink-900 text-white opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                </span>
              </div>
              <div class="flex flex-1 flex-col px-1.5 pt-4 pb-1 sm:px-2">
                <h3 class="text-sm font-bold leading-snug text-ink-900 transition-colors group-hover:text-brand-600 sm:text-base">
                  {{ $company['name'] }}
                </h3>
                @if(!empty($company['category']))
                  <p class="mt-1 text-xs text-ink-400 sm:text-sm">{{ $company['category'] }}</p>
                @endif
              </div>
            </a>
          </x-site.motion>
        @endforeach
      </div>

      <x-site.motion class="site-companies-overview-cta mt-12 flex justify-center" variant="fade-up" :delay="200">
        <a href="{{ route('site.our-companies') }}" class="ui-btn ui-btn--dark">
          View All Entities
          {!! $arrow !!}
        </a>
      </x-site.motion>
    </div>
  </section>

  {{-- ============================== WHY CHOOSE ============================== --}}
  <section class="ui-section relative overflow-hidden bg-sand-100">
    <div class="ui-container">
      <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <x-site.motion variant="fade-up" class="relative order-2 lg:order-1">
          @if($whyChooseImageUrl)
            <div class="relative">
              <div class="absolute -top-6 -left-6 h-40 w-40 rounded-full bg-lagoon-300/40 blur-3xl" aria-hidden="true"></div>
              <div class="ui-zoom relative aspect-[4/5] overflow-hidden rounded-[2rem] shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)] sm:aspect-[5/5]">
                <img src="{{ $whyChooseImageUrl }}" alt="Why Choose LITUS Group" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                <div class="absolute inset-0 bg-gradient-to-t from-ink-950/50 via-transparent to-transparent"></div>
              </div>
              <div class="animate-float absolute right-3 bottom-6 w-52 rounded-3xl bg-white p-5 shadow-[0_30px_60px_-20px_rgba(6,22,52,0.35)] sm:-right-8 sm:bottom-8 sm:w-64">
                <p class="text-4xl font-extrabold tracking-tight text-ink-900">{{ count($allCompanies) }}<span class="text-brand-600">+</span></p>
                <p class="mt-1 text-sm leading-snug text-ink-500">Specialised entities, one trusted group</p>
              </div>
            </div>
          @else
            <div class="ui-bg-ink rounded-[2rem] p-10">
              <p class="text-7xl font-extrabold tracking-tight">{{ count($allCompanies) }}<span class="text-lagoon-400">+</span></p>
              <p class="mt-3 text-lg text-ink-300">Specialised entities, one trusted group</p>
            </div>
          @endif
        </x-site.motion>

        <x-site.motion variant="fade-up" :delay="150" class="order-1 lg:order-2">
          <span class="ui-eyebrow">Why LITUS</span>
          <h2 class="ui-h2 mt-5">Why choose <span class="ui-accent text-brand-600">LITUS Group</span></h2>
          <p class="mt-6 text-base leading-relaxed text-ink-500 sm:text-lg">
            LITUS Group stands as a beacon of diversification and excellence in the Maldives business landscape. With 16 specialized companies spanning multiple industries, we deliver comprehensive solutions that drive growth and create lasting value.
          </p>
          <p class="mt-4 text-base leading-relaxed text-ink-500 sm:text-lg">
            Our commitment to quality, innovation, and customer satisfaction has made us a trusted partner for businesses and individuals alike.
          </p>

          <ul class="mt-8 flex flex-wrap gap-2.5 sm:grid sm:grid-cols-3 sm:gap-3">
            @foreach(['Quality', 'Innovation', 'Customer satisfaction'] as $pillar)
              <li class="flex items-center gap-2.5 rounded-full bg-white py-2 pr-4 pl-2 sm:gap-3 sm:rounded-2xl sm:px-4 sm:py-3.5 text-sm font-semibold text-ink-800 ring-1 ring-ink-100">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                </span>
                {{ $pillar }}
              </li>
            @endforeach
          </ul>

          <div class="mt-10">
            <a href="{{ route('site.about') }}" class="ui-btn ui-btn--primary">
              Learn More About Us
              {!! $arrow !!}
            </a>
          </div>
        </x-site.motion>
      </div>
    </div>
  </section>

  {{-- ============================== MISSION / VISION ============================== --}}
  <section class="ui-section ui-bg-ink" data-home-mission-vision>
    <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]" aria-hidden="true"></div>
    <div class="ui-container">
      <x-site.motion class="mx-auto mb-14 max-w-3xl text-center md:mb-20" variant="fade-up">
        <span class="ui-eyebrow ui-eyebrow--light ui-eyebrow--center">Our Purpose</span>
        <h2 class="ui-h2 mt-5 !text-white">Driven by purpose, <span class="ui-accent text-lagoon-300">guided by vision</span></h2>
      </x-site.motion>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
        @foreach([
          ['no' => '01', 'title' => 'Our Mission', 'text' => 'To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction.', 'icon' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>'],
          ['no' => '02', 'title' => 'Our Vision', 'text' => 'To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders.', 'icon' => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>'],
        ] as $i => $mv)
          <x-site.motion variant="fade-up" :delay="$i * 150">
            <div class="ui-glass group relative h-full overflow-hidden rounded-[2rem] p-8 transition-all duration-500 hover:border-white/30 hover:bg-white/[0.1] sm:p-10 lg:p-12">
              <div class="absolute -top-24 -right-24 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl transition-opacity duration-500 group-hover:opacity-100 md:opacity-60" aria-hidden="true"></div>
              <div class="relative flex items-start justify-between">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-lagoon-500 text-white shadow-lg shadow-brand-600/30">
                  <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $mv['icon'] !!}</svg>
                </span>
                <span class="font-serif text-5xl italic text-white/20">{{ $mv['no'] }}</span>
              </div>
              <h3 class="relative mt-10 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $mv['title'] }}</h3>
              <p class="relative mt-4 text-base leading-relaxed text-ink-200 sm:text-lg">{{ $mv['text'] }}</p>
            </div>
          </x-site.motion>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ============================== CORE VALUES ============================== --}}
  <section class="ui-section bg-white">
    <div class="ui-container">
      <x-site.motion class="mb-12 grid grid-cols-1 items-end gap-6 md:mb-16 lg:grid-cols-12" variant="fade-up">
        <div class="lg:col-span-7">
          <span class="ui-eyebrow">Our Core Values</span>
          <h2 class="ui-h2 mt-5">The <span class="ui-accent text-brand-600">LITUS</span> principles</h2>
        </div>
        <p class="ui-lead lg:col-span-5">The LITUS principles that guide everything we do.</p>
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

      <div class="grid grid-cols-1 overflow-hidden rounded-[2rem] border border-ink-100 sm:grid-cols-2 lg:grid-cols-5">
        @foreach($values as $i => $v)
          <x-site.motion :delay="$i * 90" :duration="700" variant="fade-up" class="h-full {{ $i === 4 ? 'sm:col-span-2 lg:col-span-1' : '' }}">
            <div class="group relative flex h-full items-start gap-5 border-ink-100 p-6 transition-colors duration-500 hover:bg-ink-900 sm:p-8 lg:flex-col lg:gap-0 {{ $i < 4 ? 'max-lg:border-b lg:border-r' : '' }} {{ $i % 2 === 0 && $i < 4 ? 'sm:max-lg:border-r' : '' }}">
              <span class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out-expo group-hover:w-full" aria-hidden="true"></span>
              <span class="bg-gradient-to-br from-brand-600 to-lagoon-500 bg-clip-text font-serif text-7xl leading-none text-transparent italic transition-all duration-500 lg:text-[7rem]">{{ $v['letter'] }}</span>
              <div class="lg:mt-10">
                <h3 class="text-lg font-bold tracking-tight text-ink-900 transition-colors duration-500 group-hover:text-white sm:text-xl">{{ $v['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink-500 transition-colors duration-500 group-hover:text-ink-300 sm:text-[0.95rem]">{{ $v['description'] }}</p>
              </div>
            </div>
          </x-site.motion>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ============================== LEADERSHIP ============================== --}}
  <section class="ui-section ui-bg-ink">
    <div class="ui-container">
      <x-site.motion class="mb-12 flex flex-col gap-6 md:mb-16 md:flex-row md:items-end md:justify-between" variant="fade-up">
        <div class="max-w-2xl">
          <span class="ui-eyebrow ui-eyebrow--light">Our Leadership</span>
          <h2 class="ui-h2 mt-5 !text-white">Meet the <span class="ui-accent text-lagoon-300">visionaries</span></h2>
          <p class="mt-5 text-base leading-relaxed text-ink-300 sm:text-lg">Meet the visionary leaders driving LITUS Group's success across all sectors.</p>
        </div>
        <a href="{{ route('site.team') }}" class="ui-btn ui-btn--light shrink-0 self-start md:self-auto">
          View Full Team
          {!! $arrow !!}
        </a>
      </x-site.motion>

      @if(count($leadershipTeam) > 0)
        <div class="-mx-5 flex snap-x snap-mandatory gap-3 overflow-x-auto px-5 pb-2 [scrollbar-width:none] sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 sm:pb-0 [&::-webkit-scrollbar]:hidden {{ count($leadershipTeam) >= 4 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
          @foreach($leadershipTeam as $index => $member)
            <x-site.motion :delay="$index * 90" :duration="700" variant="fade-up" class="h-full w-[78%] shrink-0 snap-start sm:w-auto">
              <article class="group relative aspect-[4/5] overflow-hidden rounded-3xl bg-ink-800 ring-1 ring-white/10">
                <img
                  src="{{ $member['image'] }}"
                  alt="{{ $member['name'] }}"
                  class="absolute inset-0 h-full w-full object-cover object-top transition-transform duration-[1200ms] ease-out-expo group-hover:scale-105"
                  loading="lazy"
                  decoding="async"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/20 to-transparent"></div>

                @if(!empty($member['linkedin_url']) || !empty($member['email']))
                  <div class="absolute top-3 right-3 flex gap-2 transition-all duration-500 md:translate-y-[-6px] md:opacity-0 md:group-hover:translate-y-0 md:group-hover:opacity-100">
                    @if(!empty($member['linkedin_url']))
                      <a href="{{ $member['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" class="ui-icon-btn !h-9 !w-9 bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-md hover:bg-white hover:text-ink-900" aria-label="LinkedIn — {{ $member['name'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" /><rect width="4" height="12" x="2" y="9" /><circle cx="4" cy="4" r="2" /></svg>
                      </a>
                    @endif
                    @if(!empty($member['email']))
                      <a href="mailto:{{ $member['email'] }}" class="ui-icon-btn !h-9 !w-9 bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-md hover:bg-white hover:text-ink-900" aria-label="Email — {{ $member['name'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /></svg>
                      </a>
                    @endif
                  </div>
                @endif

                <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6">
                  <h3 class="text-base font-bold leading-snug text-white sm:text-xl">{{ $member['name'] }}</h3>
                  @if(!empty($member['role']))
                    <p class="mt-1 text-xs font-medium leading-snug text-lagoon-300 sm:text-sm">{{ $member['role'] }}</p>
                  @endif
                </div>
              </article>
            </x-site.motion>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  {{-- ============================== NEWS ============================== --}}
  <section class="ui-section bg-sand-100">
    <div class="ui-container">
      <x-site.motion class="mb-12 flex flex-col gap-6 md:mb-16 md:flex-row md:items-end md:justify-between" variant="fade-up">
        <div class="max-w-2xl">
          <span class="ui-eyebrow">News & Media</span>
          <h2 class="ui-h2 mt-5">Latest <span class="ui-accent text-brand-600">stories</span></h2>
          <p class="ui-lead mt-5">Stay updated with the latest stories and insights from across the LITUS Group.</p>
        </div>
        <a href="{{ route('site.blogs') }}" class="ui-btn ui-btn--outline shrink-0 self-start md:self-auto">
          Read More
          {!! $arrow !!}
        </a>
      </x-site.motion>

      @if($featuredPost)
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 lg:gap-6">
          <x-site.motion variant="fade-up" class="lg:col-span-7">
            <a href="{{ route('site.blog-article', ['slug' => $featuredPost['slug']]) }}" class="group relative block h-full min-h-[22rem] overflow-hidden rounded-[2rem] bg-ink-900 sm:min-h-[28rem]">
              @if(filled($featuredPost['image'] ?? null))
                <div class="ui-zoom absolute inset-0">
                  <img src="{{ $featuredPost['image'] }}" alt="{{ $featuredPost['title'] }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                </div>
              @endif
              <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/40 to-transparent"></div>
              <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
                <div class="flex flex-wrap items-center gap-2">
                  @if(filled($featuredPost['category'] ?? null))
                    <span class="ui-chip ui-chip--dark">{{ $featuredPost['category'] }}</span>
                  @endif
                  @if(filled($featuredPost['date'] ?? null))
                    <span class="text-sm text-white/70">{{ $featuredPost['date'] }}</span>
                  @endif
                </div>
                <h3 class="mt-4 max-w-2xl text-2xl font-bold leading-tight tracking-tight text-white sm:text-3xl lg:text-4xl">{{ $featuredPost['title'] }}</h3>
                <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-lagoon-300 transition-all group-hover:gap-3">
                  Read story {!! $arrow !!}
                </span>
              </div>
            </a>
          </x-site.motion>

          <div class="flex flex-col gap-4 lg:col-span-5">
            @foreach($morePosts as $i => $post)
              <x-site.motion :delay="($i + 1) * 100" variant="fade-up" class="flex-1">
                <a href="{{ route('site.blog-article', ['slug' => $post['slug']]) }}" class="ui-card ui-card--hover group flex h-full items-center gap-4 p-3 sm:gap-5">
                  <div class="ui-zoom relative aspect-square w-24 shrink-0 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-lagoon-100 sm:w-32">
                    @if(filled($post['image'] ?? null))
                      <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                    @endif
                  </div>
                  <div class="min-w-0 flex-1 pr-2">
                    <div class="flex flex-wrap items-center gap-x-2 text-xs text-ink-400">
                      @if(filled($post['category'] ?? null))
                        <span class="font-bold uppercase tracking-wider text-brand-600">{{ $post['category'] }}</span>
                      @endif
                      @if(filled($post['date'] ?? null))
                        <span>{{ $post['date'] }}</span>
                      @endif
                    </div>
                    <h3 class="mt-2 line-clamp-2 text-[0.95rem] font-bold leading-snug text-ink-900 transition-colors group-hover:text-brand-600 sm:text-lg">{{ $post['title'] }}</h3>
                  </div>
                </a>
              </x-site.motion>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </section>

  {{-- ============================== CTA DUO ============================== --}}
  <section class="bg-white py-20 md:py-28">
    <div class="ui-container grid grid-cols-1 gap-5 lg:grid-cols-2">
      <x-site.motion variant="fade-up">
        <div class="ui-bg-ink relative flex h-full flex-col justify-between overflow-hidden rounded-[2rem] p-8 sm:p-12">
          <div>
            <span class="ui-eyebrow ui-eyebrow--light">Careers</span>
            <h2 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">Join <span class="ui-accent text-lagoon-300">our team</span></h2>
            <p class="mt-4 max-w-md text-base leading-relaxed text-ink-300 sm:text-lg">
              Build your career with LITUS Group and be part of a dynamic team that's shaping the future across 16 diverse companies.
            </p>
          </div>
          <div class="mt-10">
            <a href="{{ route('site.careers') }}" class="ui-btn ui-btn--light">
              Explore Careers
              {!! $arrow !!}
            </a>
          </div>
        </div>
      </x-site.motion>

      <x-site.motion variant="fade-up" :delay="150">
        <div class="relative isolate flex h-full flex-col justify-between overflow-hidden rounded-[2rem] bg-brand-600 p-8 text-white sm:p-12">
          <div class="absolute -right-20 -bottom-20 -z-10 h-72 w-72 rounded-full bg-lagoon-400/40 blur-3xl" aria-hidden="true"></div>
          <div class="ui-grid-texture--light absolute inset-0 -z-10 opacity-70" aria-hidden="true"></div>
          <div>
            <span class="ui-eyebrow !text-white/80">Get in touch</span>
            <h2 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Let's <span class="ui-accent">connect</span></h2>
            <p class="mt-4 max-w-md text-base leading-relaxed text-white/80 sm:text-lg">
              Have questions or interested in our services? Get in touch with us today.
            </p>
          </div>
          <div class="mt-10">
            <a href="{{ route('site.contact') }}" class="ui-btn bg-white text-brand-700 shadow-lg hover:-translate-y-0.5 hover:bg-ink-900 hover:text-white">
              Contact Us
              {!! $arrowUpRight !!}
            </a>
          </div>
        </div>
      </x-site.motion>
    </div>
  </section>
</div>
@endsection
