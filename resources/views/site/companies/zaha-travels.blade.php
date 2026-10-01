@extends('layouts.site')

{{-- Bespoke Zaha Travels entity page (design: zaha-travels-litus.html). Shared navbar/footer come from the layout. --}}

@php
  $company = $company ?? [];
  $name = $company['name'] ?? 'Zaha Travels';
  $site = 'https://www.zahatravels.com';
  // Official logo from zahatravels.com: tightly cropped, so it lines up with the hero text.
  $logo = $site.'/images/logo-web.png';
  $maldivesImg = $site.'/storage/destinations/hero/maldives.jpeg';
  $sriLankaImg = $site.'/storage/destinations/hero/3PC3DLBPxxUgHfFQhHlwQtWDswuJlr-metaMHgwLndlYnA%3D-.webp';

  $external = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>';
  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';

  // Admin-editable fields (Companies → Zaha Travels); the approved design copy is the fallback.
  $mediaUrl = function (?string $raw): ?string {
    if (blank($raw)) {
      return null;
    }
    if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
      return $raw;
    }

    return \Illuminate\Support\Facades\Storage::disk('public')->url($raw);
  };
  $heroImageUrl = $mediaUrl($company['hero_image'] ?? null);
  $aboutImageUrl = $mediaUrl($company['about_image'] ?? null) ?? $sriLankaImg;
  $category = filled($company['category'] ?? null) ? $company['category'] : 'Travel & tourism';
  $tagline = filled($company['tagline'] ?? null) ? $company['tagline'] : 'Travel guided by Experts';
  $aboutPrimary = filled($company['description'] ?? null)
    ? $company['description']
    : 'Zaha Travels is a destination management company within LITUS Group, specialising in the Maldives and Sri Lanka. We serve individual travellers, travel agencies and tour operators with destination knowledge and personalised travel arrangements.';
  $aboutSecondary = $company['description_secondary'] ?? null;
  // First mention of the company name links to the official site (escaped first, then linked).
  $aboutPrimaryHtml = \Illuminate\Support\Str::replaceFirst(
    e($name),
    '<a href="'.$site.'/" class="font-semibold text-brand-600 underline decoration-brand-600/30 underline-offset-4 transition-colors hover:decoration-brand-600" target="_blank" rel="noopener">'.e($name).'</a>',
    e($aboutPrimary)
  );
  $hotline = $company['hotline'] ?? null;
  $email = $company['email'] ?? null;

  // Strengths → highlights strip. Caption comes from the admin, else the design's caption for known values.
  // A numeric strength such as "100+" is the partner count, reused in the property network section.
  $isCount = fn (string $label): bool => (bool) preg_match('/^\d[\d,]*\+?$/', $label);
  $proofCaptions = [
    'two destinations' => 'Maldives & Sri Lanka',
    'personal experts' => 'Guidance from start to finish',
    '24/7 support' => 'During your trip',
  ];
  $proof = collect($company['strengths'] ?? [])
    ->map(fn ($item) => \App\Support\CompanyPageIcons::resolveLabeledItem($item))
    ->filter(fn ($item) => $item['label'] !== '')
    ->map(fn ($item) => [
      $item['label'],
      $item['description']
        ?? ($isCount($item['label']) ? 'Resort & hotel partners' : ($proofCaptions[mb_strtolower($item['label'])] ?? null)),
    ])
    ->values()
    ->all();
  $partnerCount = collect($proof)->first(fn ($row) => $isCount($row[0]))[0] ?? '100+';

  $destinations = [
    [
      'title' => 'Maldives',
      'tag' => 'Island escapes',
      'image' => $site.'/storage/media-assets/T4Fu5EJ2hUnRCK0bNWKSCSQKnk0TVP-metaTWFsZGl2ZXMud2VicA%3D%3D-.webp',
      'alt' => 'Aerial view of a Maldives island resort and turquoise lagoon',
      'text' => 'Private island resorts, overwater villas and local island stays. Discover honeymoons, family holidays, snorkelling and marine adventures, with resort and transfer advice from our team.',
      'link' => $site.'/destinations/maldives',
      'cta' => 'Explore Maldives holidays',
    ],
    [
      'title' => 'Sri Lanka',
      'tag' => 'Discovery & culture',
      'image' => $sriLankaImg,
      'alt' => 'Sri Lanka destination scenery',
      'text' => 'Cultural landmarks, wildlife safaris, tea country and coastal retreats. Explore private tours, scenic rail journeys and beach holidays, with personalised itineraries supported by our Colombo team.',
      'link' => $site.'/destinations/sri-lanka',
      'cta' => 'Explore Sri Lanka tours',
    ],
  ];

  $partnerRows = [
    ['Maldives island stays', 'Beach villas, overwater retreats and stays selected around the island experience.'],
    ['Sri Lanka hotels & retreats', 'City hotels, boutique stays and coastal resorts to complement a personalised route.'],
    ['A stay that fits the journey', 'Thoughtful recommendations for couples, families and travellers combining destinations.'],
  ];

  // Services come from the admin. The admin description and uploaded icon win; known titles
  // fall back to the design's description and icon.
  $serviceDefaults = [
    'personal travel planning' => ['Destination advice and itineraries shaped around interests, preferences and budget.', '<circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36z"/>'],
    'stays & private tours' => ['Resort reservations, split stays and Sri Lanka touring for couples, families and groups.', '<path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/>'],
    'transfers & experiences' => ['Airport assistance, island connections, ground transport and memorable excursions.', '<path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-9-4-9 4c0 2.9.94 5.34 2.81 7.76"/><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6"/><path d="M12 10v4"/><path d="M12 2v3"/>'],
    'support during the journey' => ['Personal guidance before departure and 24/7 assistance during the trip.', '<path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>'],
    'combined holidays' => ['Sri Lanka discovery and Maldives relaxation, coordinated in one personalised journey.', '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>'],
    'travel trade partnerships' => ['Destination advice, property recommendations and booking coordination for agencies and tour operators.', '<path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/>'],
  ];
  $fallbackServiceIcon = '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>';
  $services = collect($company['services'] ?? [])
    ->map(fn ($item) => \App\Support\CompanyPageIcons::resolveLabeledItem($item))
    ->filter(fn ($item) => $item['label'] !== '')
    ->map(function ($item) use ($serviceDefaults, $fallbackServiceIcon) {
      [$text, $svg] = $serviceDefaults[mb_strtolower($item['label'])] ?? [null, $fallbackServiceIcon];

      return ['title' => $item['label'], 'text' => $item['description'] ?? $text, 'svg' => $svg, 'icon_url' => $item['icon_url']];
    })
    ->values()
    ->all();

  $offices = ['Malé · Headquarters', 'Colombo · Sri Lanka office', 'Dubai · Support office'];

  // JSON-LD: one @graph (with the site Organization/WebSite from SeoService), linked by @id.
  $seo = app(\App\Services\SeoService::class);
  $pageUrl = route('site.company', ['slug' => 'zaha-travels']);
  $zahaId = $site.'/#organization';
  $seo->setPageGraph([
    [
      '@type' => 'TravelAgency',
      '@id' => $zahaId,
      'name' => $name,
      'url' => $site.'/',
      'logo' => $site.'/images/logo-web.png',
      'slogan' => $tagline,
      'description' => 'A destination management company within LITUS Group, specialising in Maldives and Sri Lanka travel for individual travellers, travel agencies and tour operators.',
      'parentOrganization' => ['@id' => $seo->organizationId()],
      'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Malé', 'addressCountry' => 'MV'],
      'areaServed' => [
        ['@type' => 'Country', 'name' => 'Maldives'],
        ['@type' => 'Country', 'name' => 'Sri Lanka'],
      ],
    ],
    array_filter([
      '@type' => 'AboutPage',
      '@id' => $pageUrl.'#webpage',
      'url' => $pageUrl,
      'name' => \Artesaos\SEOTools\Facades\SEOMeta::getTitleSession(),
      'description' => \Artesaos\SEOTools\Facades\SEOMeta::getDescription(),
      'inLanguage' => 'en',
      'isPartOf' => ['@id' => $seo->websiteId()],
      'about' => ['@id' => $zahaId],
      'publisher' => ['@id' => $seo->organizationId()],
      'breadcrumb' => ['@id' => $pageUrl.'#breadcrumb'],
    ]),
    [
      '@type' => 'BreadcrumbList',
      '@id' => $pageUrl.'#breadcrumb',
      'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Our Entities', 'item' => route('site.our-companies')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $name, 'item' => $pageUrl],
      ],
    ],
  ]);
