@extends('layouts.site')

@section('content')
@php
  $heroImagePath = \App\Models\SiteSetting::getValue('about.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('about.hero.position_y', 43);
  $aboutIntro1 = \App\Models\SiteSetting::getValue(
    'about.intro.paragraph_1',
    'LITUS Group brings together businesses in automotive, building materials, logistics, engineering, currency exchange, travel, hospitality, construction and technology. Based in the Maldives, we serve individuals, businesses, resorts and project teams through companies specialising in their respective industries.'
  );
  $aboutIntro2 = \App\Models\SiteSetting::getValue(
    'about.intro.paragraph_2',
    'From motorcycle spare parts and home improvements to freight movements and resort engineering support, our businesses provide practical products and services that help customers keep everyday life and business moving.'
  );

  $heroTags = ['Automotive', 'Trading', 'Logistics', 'Engineering', 'Travel & more'];

  // Inline link to a company page (or an external URL) inside service card copy.
  $link = fn (string $label, string $slugOrUrl) => sprintf(
    '<a href="%s"%s class="text-ink-600 underline decoration-ink-200 underline-offset-4 transition-colors hover:text-brand-600 hover:decoration-brand-500/40">%s</a>',
    e(str_starts_with($slugOrUrl, 'http') ? $slugOrUrl : route('site.company', $slugOrUrl)),
    str_starts_with($slugOrUrl, 'http') ? ' target="_blank" rel="noopener"' : '',
    e($label)
  );

  $services = [
    [
      'title' => 'Automotive & Motorcycle Spare Parts',
      'icon' => '<circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="m5 17 4-8h5l5 8M8 12h8M14 6h3l2 11M5 7h5"/>',
      'body' => $link('LITUS Automobiles', 'litus-automobiles').' provides vehicle sales, while LITUS Service Center &amp; Parts (LSP) supports customers with servicing and motorcycle spare parts.',
    ],
    [
      'title' => 'Hardware, Building Materials & Paint',
      'icon' => '<path d="M3 3h13v6H3zM16 6h4v7h-9v3M9 16h4v6H9z"/>',
      'body' => $link('Favala Hardware', 'favala-hardware').' supplies building materials, finishing materials and general hardware, including silicone and sealants. '.$link('Favala Paint', 'favala-paint').' offers paints, coatings and paint application services. '.$link('Favala Supply', 'favala-supply').' serves commercial and industrial supply needs.',
    ],
    [
      'title' => 'Logistics & Project Handling',
      'icon' => '<path d="M3 12l9-3 9 3-3 7H6zM7 10V5h10v5M10 5V2h4v3M2 21q3-3 5 0 3-3 5 0 3-3 5 0 3-3 5 0"/>',
      'body' => 'Our logistics businesses provide sea and land transport, international freight, customs clearance and project handling for resorts, construction projects and large businesses, coordinating the movement of goods, materials and equipment.',
    ],
    [
      'title' => 'Engineering Services',
      'icon' => '<path d="m14 6 4 4 4-4a7 7 0 0 1-9 9l-6 6a3 3 0 0 1-4-4l6-6a7 7 0 0 1 9-9z"/>',
      'body' => $link('LM Workshop', 'https://www.lmworkshop.com/').', the engineering division of '.$link('LITUS Maldives', 'litus-maldives').', provides marine, mechanical and electrical engineering, power systems support, fabrication and industrial maintenance for resorts, vessels and commercial facilities.',
    ],
    [
      'title' => 'Currency Exchange',
      'icon' => '<path d="M3 7h17m-4-4 4 4-4 4M21 17H4m4-4-4 4 4 4"/>',
      'body' => 'Home Currency Link (HCL) provides currency exchange services in the Maldives.',
    ],
    [
      'title' => 'Travel, Hospitality, Construction & Technology',
      'icon' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18"/>',
      'body' => $link('Zaha Travels', 'zaha-travels').' provides travel planning and destination services in the Maldives and Sri Lanka. Other group businesses include '.$link('Zaha Residence & Hotels', 'zaha-residence-hotels').', '.$link('LITUS Constructions', 'litus-constructions').' and '.$link('LITUS Connect', 'litus-connect').'.',
    ],
  ];

  $purpose = [
    [
      'title' => 'Our Mission',
      'body' => 'To deliver exceptional value across diverse industries through innovation, quality, and unwavering commitment to customer satisfaction. We strive to be the partner of choice for businesses and individuals seeking excellence.',
    ],
    [
      'title' => 'Our Vision',
      'body' => 'To be the most trusted and diversified business group in the Maldives, setting industry standards and creating sustainable value for all stakeholders while contributing to national economic growth.',
    ],
  ];
@endphp

<div data-about-page class="about-page">
  {{-- Hide scroll-reveal content before first paint; app.js reveals it (falls back to visible after 3s). --}}
  <script>
    (function (root) {
      if (!root || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      root.classList.add('about-js');
      setTimeout(function () { if (!root.dataset.revealBound) root.classList.remove('about-js'); }, 3000);
    })(document.currentScript.parentElement);
  </script>

  {{-- ============================== HERO ============================== --}}
  <section data-about-hero class="about-hero relative isolate flex min-h-[540px] items-center overflow-hidden bg-ink-950 pt-36 pb-8 text-white md:min-h-[590px] md:pt-44 md:pb-10 2xl:min-h-[620px]">
    <div class="absolute inset-0 -z-10">
      <div class="about-hero-media absolute inset-0">
        @if(filled($heroImageUrl))
          <img
            src="{{ $heroImageUrl }}"
            alt="LITUS Group team gathered for a company event"
            class="h-full w-full object-cover"
            style="object-position: 50% {{ $heroPosY }}%;"
            fetchpriority="high"
            decoding="async"
          />
        @else
          <div class="ui-page-hero__glow"></div>
        @endif
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 to-ink-950/50"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-ink-950 to-transparent to-85%"></div>
    </div>

    <div class="about-hero-content ui-container">
      <span class="ui-eyebrow ui-eyebrow--light about-anim-fade about-eyebrow-grow">About LITUS Group</span>
      <h1 class="mt-5 text-[3rem] leading-[1.07] font-extrabold tracking-[-0.055em] text-white sm:text-6xl lg:text-[5.5rem]" aria-label="About LITUS Group">
        <span class="about-mask" aria-hidden="true"><span class="about-word" style="--d: 150ms">About</span></span>
        <span class="about-mask" aria-hidden="true"><span class="about-word" style="--d: 260ms">LITUS</span></span>
        <span class="about-mask" aria-hidden="true"><span class="about-word ui-accent about-shine text-lagoon-300" style="--d: 370ms">Group</span></span>
      </h1>
      <p class="about-anim-fade mt-6 mb-9 max-w-[650px] text-[1.0625rem] leading-relaxed text-ink-100/90 md:mb-12 md:text-xl md:leading-[1.65]" style="--d: 550ms">
        A diversified business group in the Maldives.<br>
        Supporting everyday life, business and industry.
      </p>

      <div class="about-hero-rule flex flex-col gap-4 pt-6 md:flex-row md:items-center md:justify-between md:gap-5">
        <span class="about-anim-fade text-sm text-ink-300" style="--d: 850ms">Specialist businesses. Shared purpose.</span>
        <ul class="flex flex-wrap gap-1.5 md:justify-end md:gap-2" aria-label="Industries">
          @foreach($heroTags as $tag)
            <li class="about-tag rounded-full border border-white/15 bg-white/5 px-2.5 py-1 text-[0.7rem] text-ink-100 transition-colors duration-300 hover:border-lagoon-300/50 hover:bg-white/10 md:px-3 md:py-1.5 md:text-xs" style="--d: {{ 900 + $loop->index * 80 }}ms">{{ $tag }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </section>

  {{-- ============================== WHO WE ARE ============================== --}}
  <section id="about" class="bg-white py-14 md:py-20 lg:pt-[84px] lg:pb-[76px]">
    <div class="ui-container grid grid-cols-1 items-start gap-6 lg:grid-cols-[1fr_1.1fr] lg:gap-[90px]">
      <div data-reveal>
        <span class="ui-eyebrow">Who we are</span>
        <h2 class="mt-5 text-[2rem] leading-[1.16] font-extrabold tracking-[-0.045em] text-ink-900 sm:text-[2.5rem] lg:text-5xl">
          A diversified business group <span class="ui-accent about-underline text-brand-600">in the Maldives.</span>
        </h2>
      </div>
      <div class="space-y-[18px] text-base leading-[1.85] md:text-[1.0625rem]">
        <p data-reveal class="text-ink-600" style="--d: 150ms">{!! nl2br(e($aboutIntro1)) !!}</p>
        @if(filled($aboutIntro2))
          <p data-reveal class="text-ink-500" style="--d: 300ms">{!! nl2br(e($aboutIntro2)) !!}</p>
        @endif
      </div>
    </div>
  </section>

  {{-- ============================== BUSINESSES & SERVICES ============================== --}}
  <section class="bg-white pb-14 md:pb-[88px]" aria-labelledby="services-title">
    <div class="ui-container">
      <div data-reveal class="mb-8 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between md:gap-6">
        <div>
          <span class="ui-eyebrow">What we do</span>
          <h2 id="services-title" class="mt-4 text-[2rem] leading-[1.16] font-extrabold tracking-[-0.045em] text-ink-900 md:text-4xl">
            Our businesses <span class="ui-accent about-underline text-brand-600">&amp; services</span>
          </h2>
        </div>
        <a href="{{ route('site.our-companies') }}" class="about-link group inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700">
          <span class="underline underline-offset-[5px]">Explore our companies</span>
          <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>

      <div class="grid grid-cols-1 gap-3.5 md:grid-cols-2 md:gap-5">
        @foreach($services as $index => $service)
          <div data-reveal style="--d: {{ ($index % 2) * 120 }}ms">
            <article class="about-card h-full rounded-[20px] border border-ink-100 bg-white p-6 md:px-8 md:pt-[30px] md:pb-8">
              <div class="mb-[18px] flex items-center justify-between md:mb-[23px]">
                <span class="about-card-icon grid h-[46px] w-[46px] place-items-center rounded-[13px] bg-brand-50 text-brand-600">
                  <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $service['icon'] !!}</svg>
                </span>
                <span class="about-card-num text-[0.8125rem] font-semibold text-ink-400 tabular-nums">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <h3 class="mb-3 text-xl leading-[1.4] font-bold tracking-[-0.025em] text-ink-900 md:text-[1.3125rem]">{{ $service['title'] }}</h3>
              <p class="text-base leading-[1.8] text-ink-500">{!! $service['body'] !!}</p>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ============================== MISSION & VISION ============================== --}}
  <section class="about-purpose-section relative isolate overflow-hidden bg-ink-900 py-12 text-white md:py-[72px]">
    <div class="about-orb about-orb--brand" aria-hidden="true"></div>
    <div class="about-orb about-orb--lagoon" aria-hidden="true"></div>

    <div class="ui-container">
      <div data-reveal class="md:flex md:items-end md:justify-between md:gap-10">
        <div>
          <span class="ui-eyebrow ui-eyebrow--light">Mission &amp; vision</span>
          <h2 class="mt-5 text-[2rem] leading-[1.16] font-extrabold tracking-[-0.045em] text-white sm:text-[2.5rem] lg:text-5xl">
            What <span class="ui-accent about-underline text-lagoon-300">drives</span> us
          </h2>
        </div>
        <p class="mt-5 max-w-[360px] text-base leading-[1.7] text-ink-300 md:mt-0">
          Our purpose and ambition, shared by every business in the LITUS family.
        </p>
      </div>

      <div class="mt-7 grid grid-cols-1 gap-7 md:mt-[38px] md:grid-cols-2 md:gap-[60px]">
        @foreach($purpose as $index => $item)
          <div data-reveal class="about-purpose grid grid-cols-[35px_1fr] gap-[18px] pt-7" style="--d: {{ $index * 180 }}ms">
            <span class="about-purpose-num pt-1.5 text-[0.8125rem] text-lagoon-300 tabular-nums">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <div>
              <h3 class="mb-3.5 text-[1.4375rem] font-bold text-white">{{ $item['title'] }}</h3>
              <p class="text-base leading-[1.85] text-ink-300">{{ $item['body'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ============================== CTA ============================== --}}
  <section class="about-cta relative isolate overflow-hidden bg-sand-100 py-12 text-center md:py-[68px]">
    <div class="ui-container">
      <div data-reveal>
        <span class="ui-eyebrow ui-eyebrow--center">Grow with us</span>
        <h2 class="mx-auto mt-5 mb-7 max-w-[750px] text-[2rem] leading-[1.16] font-extrabold tracking-[-0.045em] text-ink-900 sm:text-[2.5rem] lg:text-5xl">
          Discover the businesses<br class="hidden sm:block"> we <span class="ui-accent about-underline text-brand-600">grow together.</span>
        </h2>
      </div>
      <div data-reveal class="flex flex-wrap justify-center gap-3" style="--d: 200ms">
        <a href="{{ route('site.our-companies') }}" class="ui-btn ui-btn--primary">
          Explore Our Companies
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <a href="{{ route('site.contact') }}" class="ui-btn ui-btn--outline">Contact Us</a>
      </div>
    </div>
  </section>
</div>
@endsection
