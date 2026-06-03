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
@endphp
{{-- Active rows from job_openings (SiteData::careerOpenings); no static fallbacks --}}
<div>
  <section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-[640px]">
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl ?? null))
        <img
          src="{{ $heroImageUrl }}"
          alt="Careers hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
      @else
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900"></div>
      @endif
      <div class="absolute inset-0 bg-gradient-to-b from-blue-950/45 via-blue-900/30 to-blue-950/15 md:bg-gradient-to-r md:from-blue-900/90 md:via-blue-800/80 md:to-transparent"></div>
    </div>
    <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-20 md:py-24 lg:px-8">
      <div class="site-blogs-hero mx-auto max-w-3xl">
        <h1 class="mb-4 text-3xl font-bold text-white max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-6xl md:[text-shadow:none]">Careers</h1>
        <p class="mx-auto text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:text-lg md:max-w-3xl md:text-2xl md:[text-shadow:none]">
          Explore opportunities to grow your career with LITUS Group
        </p>
      </div>
    </div>
  </section>

  <section
    id="careers"
    class="bg-white py-14 md:py-24"
    data-careers-page
    x-data="careersPage({{ \Illuminate\Support\Js::from($careersAlpineConfig ?? []) }})"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      @if (session('job_apply_success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 sm:mb-8 sm:px-5 sm:py-4 sm:text-base" role="status">
          {{ session('job_apply_success') }}
        </div>
      @endif
      <div
        class="site-careers-header mb-10 translate-y-[50px] text-center opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="careersInView = true"
        :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">
          Join Our Team
        </h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Build your career with LITUS Group and be part of our diverse,
          dynamic team across multiple industries
        </p>
      </div>

      {{-- Why Join Us --}}
      <div
        class="site-careers-why mb-8 translate-y-[30px] rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto sm:mb-10 sm:p-8 md:mb-12 md:p-12 md:will-change-[opacity,transform]"
        style="transition-delay: 200ms"
        :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
      >
        <h3 class="mb-4 text-center text-xl font-bold text-gray-900 sm:mb-6 sm:text-2xl md:text-left">
          Why Join LITUS Group?
        </h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 md:gap-6">
          <div class="text-center md:text-left">
            <div class="mb-1.5 text-sm font-semibold text-blue-600 sm:mb-2 sm:text-base">Career Growth</div>
            <p class="text-sm leading-relaxed text-gray-600 sm:text-base">
              Opportunities to grow across our diverse portfolio of companies
            </p>
          </div>
          <div class="text-center md:text-left">
            <div class="mb-1.5 text-sm font-semibold text-blue-600 sm:mb-2 sm:text-base">Competitive Benefits</div>
            <p class="text-sm leading-relaxed text-gray-600 sm:text-base">
              Comprehensive benefits package and competitive compensation
            </p>
          </div>
          <div class="text-center sm:col-span-2 md:col-span-1 md:text-left">
            <div class="mb-1.5 text-sm font-semibold text-blue-600 sm:mb-2 sm:text-base">Innovation Culture</div>
            <p class="text-sm leading-relaxed text-gray-600 sm:text-base">
              Work with cutting-edge technology and innovative solutions
            </p>
          </div>
        </div>
      </div>

      {{-- Job Openings (active records from job_openings only) --}}
      <div class="space-y-3 sm:space-y-4">
        <h3 class="mb-4 text-center text-xl font-bold text-gray-900 sm:mb-6 sm:text-left sm:text-2xl">
          Current Openings
        </h3>
        @forelse($jobOpenings as $index => $job)
          <div
            class="site-careers-job group translate-y-[30px] rounded-xl border border-gray-200 bg-white p-4 opacity-0 transition-all duration-500 ease-out max-md:will-change-auto sm:p-6 md:-translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform] md:duration-500 @if(!empty($job['description'])) cursor-pointer @endif md:hover:border-blue-300 md:hover:shadow-lg"
            style="transition-delay: {{ 300 + $index * 100 }}ms"
            :class="careersInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
            @if(!empty($job['description']))
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
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div class="min-w-0 flex-1">
                <h4 class="mb-2 text-base font-bold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 sm:text-lg md:text-xl">
                  {{ $job['title'] }}
                </h4>
                <div class="flex flex-col gap-2 text-sm text-gray-600 sm:flex-row sm:flex-wrap sm:gap-4">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                      <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                      <rect width="20" height="14" x="2" y="6" rx="2" />
                    </svg>
                    <span class="truncate">{{ $job['company'] }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                      <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>{{ $job['location'] }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                      <circle cx="12" cy="12" r="10" />
                      <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>{{ $job['type'] }}</span>
                  </div>
                </div>
              </div>
              <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center md:w-auto md:gap-4">
                <span class="inline-flex justify-center rounded-full bg-blue-50 px-3 py-1.5 text-center text-xs font-medium text-blue-600 sm:px-4 sm:py-2 sm:text-sm">
                  {{ $job['department'] }}
                </span>
                <button
                  type="button"
                  class="flex w-full items-center justify-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-blue-700 sm:w-auto sm:px-6 md:group-hover:gap-3"
                  @click.stop="openApplyModal('{{ addslashes($job['title']) }}')"
                >
                  Apply
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>

            @if(!empty($job['description']))
              <div
                class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-700 sm:mt-5 sm:pt-5 sm:text-base"
                x-show="activeJobIndex === {{ $index }}"
                x-transition.opacity.duration.150ms
                x-cloak
              >
                <p class="whitespace-pre-line leading-relaxed">{{ $job['description'] }}</p>
              </div>
            @endif
          </div>
        @empty
          <p class="py-4 text-center text-sm text-gray-600 sm:text-left sm:text-base">
            There are no open positions at the moment. Please check back later.
          </p>
        @endforelse
      </div>

      <x-job-apply-modal />

      {{-- CTA --}}
      <div
        class="site-careers-cta mt-8 translate-y-[30px] rounded-2xl bg-gray-50 p-5 text-center opacity-0 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto sm:mt-12 sm:p-8 md:will-change-[opacity,transform]"
        style="transition-delay: 800ms"
        :class="careersInView ? '!translate-y-0 !opacity-100' : ''"
      >
        <p class="mb-4 text-sm text-gray-600 sm:text-base">
          Don't see a position that matches your skills?
        </p>
        <button
          type="button"
          class="inline-flex w-full max-w-sm items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-lg transition-all hover:bg-blue-700 hover:shadow-xl sm:w-auto sm:px-8"
          @click="openApplyModal('', false)"
        >
          Send Us Your Resume
        </button>
      </div>
    </div>
  </section>
</div>
@endsection