@endphp

@push('head')
  <link rel="preload" as="image" href="{{ $heroImageUrl ?? $maldivesImg }}" fetchpriority="high">
@endpush

@section('content')
<div data-company-detail data-company="zaha-travels">

  {{-- Hero --}}
  <section class="relative isolate flex min-h-[min(92svh,760px)] flex-col overflow-hidden bg-ink-950 text-white" aria-labelledby="page-title">
    <div class="absolute inset-0 -z-10 grid {{ $heroImageUrl ? 'grid-cols-1' : 'grid-cols-1 sm:grid-cols-2' }}" aria-hidden="true">
      @if($heroImageUrl)
        <img src="{{ $heroImageUrl }}" alt="" class="h-full w-full object-cover" fetchpriority="high" decoding="async">
      @else
        <img src="{{ $maldivesImg }}" alt="" class="h-full w-full object-cover" fetchpriority="high" decoding="async" width="1000" height="700">
        <img src="{{ $sriLankaImg }}" alt="" class="hidden h-full w-full object-cover sm:block" decoding="async" width="1000" height="700">
      @endif
      <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/55 to-ink-950/50"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/75 to-transparent"></div>
    </div>

    <div class="ui-container flex flex-1 flex-col justify-end pt-32 pb-12 md:pt-40 md:pb-16">
      <div class="max-w-3xl">
        <nav aria-label="Breadcrumb" class="mb-6">
          <ol class="flex flex-wrap items-center gap-2 text-sm text-ink-300">
            <li><a href="{{ url('/') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-white">Home</a></li>
            <li aria-hidden="true">{!! $chevron !!}</li>
            <li><a href="{{ route('site.our-companies') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-white">Our Entities</a></li>
            <li aria-hidden="true">{!! $chevron !!}</li>
            <li class="font-semibold text-white" aria-current="page">{{ $name }}</li>
          </ol>
        </nav>

        <div class="mb-8 flex h-12 items-center sm:h-14 md:mb-10 md:h-16">
          <img src="{{ $logo }}" alt="{{ $name }}" class="h-full w-auto max-w-full object-contain object-left brightness-0 invert" width="192" height="48" decoding="async" onerror="this.parentElement.hidden=true">
        </div>

        <span class="ui-eyebrow ui-eyebrow--light"><span>{{ $category }}<span class="hidden sm:inline"> · A LITUS Group company</span></span></span>
        <h1 id="page-title" class="mt-5 text-[2.75rem] leading-[1.04] font-extrabold tracking-[-0.035em] text-white sm:text-6xl md:text-7xl">{{ $name }}</h1>
        <p class="mt-4 font-serif text-3xl leading-snug text-lagoon-300 italic sm:text-4xl md:text-[2.4rem]">{{ $tagline }}</p>
        <p class="mt-5 max-w-xl text-base leading-relaxed text-ink-200 sm:text-lg">Personalised holidays and destination expertise in the Maldives and Sri Lanka.</p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
          <a href="{{ $site }}/" class="ui-btn ui-btn--light" target="_blank" rel="noopener">Explore Zaha Travels {!! $external !!}</a>
          @if(filled($hotline))
            <a href="tel:{{ preg_replace('/\s+/', '', $hotline) }}" class="ui-btn ui-btn--glass" aria-label="Call {{ $name }} on {{ $hotline }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <span>Call <span class="tabular-nums">{{ $hotline }}</span></span>
            </a>
          @endif
        </div>
      </div>
    </div>

    <div class="absolute right-5 bottom-7 hidden gap-3 text-[0.65rem] font-semibold tracking-[0.15em] uppercase md:flex lg:right-10" aria-hidden="true">
      <span class="ui-chip ui-chip--dark">Maldives</span>
      <span class="ui-chip ui-chip--dark">Sri Lanka</span>
    </div>
  </section>

  {{-- Highlights --}}
  @if(count($proof))
    <div class="border-b border-ink-100 bg-white">
      <div class="ui-container grid grid-cols-2 gap-y-6 py-8 md:grid-cols-4 md:py-10" aria-label="{{ $name }} highlights">
        @foreach($proof as $i => [$value, $caption])
          <div class="border-ink-100 {{ $i % 2 === 0 ? 'border-r pr-4' : 'pl-4' }} md:border-r md:px-6 md:first:pl-0 md:last:border-r-0">
            <strong class="block text-xl font-extrabold tracking-[-0.03em] text-ink-900 sm:text-2xl">{{ $value }}</strong>
            @if($caption)
              <span class="mt-1.5 block text-xs text-ink-500 sm:text-sm">{{ $caption }}</span>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- About --}}
  <section class="ui-section overflow-x-clip bg-white" aria-labelledby="intro-title">
    @php
      $pin = '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>';
      $glance = [
        ['Group', 'LITUS Group', '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>'],
        ['Headquarters', 'Malé, Maldives', $pin],
        ['Sri Lanka office', 'Colombo', $pin],
        ['Dubai presence', 'Support office', '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>'],
      ];
    @endphp
    <div class="ui-container grid grid-cols-1 items-center gap-14 lg:grid-cols-12 lg:gap-16 xl:gap-20">
      {{-- Story + key facts --}}
      <div class="lg:col-span-6">
        <span class="ui-eyebrow">Who we are</span>
        <h2 id="intro-title" class="ui-h2 mt-5">About <span class="ui-accent text-brand-600">{{ $name }}</span></h2>
        <p class="mt-6 text-lg leading-relaxed text-ink-700 sm:text-xl">{!! $aboutPrimaryHtml !!}</p>
        @if(filled($aboutSecondary))
          <p class="mt-4 text-base leading-relaxed text-ink-500 sm:text-lg">{{ $aboutSecondary }}</p>
        @endif

        <dl class="mt-10 grid grid-cols-2 gap-3" aria-label="{{ $name }} at a glance">
          @foreach($glance as [$term, $value, $icon])
            <div class="group flex flex-col items-start gap-3 rounded-2xl border border-ink-100 bg-sand-50 p-4 transition-colors duration-300 hover:border-brand-100 hover:bg-white sm:flex-row sm:p-5">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600 ring-1 ring-ink-100 transition-colors duration-300 group-hover:bg-brand-50 group-hover:ring-brand-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
              </span>
              <div class="min-w-0">
                <dt class="text-[0.65rem] font-bold tracking-[0.1em] text-ink-400 uppercase sm:text-[0.7rem] sm:tracking-[0.14em]">{{ $term }}</dt>
                <dd class="mt-1 text-sm font-bold text-ink-900 sm:text-base">{{ $value }}</dd>
              </div>
            </div>
          @endforeach
        </dl>

        {{-- Contact channels, each with a visible label --}}
        @php
          $channels = array_values(array_filter([
            filled($hotline) ? ['Phone', $hotline, 'tel:'.preg_replace('/\s+/', '', $hotline), false, '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>'] : null,
            filled($email) ? ['Email', $email, 'mailto:'.$email, false, '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>'] : null,
            ['Website', 'zahatravels.com', $site.'/', true, '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>'],
          ]));
        @endphp
        <ul class="mt-8 grid grid-cols-1 gap-x-6 gap-y-5 border-t border-ink-100 pt-8 sm:grid-cols-3" aria-label="Contact {{ $name }}">
          @foreach($channels as [$label, $value, $href, $isExternal, $icon])
            <li>
              <a href="{{ $href }}" class="group flex items-center gap-3" @if($isExternal) target="_blank" rel="noopener" @endif>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 ring-1 ring-brand-100 transition-colors duration-300 group-hover:bg-brand-600 group-hover:text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
                </span>
                <span class="min-w-0">
                  <span class="block text-[0.7rem] font-bold tracking-[0.14em] text-ink-400 uppercase">{{ $label }}</span>
                  <span class="block truncate text-sm font-semibold text-ink-900 transition-colors group-hover:text-brand-600 {{ $label === 'Phone' ? 'tabular-nums' : '' }}">{{ $value }}@if($isExternal) <span aria-hidden="true">↗</span>@endif</span>
                </span>
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Image --}}
      <div class="relative lg:col-span-6">
        <div class="absolute -top-10 -right-10 h-56 w-56 rounded-full bg-lagoon-300/40 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-10 -left-10 h-56 w-56 rounded-full bg-brand-400/20 blur-3xl" aria-hidden="true"></div>

        <figure class="ui-zoom relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-ink-800 shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)] lg:aspect-[4/5]">
          <img src="{{ $aboutImageUrl }}" alt="{{ $aboutImageUrl === $sriLankaImg ? 'Sri Lanka destination scenery' : $name }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="800" height="1000">
          <div class="absolute inset-0 bg-gradient-to-t from-ink-950/50 via-transparent to-transparent" aria-hidden="true"></div>
        </figure>
      </div>
    </div>
  </section>

  {{-- Destinations --}}
  <section class="ui-section bg-sand-100" aria-labelledby="destination-title">
    <div class="ui-container">
      <div class="mb-10 grid grid-cols-1 items-end gap-6 md:mb-14 lg:grid-cols-12">
        <div class="lg:col-span-7">
          <span class="ui-eyebrow">Our destinations</span>
          <h2 id="destination-title" class="ui-h2 mt-5">Explore our <span class="ui-accent text-brand-600">destinations</span></h2>
        </div>
        <p class="ui-lead lg:col-span-5">Explore each destination in its own right, or bring them together in one thoughtfully planned holiday.</p>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach($destinations as $d)
          <article class="ui-card ui-card--hover group flex flex-col overflow-hidden">
            <div class="ui-zoom relative aspect-[16/10] overflow-hidden bg-ink-700">
              <img src="{{ $d['image'] }}" alt="{{ $d['alt'] }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="900" height="600">
              <span class="ui-chip ui-chip--dark absolute bottom-5 left-5 bg-ink-900/60 text-[0.65rem] tracking-[0.12em] uppercase">{{ $d['tag'] }}</span>
            </div>
            <div class="flex flex-1 flex-col p-6 sm:p-8">
              <h3 class="text-2xl font-extrabold tracking-[-0.03em] text-ink-900 sm:text-3xl">{{ $d['title'] }}</h3>
              <p class="mt-4 mb-6 flex-1 text-base leading-relaxed text-ink-500">{{ $d['text'] }}</p>
              <a href="{{ $d['link'] }}" class="ui-link text-sm text-brand-600" target="_blank" rel="noopener">{{ $d['cta'] }} {!! $external !!}</a>
            </div>
          </article>
        @endforeach
      </div>

      <div class="mt-6 flex flex-col items-start justify-between gap-5 rounded-3xl bg-white p-6 sm:p-8 md:flex-row md:items-center">
        <div>
          <p class="text-lg font-bold tracking-[-0.015em] text-ink-900 sm:text-xl">Sri Lanka discovery. Maldives relaxation.</p>
          <p class="mt-1 text-sm text-ink-500">Combine a Sri Lanka tour with an island stay in the Maldives.</p>
        </div>
        <a href="{{ $site }}/destinations/maldives-sri-lanka" class="ui-link shrink-0 text-sm text-brand-600" target="_blank" rel="noopener">Explore combined holidays {!! $external !!}</a>
      </div>
    </div>
  </section>

  {{-- Property network --}}
  <section class="ui-section ui-bg-ink" aria-labelledby="partners-title">
    <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]" aria-hidden="true"></div>
    <div class="ui-container grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
      <div>
        <span class="ui-eyebrow ui-eyebrow--light">Our property network</span>
        <div class="mt-6 text-7xl leading-none font-extrabold tracking-[-0.05em] text-lagoon-300 sm:text-8xl" aria-hidden="true">{{ $partnerCount }}</div>
        <h2 id="partners-title" class="ui-h2 mt-4 !text-white">Resort &amp; hotel <span class="ui-accent text-lagoon-300">partners</span></h2>
        <p class="mt-6 text-base leading-relaxed text-ink-300 sm:text-lg">Our partner network opens up a broad choice of stays. Experts help travellers compare locations, room types and experiences, with access to partner rates and applicable offers.</p>
        <a href="{{ $site }}/properties" class="ui-link mt-8 text-sm !text-lagoon-300 hover:!text-white" target="_blank" rel="noopener">Explore resorts &amp; hotels {!! $external !!}</a>
      </div>

      <div class="grid gap-4">
        @foreach($partnerRows as $i => [$title, $text])
          <div class="ui-glass flex gap-5 rounded-3xl p-6 transition-colors duration-500 hover:bg-white/[0.1]">
            <span class="font-serif text-3xl leading-none text-lagoon-300 italic tabular-nums" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
            <div>
              <h3 class="text-lg font-bold tracking-tight text-white">{{ $title }}</h3>
              <p class="mt-1.5 text-sm leading-relaxed text-ink-300">{{ $text }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Services --}}
  @if(count($services))
  <section class="ui-section bg-sand-100" aria-labelledby="services-title">
    <div class="ui-container">
      <div class="mb-10 flex flex-col items-start justify-between gap-6 md:mb-14 md:flex-row md:items-end">
        <div>
          <span class="ui-eyebrow">What we do</span>
          <h2 id="services-title" class="ui-h2 mt-5">Our <span class="ui-accent text-brand-600">services</span></h2>
        </div>
        <a href="{{ $site }}/our-experts" class="ui-link text-sm text-brand-600" target="_blank" rel="noopener">Meet our travel experts {!! $external !!}</a>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
        @foreach($services as $i => $service)
          <article class="ui-card ui-card--hover group relative flex h-full flex-col overflow-hidden p-5 sm:p-7">
            <span class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out-expo group-hover:w-full" aria-hidden="true"></span>
            <span class="absolute top-5 right-5 font-serif text-2xl leading-none text-ink-200 italic tabular-nums transition-colors duration-500 group-hover:text-brand-400 sm:top-7 sm:right-7 sm:text-4xl" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
            <div class="flex items-center gap-4 pr-10 sm:block sm:pr-0">
              <span class="ui-icon-tile h-12 w-12 group-hover:bg-white group-hover:shadow-[0_10px_30px_-12px_rgba(31,79,224,0.45)] sm:h-14 sm:w-14">
                @if(filled($service['icon_url']))
                  <img src="{{ $service['icon_url'] }}" alt="" class="h-7 w-7 object-contain sm:h-8 sm:w-8" loading="lazy" decoding="async">
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $service['svg'] !!}</svg>
                @endif
              </span>
              <h3 class="text-base font-bold leading-snug tracking-[-0.01em] text-ink-900 sm:mt-8 sm:text-xl">{{ $service['title'] }}</h3>
            </div>
            @if(filled($service['text']))
              <p class="mt-3 text-sm leading-relaxed text-ink-500 sm:text-[0.95rem]">{{ $service['text'] }}</p>
            @endif
          </article>
        @endforeach
      </div>

      {{-- Experiences: contextual next step after the services --}}
      <div class="ui-bg-ink mt-6 flex flex-col items-start justify-between gap-6 rounded-3xl p-7 sm:p-9 md:flex-row md:items-center">
        <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_right,black,transparent_70%)]" aria-hidden="true"></div>
        <div class="max-w-xl">
          <p class="text-[0.7rem] font-bold tracking-[0.18em] text-lagoon-300 uppercase">Excursions &amp; activities</p>
          <h3 class="mt-2 text-xl font-extrabold tracking-[-0.02em] text-white sm:text-2xl">Make every day of the trip <span class="ui-accent text-lagoon-300">memorable</span></h3>
          <p class="mt-2 text-sm leading-relaxed text-ink-300 sm:text-base">Snorkelling, island hopping, safaris and cultural tours across the Maldives and Sri Lanka.</p>
        </div>
        <a href="{{ $site }}/experiences" class="ui-btn ui-btn--light shrink-0" target="_blank" rel="noopener">Explore travel experiences {!! $external !!}</a>
      </div>
    </div>
  </section>
  @endif

  {{-- Travellers & travel trade --}}
  <section class="ui-section bg-white" aria-labelledby="trade-title">
    <div class="ui-container grid grid-cols-1 items-start gap-10 lg:grid-cols-12 lg:gap-16">
      <div class="lg:col-span-7">
        <span class="ui-eyebrow">Travellers &amp; travel trade</span>
        <h2 id="trade-title" class="ui-h2 mt-5">Travellers &amp; <span class="ui-accent text-brand-600">travel partners</span></h2>
        <p class="mt-6 text-base leading-relaxed text-ink-500 sm:text-lg">Alongside personalised holidays, Zaha Travels supports travel agencies and tour operators with property recommendations, destination advice and booking coordination for the Maldives and Sri Lanka.</p>
        <a href="{{ $site }}/" class="ui-link mt-6 text-sm text-brand-600" target="_blank" rel="noopener">Connect with Zaha Travels {!! $external !!}</a>
        <ul class="mt-8 flex flex-wrap gap-2" aria-label="Office locations">
          @foreach($offices as $office)
            <li class="ui-chip">{{ $office }}</li>
          @endforeach
        </ul>
      </div>

      <aside class="rounded-[2rem] bg-sand-100 p-7 sm:p-10 lg:col-span-5">
        <span class="ui-eyebrow">Our group connection</span>
        <h3 class="mt-4 text-2xl font-extrabold tracking-[-0.03em] text-ink-900 sm:text-3xl">A member of <span class="ui-accent text-brand-600">LITUS Group</span></h3>
        <p class="mt-4 text-base leading-relaxed text-ink-500">Zaha Travels is part of LITUS Group, a diversified business group in the Maldives. This corporate connection is an integral part of our identity as a destination partner for travellers and the international travel trade.</p>
        <a href="{{ route('site.about') }}" class="ui-link mt-6 text-sm">Discover LITUS Group {!! $arrow !!}</a>
      </aside>
    </div>
  </section>

  {{-- Closing CTA --}}
  <section class="border-t border-ink-100 bg-white py-16 md:py-24" aria-labelledby="closing-title">
    <div class="ui-container flex flex-col items-start justify-between gap-10 lg:flex-row lg:items-center">
      <div class="max-w-2xl">
        <span class="ui-eyebrow">Discover more with Zaha Travels</span>
        <h2 id="closing-title" class="ui-h2 mt-5">Discover <span class="ui-accent text-brand-600">{{ $name }}</span></h2>
        <p class="mt-5 text-base leading-relaxed text-ink-500 sm:text-lg">Explore Maldives holidays, Sri Lanka tours and journeys that bring both destinations together.</p>
      </div>
      <div class="flex w-full shrink-0 flex-col items-center gap-4 sm:w-auto sm:items-start lg:items-center">
        <a href="{{ $site }}/" class="ui-btn ui-btn--dark w-full sm:w-auto" target="_blank" rel="noopener">Visit zahatravels.com {!! $external !!}</a>
        <a href="{{ $site }}/travel-packages" class="ui-link text-sm text-brand-600" target="_blank" rel="noopener">View travel packages {!! $external !!}</a>
      </div>
    </div>
  </section>
</div>
@endsection
