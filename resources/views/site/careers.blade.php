@extends('layouts.site')

@section('content')
@php
  $jobOpenings = $jobOpenings ?? [];

  $careersAlpineConfig = [
    'reopenJobModal' => $errors->any(),
    'jobModalTitle' => old('position', ''),
    'jobModalLocked' => (string) old('apply_title_locked', '1') === '1',
  ];

  $heroImagePath = \App\Models\SiteSetting::getValue('careers.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('careers.hero.position_y', 50);

  $openingsCount = count($jobOpenings);

  $whyJoin = [
    [
      'title' => 'Career Growth',
      'text' => 'Opportunities to grow across our diverse portfolio of companies',
      'icon' => '<path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/>',
    ],
    [
      'title' => 'Competitive Benefits',
      'text' => 'Comprehensive benefits package and competitive compensation',
      'icon' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
    ],
    [
      'title' => 'Innovation Culture',
      'text' => 'Work with cutting-edge technology and innovative solutions',
      'icon' => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
    ],
  ];

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
@endphp
{{-- Active rows from job_openings (SiteData::careerOpenings); no static fallbacks --}}
<div>
  {{-- ============================== HERO ============================== --}}
  <section class="ui-page-hero md:pb-32">
    @if(filled($heroImageUrl ?? null))
      <div class="absolute inset-0 -z-20">
        <img
          src="{{ $heroImageUrl }}"
          alt="Careers hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
        <div class="absolute inset-0 bg-ink-950/35"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/20"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
      </div>
    @endif
    <div class="ui-page-hero__glow"></div>
    <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:linear-gradient(to_bottom,black,transparent_85%)]"></div>

    <div class="ui-container">
      <div class="site-blogs-hero max-w-4xl">
        <span class="ui-eyebrow ui-eyebrow--light">Careers</span>
        <h1 class="ui-h1 mt-6 text-white">
          Grow your <span class="ui-accent text-lagoon-300">career</span> with LITUS
        </h1>
        <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg md:mt-8 md:text-xl">
          Explore opportunities to grow your career with LITUS Group
        </p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center md:mt-10">
          <a href="#openings" class="ui-btn ui-btn--light">
            View open roles
            @if($openingsCount > 0)
              <span class="rounded-full bg-ink-900 px-2 py-0.5 text-xs font-bold text-white tabular-nums">{{ sprintf('%02d', $openingsCount) }}</span>
            @endif
            {!! $arrow !!}
          </a>
        </div>
      </div>
    </div>
  </section>

  <section
    id="careers"
    data-careers-page
    x-data="careersPage({{ \Illuminate\Support\Js::from($careersAlpineConfig ?? []) }})"
  >
    {{-- ============================== WHY JOIN ============================== --}}
    <div class="ui-section bg-white">
      <div class="ui-container">
        @if (session('job_apply_success'))
          <div class="mb-10 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-900 sm:text-base" role="status">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <span>{{ session('job_apply_success') }}</span>
          </div>
        @endif

        <div
          class="site-careers-header mb-12 grid translate-y-[50px] grid-cols-1 items-end gap-6 opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform] lg:grid-cols-12"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="careersInView = true"
          :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
        >
          <div class="lg:col-span-7">
            <span class="ui-eyebrow">Why LITUS Group</span>
            <h2 class="ui-h2 mt-5">
              Join our <span class="ui-accent text-brand-600">team</span>
            </h2>
          </div>
          <div class="lg:col-span-5">
            <p class="ui-lead">
              Build your career with LITUS Group and be part of our diverse,
              dynamic team across multiple industries
            </p>
          </div>
        </div>

        <div
          class="site-careers-why grid translate-y-[30px] grid-cols-1 gap-4 opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto sm:gap-5 md:grid-cols-3 md:will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
        >
          @foreach($whyJoin as $i => $item)
            <div class="ui-card ui-card--hover group flex h-full flex-col p-7 sm:p-8">
              <div class="flex items-center justify-between">
                <div class="ui-icon-tile group-hover:bg-brand-600 group-hover:text-white group-hover:ring-brand-600">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                </div>
                <span class="text-sm font-bold text-ink-300 tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
              </div>
              <h3 class="mt-6 text-lg sm:mt-8 font-bold tracking-tight text-ink-900 sm:text-xl">{{ $item['title'] }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-ink-500 sm:text-base">{{ $item['text'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ============================== OPENINGS ============================== --}}
    <div id="openings" class="ui-section scroll-mt-20 bg-sand-100">
      <div class="ui-container">
        <div class="mb-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between md:mb-12">
          <div>
            <span class="ui-eyebrow">Open positions</span>
            <h2 class="ui-h2 mt-5">
              Current <span class="ui-accent text-brand-600">openings</span>
            </h2>
          </div>
          @if($openingsCount > 0)
            <span class="inline-flex items-center gap-2 self-start rounded-full border border-ink-100 bg-white px-4 py-2 text-sm font-semibold text-ink-700 sm:self-auto">
              <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-lagoon-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-lagoon-500"></span>
              </span>
              <span class="tabular-nums">{{ $openingsCount }}</span> {{ $openingsCount === 1 ? 'role' : 'roles' }} open
            </span>
          @endif
        </div>

        <div class="space-y-3 sm:space-y-4">
          @forelse($jobOpenings as $index => $job)
            @php $hasDescription = !empty($job['description']); @endphp
            <div
              class="site-careers-job translate-y-[30px] opacity-0 transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto md:-translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform]"
              style="transition-delay: {{ 300 + $index * 100 }}ms"
              :class="careersInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
            >
              <div
                class="group rounded-3xl border bg-white shadow-[0_1px_2px_rgba(6,22,52,0.04),0_8px_24px_-12px_rgba(6,22,52,0.08)] transition-all duration-300 ease-out outline-none focus-visible:ring-4 focus-visible:ring-brand-500/20 @if($hasDescription) cursor-pointer md:hover:border-brand-200 md:hover:shadow-[0_2px_4px_rgba(6,22,52,0.04),0_24px_48px_-20px_rgba(6,22,52,0.2)] @endif"
                :class="activeJobIndex === {{ $index }} ? 'border-brand-200 shadow-[0_2px_4px_rgba(6,22,52,0.04),0_24px_48px_-20px_rgba(6,22,52,0.2)]' : 'border-ink-100'"
                @if($hasDescription)
                  role="button"
                  tabindex="0"
                  :aria-expanded="activeJobIndex === {{ $index }} ? 'true' : 'false'"
                  @click="toggleJob({{ $index }})"
                  @keydown.enter.prevent="toggleJob({{ $index }})"
                  @keydown.space.prevent="toggleJob({{ $index }})"
                @else
                  role="article"
                @endif
              >
                <div class="flex flex-col gap-5 p-5 sm:p-7 lg:flex-row lg:items-center lg:gap-8">
                  <span class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sand-100 text-sm font-bold text-ink-500 tabular-nums transition-colors duration-300 group-hover:bg-brand-50 group-hover:text-brand-600 lg:flex">
                    {{ sprintf('%02d', $index + 1) }}
                  </span>

                  <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                      <h3 class="text-lg font-bold leading-snug tracking-tight text-ink-900 transition-colors group-hover:text-brand-600 sm:text-xl">
                        {{ $job['title'] }}
                      </h3>
                      @if(!empty($job['department']))
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100">
                          {{ $job['department'] }}
                        </span>
                      @endif
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                      @if(!empty($job['company']))
                        <span class="ui-chip max-w-full bg-sand-50 py-1.5">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true">
                            <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            <rect width="20" height="14" x="2" y="6" rx="2" />
                          </svg>
                          <span class="truncate">{{ $job['company'] }}</span>
                        </span>
                      @endif
                      @if(!empty($job['location']))
                        <span class="ui-chip bg-sand-50 py-1.5">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                          </svg>
                          <span>{{ $job['location'] }}</span>
                        </span>
                      @endif
                      @if(!empty($job['type']))
                        <span class="ui-chip bg-sand-50 py-1.5">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                          </svg>
                          <span>{{ $job['type'] }}</span>
                        </span>
                      @endif
                    </div>
                  </div>

                  <div class="flex items-center gap-3">
                    <button
                      type="button"
                      class="ui-btn ui-btn--primary ui-btn--sm flex-1 lg:flex-none"
                      @click.stop="openApplyModal('{{ addslashes($job['title']) }}')"
                    >
                      Apply now
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m12 5 7 7-7 7" />
                      </svg>
                    </button>
                    @if($hasDescription)
                      <span
                        class="ui-icon-btn border border-ink-200 text-ink-700 group-hover:border-ink-900"
                        :class="activeJobIndex === {{ $index }} ? 'rotate-180 !border-ink-900 bg-ink-900 text-white' : 'bg-white'"
                        aria-hidden="true"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                      </span>
                    @else
                      <span class="hidden h-11 w-11 shrink-0 lg:block" aria-hidden="true"></span>
                    @endif
                  </div>
                </div>

                @if($hasDescription)
                  <div
                    x-show="activeJobIndex === {{ $index }}"
                    x-collapse.duration.400ms
                    x-cloak
                  >
                    <div class="mx-5 border-t border-ink-100 pt-5 pb-6 sm:mx-7 sm:pb-7 lg:ml-[6.75rem]">
                      <p class="mb-3 text-[0.7rem] font-bold uppercase tracking-[0.2em] text-ink-400">About the role</p>
                      <p class="max-w-3xl whitespace-pre-line text-sm leading-relaxed text-ink-600 sm:text-base">{{ $job['description'] }}</p>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="ui-card flex flex-col items-center px-6 py-14 text-center">
              <div class="ui-icon-tile">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>
              </div>
              <p class="mt-5 text-base font-medium text-ink-600">
                There are no open positions at the moment. Please check back later.
              </p>
            </div>
          @endforelse
        </div>

        <x-job-apply-modal />

        {{-- ============================== CTA ============================== --}}
        <div
          class="site-careers-cta mt-14 translate-y-[30px] opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mt-20 md:will-change-[opacity,transform]"
          style="transition-delay: 800ms"
          :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
        >
          <div class="ui-bg-ink relative rounded-[2rem] px-6 py-12 sm:px-12 md:py-16 lg:px-16">
            <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]"></div>
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
              <div class="lg:col-span-8">
                <span class="ui-eyebrow ui-eyebrow--light">Open application</span>
                <h2 class="ui-h2 mt-5 !text-white">
                  Don't see a position that <span class="ui-accent text-lagoon-300">matches</span> your skills?
                </h2>
              </div>
              <div class="lg:col-span-4 lg:flex lg:justify-end">
                <button
                  type="button"
                  class="ui-btn ui-btn--light w-full sm:w-auto"
                  @click="openApplyModal('', false)"
                >
                  Send Us Your Resume
                  {!! $arrow !!}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
