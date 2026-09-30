@extends('layouts.site')

@section('content')
@php
  use App\Support\SiteData;
  $heroImagePath = \App\Models\SiteSetting::getValue('our_companies.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('our_companies.hero.position_y', 50);
  $divisions = $divisions ?? SiteData::divisions();
  $companies = $companies ?? SiteData::companies();
  $divisionOrder = $divisionOrder ?? [
    'corporate',
    'logistics-shipping',
    'automotive',
    'trading',
    'construction',
    'technology-retail',
    'hospitality-lifestyle',
  ];

  // Divisions that actually have entities (drives the jump-nav + numbering).
  $activeDivisions = [];
  foreach ($divisionOrder as $divisionKey) {
    $division = $divisions[$divisionKey] ?? null;
    $count = count(array_filter($companies, fn ($c) => ($c['division'] ?? '') === $divisionKey));
    if ($division && $count) {
      $activeDivisions[$divisionKey] = ['title' => $division['title'], 'count' => $count];
    }
  }
  $divisionNumbers = array_flip(array_keys($activeDivisions));
@endphp

{{-- PageHero --}}
<section class="ui-page-hero">
  @if(filled($heroImageUrl))
    <div class="absolute inset-0 -z-20" aria-hidden="true">
      <img
        src="{{ $heroImageUrl }}"
        alt="Our Companies hero"
        class="h-full w-full object-cover"
        style="object-position: 50% {{ $heroPosY }}%;"
        fetchpriority="high"
        decoding="async"
      />
      <div class="absolute inset-0 bg-ink-950/45"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/20"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-900 via-ink-950/60 to-transparent"></div>
    </div>
  @endif
  <div class="ui-page-hero__glow"></div>
  <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_top_right,black,transparent_70%)]" aria-hidden="true"></div>

  <div class="ui-container">
    <div class="site-our-companies-hero grid grid-cols-1 items-end gap-10 lg:grid-cols-12 lg:gap-8">
      <div class="lg:col-span-8">
        <span class="ui-eyebrow ui-eyebrow--light">The LITUS Group portfolio</span>
        <h1 class="ui-h1 mt-6 text-white">
          Our <span class="ui-accent text-lagoon-300">Entities</span>
        </h1>
        <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg md:mt-8 md:text-xl">
          Explore our diverse portfolio of {{ count($companies) }} specialized companies delivering excellence across multiple industries
        </p>
      </div>

      <dl class="grid grid-cols-2 gap-3 lg:col-span-4">
        <div class="ui-glass flex flex-col rounded-3xl p-5 sm:p-6">
          <dt class="order-2 mt-1 text-sm text-ink-300">Entities</dt>
          <dd class="order-1 text-4xl font-extrabold tracking-tight text-white tabular-nums sm:text-5xl">{{ sprintf('%02d', count($companies)) }}</dd>
        </div>
        <div class="ui-glass flex flex-col rounded-3xl p-5 sm:p-6">
          <dt class="order-2 mt-1 text-sm text-ink-300">Divisions</dt>
          <dd class="order-1 text-4xl font-extrabold tracking-tight text-white tabular-nums sm:text-5xl">{{ sprintf('%02d', count($activeDivisions)) }}</dd>
        </div>
      </dl>
    </div>

    @if(count($activeDivisions))
      <nav class="site-our-companies-hero mt-12 border-t border-white/10 pt-6 md:mt-16" aria-label="Jump to division">
        <ul class="-mx-5 flex gap-2 overflow-x-auto px-5 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0 [&::-webkit-scrollbar]:hidden">
          @foreach($activeDivisions as $key => $meta)
            <li class="shrink-0">
              <a
                href="#division-{{ $key }}"
                class="inline-flex min-h-11 items-center gap-2.5 rounded-full border border-white/15 bg-white/[0.07] py-2 pr-4 pl-2 text-sm font-semibold text-white backdrop-blur-md transition-all duration-300 hover:border-lagoon-300/60 hover:bg-white/15"
              >
                <span class="flex h-7 min-w-7 items-center justify-center rounded-full bg-white/10 px-1.5 text-xs font-bold text-lagoon-300 tabular-nums">{{ $meta['count'] }}</span>
                {{ $meta['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </nav>
    @endif
  </div>
</section>

{{-- CompaniesByDivision --}}
<section
  class="ui-section bg-white"
  x-data="{
    inView: false,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
      else if (window.matchMedia('(max-width: 767px)').matches) this.inView = true;
    }
  }"
  x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
  data-companies-stagger
>
  <div class="ui-container">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-12">
      {{-- Desktop sticky division index --}}
      @if(count($activeDivisions))
        <aside class="hidden lg:col-span-3 lg:block">
          <div class="sticky top-32">
            <p class="text-[0.72rem] font-bold uppercase tracking-[0.22em] text-ink-400">Divisions</p>
            <ul class="mt-5 space-y-1 border-l border-ink-100">
              @foreach($activeDivisions as $key => $meta)
                <li>
                  <a
                    href="#division-{{ $key }}"
                    class="group -ml-px flex items-center justify-between gap-3 border-l-2 border-transparent py-2.5 pr-2 pl-5 text-[0.95rem] font-semibold text-ink-500 transition-colors duration-300 hover:border-brand-600 hover:text-ink-900"
                  >
                    <span class="flex items-baseline gap-3">
                      <span class="font-serif text-base text-ink-300 italic tabular-nums transition-colors group-hover:text-brand-600">{{ sprintf('%02d', $divisionNumbers[$key] + 1) }}</span>
                      {{ $meta['title'] }}
                    </span>
                    <span class="text-xs font-bold text-ink-300 tabular-nums">{{ $meta['count'] }}</span>
                  </a>
                </li>
              @endforeach
            </ul>

            <div class="ui-bg-ink mt-10 rounded-3xl p-6">
              <p class="text-sm leading-relaxed text-ink-300">Looking for the right LITUS entity for your needs?</p>
              <a href="{{ route('site.contact') }}" class="ui-btn ui-btn--light ui-btn--sm mt-5">
                Contact Us
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </a>
            </div>
          </div>
        </aside>
      @endif

      <div class="space-y-16 md:space-y-24 lg:col-span-9">
        @foreach($divisionOrder as $divIndex => $divisionKey)
          @php
            $division = $divisions[$divisionKey] ?? null;
            $divisionCompanies = array_values(array_filter($companies, fn ($c) => ($c['division'] ?? '') === $divisionKey));
          @endphp

          @if($division && count($divisionCompanies))
            <div id="division-{{ $divisionKey }}" class="scroll-mt-28">
              <div
                class="site-companies-motion-division transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
                :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
                style="transition-delay: {{ $divIndex * 100 }}ms"
              >
                <div class="mb-6 border-b border-ink-100 pb-6 md:mb-8">
                  <div class="flex items-start gap-4 sm:gap-5">
                    <span class="bg-gradient-to-br from-brand-600 to-lagoon-500 bg-clip-text pt-0.5 font-serif text-5xl leading-none text-transparent italic tabular-nums sm:text-6xl">{{ sprintf('%02d', ($divisionNumbers[$divisionKey] ?? 0) + 1) }}</span>
                    <div>
                      <h2 class="text-2xl font-extrabold tracking-[-0.025em] text-ink-900 sm:text-3xl md:text-[2.25rem] md:leading-tight">{{ $division['title'] }}</h2>
                      <p class="mt-1.5 text-sm leading-relaxed text-ink-500 sm:text-base">{{ $division['description'] }}</p>
                      <span class="ui-chip mt-3 bg-sand-50">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-600" aria-hidden="true"></span>
                        <span class="tabular-nums">{{ count($divisionCompanies) }}</span> {{ count($divisionCompanies) === 1 ? 'entity' : 'entities' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5 xl:grid-cols-3">
                @foreach($divisionCompanies as $index => $company)
                  <div
                    class="site-companies-motion-card h-full transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
                    :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    style="transition-delay: {{ $divIndex * 100 + $index * 50 }}ms"
                  >
                    <div class="ui-card ui-card--hover group relative flex h-full cursor-pointer flex-row items-center gap-4 p-3 sm:flex-col sm:items-stretch sm:gap-0">
                      <a
                        href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                        class="absolute inset-0 z-10 rounded-3xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                        aria-label="View {{ $company['name'] }}"
                      ></a>

                      @php
                        $logoSrc = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
                      @endphp
                      <div class="relative z-0 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-sand-50 to-sand-100 p-3 ring-1 ring-ink-100/70 transition-colors duration-500 group-hover:from-brand-50 group-hover:to-lagoon-100/60 sm:h-36 sm:w-full sm:p-6">
                        @if($logoSrc)
                          <img
                            src="{{ $logoSrc }}"
                            alt="{{ $company['name'] }}"
                            class="max-h-full max-w-full object-contain transition-transform duration-700 ease-out-expo group-hover:scale-105 sm:max-h-20 sm:max-w-[80%]"
                            loading="lazy"
                            decoding="async"
                            onerror="this.hidden=true;this.nextElementSibling.hidden=false"
                          />
                        @endif
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-sm ring-1 ring-ink-100 transition-colors duration-500 group-hover:bg-brand-600 group-hover:text-white sm:h-16 sm:w-16" @if($logoSrc) hidden @endif>
                          <x-site.lucide-icon :name="$company['icon'] ?? 'building2'" class="h-6 w-6 sm:h-8 sm:w-8" />
                        </span>
                        <span class="absolute top-2.5 right-2.5 hidden h-9 w-9 translate-y-1 items-center justify-center rounded-full bg-ink-900 text-white opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100 sm:flex" aria-hidden="true">
                          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                        </span>
                      </div>

                      <div class="relative z-0 flex min-w-0 flex-1 flex-col sm:px-2 sm:pt-5">
                        @if(!empty($company['category']))
                          <p class="text-[0.7rem] font-bold uppercase tracking-[0.14em] text-brand-600">{{ $company['category'] }}</p>
                        @endif
                        <h3 class="mt-1 text-base font-bold leading-snug tracking-[-0.01em] text-ink-900 transition-colors group-hover:text-brand-600 sm:text-lg">
                          {{ $company['name'] }}
                        </h3>
                        @if(!empty($company['description']))
                          <p class="mt-2 mb-5 hidden text-sm leading-relaxed text-ink-500 sm:line-clamp-3">{{ $company['description'] }}</p>
                        @endif

                        <div class="relative z-20 mt-2 flex items-center justify-between gap-3 sm:mt-auto sm:border-t sm:border-ink-100 sm:pt-4 sm:pb-1">
                          @if(!empty($company['hotline']))
                            <a
                              href="tel:{{ $company['hotline'] }}"
                              class="-my-2 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-ink-600 transition-colors hover:text-brand-600"
                              onclick="event.stopPropagation()"
                            >
                              <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                              </span>
                              <span class="truncate tabular-nums">{{ $company['hotline'] }}</span>
                            </a>
                          @endif
                          <span class="pointer-events-none ml-auto hidden items-center gap-1 text-sm font-semibold text-ink-900 transition-all duration-300 group-hover:gap-2 group-hover:text-brand-600 sm:inline-flex">
                            View
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                              <path d="M5 12h14" />
                              <path d="m12 5 7 7-7 7" />
                            </svg>
                          </span>
                        </div>
                      </div>

                      <span class="pointer-events-none flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sand-100 text-ink-900 transition-colors group-hover:bg-ink-900 group-hover:text-white sm:hidden" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  </div>
</section>
@endsection
