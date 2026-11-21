@extends('layouts.site')

@section('content')
@php
  $otherOpenings = $otherOpenings ?? [];

  $careersAlpineConfig = [
    'reopenJobModal' => $errors->any(),
    'jobModalTitle' => old('position', $job['title']),
    'jobModalLocked' => (string) old('apply_title_locked', '1') === '1',
  ];

  // Plain-text description from the admin → headings, bullet lists, paragraphs + key facts (salary, deadline…).
  $parsed = \App\Support\JobDescription::parse($job['description'] ?? null);
  $sections = \App\Support\JobDescription::sections($parsed['blocks']);
  $descFacts = $parsed['facts'];

  $salary = $descFacts['Salary'] ?? null;
  $deadline = $descFacts['Deadline'] ?? null;
  $location = filled($job['location'] ?? null) ? $job['location'] : ($descFacts['Location'] ?? null);

  $summary = array_filter([
    'Company' => $job['company'] ?? null,
    'Department' => $job['department'] ?? null,
    'Location' => $location,
    'Employment type' => $job['type'] ?? null,
    'Salary' => $salary,
    'Experience' => $descFacts['Experience'] ?? null,
    'Working hours' => $descFacts['Working hours'] ?? null,
    'Start date' => $descFacts['Start date'] ?? null,
    'Contract' => $descFacts['Contract'] ?? null,
    'Reports to' => $descFacts['Reports to'] ?? null,
    'Deadline' => $deadline,
  ], 'filled');

  $icons = [
    'company' => '<path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/>',
    'location' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
    'type' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    'deadline' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
    'salary' => '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
    'check' => '<path d="M20 6 9 17l-5-5"/>',
    'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
  ];
  $svg = fn (string $key, int $size = 16, string $class = '') => '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="'.$class.'" aria-hidden="true">'.$icons[$key].'</svg>';

  $heroMeta = array_filter([
    ['icon' => 'company', 'label' => 'Company', 'value' => $job['company'] ?? null],
    ['icon' => 'location', 'label' => 'Location', 'value' => $location],
    ['icon' => 'type', 'label' => 'Job type', 'value' => $job['type'] ?? null],
    ['icon' => 'deadline', 'label' => 'Apply by', 'value' => $deadline],
  ], fn ($m) => filled($m['value']));

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $sectionNo = 0;
@endphp
<div
  data-careers-page
  x-data="careersPage({{ \Illuminate\Support\Js::from($careersAlpineConfig) }})"
>
  {{-- ============================== HERO (same shell as company / blog detail pages) ============================== --}}
  <section class="relative isolate overflow-hidden bg-ink-950 text-white">
    <div class="absolute inset-0 -z-10">
      <div class="ui-page-hero__glow"></div>
      <div class="ui-grid-texture--light absolute inset-0 [mask-image:radial-gradient(ellipse_at_top_right,black,transparent_75%)]"></div>
      <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-ink-950 to-transparent"></div>
    </div>

    <div class="ui-container pt-32 pb-14 md:pt-40 md:pb-20">
      <div class="site-blogs-hero">
        <a
          href="{{ route('site.careers') }}#openings"
          class="group inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-white/70 transition-colors hover:text-white"
        >
          <span class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-md transition-all group-hover:-translate-x-0.5 group-hover:bg-white/20">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
          </span>
          All open roles
        </a>

        <div class="mt-8 flex flex-wrap items-center gap-3 md:mt-10">
          <span class="ui-eyebrow ui-eyebrow--light">We're hiring</span>
          @if(!empty($job['department']))
            <span class="inline-flex items-center rounded-full bg-lagoon-400 px-3 py-1 text-[0.7rem] font-bold uppercase tracking-wider text-ink-950">{{ $job['department'] }}</span>
          @endif
        </div>

        <div class="mt-6 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
          <div class="min-w-0 max-w-4xl">
            <h1 class="text-[2.4rem] leading-[1.05] font-extrabold tracking-[-0.035em] text-white [overflow-wrap:anywhere] sm:text-5xl md:text-6xl lg:text-7xl">
              {{ $job['title'] }}
            </h1>
            @if(!empty($job['company']))
              <p class="mt-4 font-serif text-2xl leading-snug text-lagoon-300 italic sm:text-3xl md:mt-5">at {{ $job['company'] }}</p>
            @endif
          </div>

          <div class="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center lg:flex-col lg:items-end">
            <button type="button" class="ui-btn ui-btn--light" @click="openApplyModal(@js($job['title']))">
              Apply now
              <span class="-mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-white">{!! $arrow !!}</span>
            </button>
            @if($salary)
              <span class="inline-flex items-center gap-2 text-sm font-semibold text-white/80">
                {!! $svg('salary', 16, 'text-lagoon-300') !!}
                {{ $salary }}
              </span>
            @endif
          </div>
        </div>

        @if(count($heroMeta))
          <dl class="mt-10 grid grid-cols-2 gap-x-6 gap-y-5 border-t border-white/10 pt-8 md:mt-14 lg:grid-cols-4">
            @foreach($heroMeta as $meta)
              <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-lagoon-300 ring-1 ring-white/15">
                  {!! $svg($meta['icon']) !!}
                </span>
                <div class="min-w-0">
                  <dt class="text-[0.7rem] font-bold uppercase tracking-[0.18em] text-white/45">{{ $meta['label'] }}</dt>
                  <dd class="truncate text-sm font-semibold text-white sm:text-base">{{ $meta['value'] }}</dd>
                </div>
              </div>
            @endforeach
          </dl>
        @endif
      </div>
    </div>
  </section>

  {{-- ============================== DETAILS ============================== --}}
  <section class="ui-section relative bg-white">
    <div class="ui-grid-texture pointer-events-none absolute inset-0 [mask-image:radial-gradient(ellipse_at_top_left,black,transparent_55%)] opacity-60" aria-hidden="true"></div>
    <div class="ui-container relative">
      @if (session('job_apply_success'))
        <div class="mb-10 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-900 sm:text-base" role="status">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>{{ session('job_apply_success') }}</span>
        </div>
      @endif

      <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
        {{-- Role content --}}
        <article class="min-w-0 lg:col-span-8">
          <span class="ui-eyebrow">About the role</span>

          @if(count($sections))
            <div class="mt-8 space-y-12 md:space-y-14">
              @foreach($sections as $section)
                @php
                  $isApply = $section['heading'] && preg_match('/\bapply\b|application/i', $section['heading']);
                @endphp

                @if($isApply)
                  {{-- "How to apply" gets its own callout --}}
                  <div class="relative overflow-hidden rounded-3xl border border-brand-100 bg-brand-50/60 p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                      <span class="ui-icon-tile bg-white">{!! $svg('mail', 22) !!}</span>
                      <div class="min-w-0">
                        <h2 class="text-xl font-extrabold tracking-[-0.02em] text-ink-900 sm:text-2xl">{{ $section['heading'] }}</h2>
                        @foreach($section['blocks'] as $block)
                          @if($block['type'] === 'list')
                            <ul class="mt-4 space-y-2 text-base leading-relaxed text-ink-700">
                              @foreach($block['items'] as $item)
                                <li class="flex gap-3"><span class="mt-2.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span><span>{!! \App\Support\JobDescription::linkify($item) !!}</span></li>
                              @endforeach
                            </ul>
                          @else
                            <p class="mt-3 text-base leading-relaxed text-ink-700 [overflow-wrap:anywhere] sm:text-lg">{!! collect($block['lines'])->map(fn ($l) => \App\Support\JobDescription::linkify($l))->implode('<br>') !!}</p>
                          @endif
                        @endforeach
                        <button type="button" class="ui-btn ui-btn--primary ui-btn--sm mt-6" @click="openApplyModal(@js($job['title']))">
                          Or apply online
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                @else
                  <div>
                    @if($section['heading'])
                      @php $sectionNo++; @endphp
                      <div class="mb-6 flex items-baseline gap-4 border-b border-ink-100 pb-4">
                        <span class="font-serif text-2xl leading-none text-brand-600 italic tabular-nums">{{ sprintf('%02d', $sectionNo) }}</span>
                        <h2 class="text-2xl font-extrabold tracking-[-0.025em] text-ink-900 sm:text-[1.75rem]">{{ $section['heading'] }}</h2>
                      </div>
                    @endif

                    <div class="space-y-6">
                      @foreach($section['blocks'] as $block)
                        @if($block['type'] === 'list')
                          <ul class="space-y-4">
                            @foreach($block['items'] as $item)
                              <li class="flex gap-4">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 ring-1 ring-brand-100">
                                  {!! $svg('check', 14) !!}
                                </span>
                                <span class="min-w-0 text-base leading-relaxed text-ink-700 [overflow-wrap:anywhere] sm:text-[1.0625rem]">{!! \App\Support\JobDescription::linkify($item) !!}</span>
                              </li>
                            @endforeach
                          </ul>
                        @else
                          <p class="text-base leading-relaxed text-ink-600 [overflow-wrap:anywhere] sm:text-lg">{!! collect($block['lines'])->map(fn ($l) => \App\Support\JobDescription::linkify($l))->implode('<br>') !!}</p>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          @else
            <p class="mt-6 text-lg leading-relaxed text-ink-600">
              Interested in this role? Send us your CV and our HR team will get in touch with the full details.
            </p>
          @endif

          {{-- Closing CTA --}}
          <div class="ui-bg-ink mt-14 rounded-3xl p-7 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-10 md:mt-16">
            <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_right,black,transparent_70%)]" aria-hidden="true"></div>
            <div>
              <p class="text-[0.7rem] font-bold uppercase tracking-[0.2em] text-lagoon-300">Ready to join us?</p>
              <p class="mt-2 text-2xl font-extrabold tracking-[-0.02em] text-white sm:text-3xl">
                Start your <span class="ui-accent text-lagoon-300">journey</span> with LITUS
              </p>
            </div>
            <button type="button" class="ui-btn ui-btn--light mt-6 w-full shrink-0 sm:mt-0 sm:w-auto" @click="openApplyModal(@js($job['title']))">
              Apply for this role
              {!! $arrow !!}
            </button>
          </div>
        </article>

        {{-- Summary --}}
        <aside class="lg:col-span-4">
          <div class="ui-card overflow-hidden lg:sticky lg:top-28">
            <div class="border-b border-ink-100 bg-sand-50 px-6 py-5 sm:px-8">
              <p class="text-[0.7rem] font-bold uppercase tracking-[0.2em] text-brand-600">At a glance</p>
              <h2 class="mt-1 text-lg font-bold tracking-tight text-ink-900">Job summary</h2>
            </div>
            <dl class="divide-y divide-ink-100 px-6 sm:px-8">
              @foreach($summary as $label => $value)
                <div class="flex items-start justify-between gap-4 py-3.5">
                  <dt class="shrink-0 text-sm text-ink-500">{{ $label }}</dt>
                  <dd class="text-right text-sm font-semibold {{ $label === 'Deadline' ? 'text-brand-600' : 'text-ink-900' }}">{{ $value }}</dd>
                </div>
              @endforeach
            </dl>
            <div class="px-6 pt-2 pb-6 sm:px-8 sm:pb-8">
              <button type="button" class="ui-btn ui-btn--primary w-full" @click="openApplyModal(@js($job['title']))">
                Apply for this role
                {!! $arrow !!}
              </button>
              <p class="mt-3 text-center text-xs text-ink-400">PDF, DOC or DOCX · up to 10 MB</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

  {{-- ============================== OTHER OPENINGS ============================== --}}
  @if(count($otherOpenings) > 0)
    <section class="ui-section bg-sand-100">
      <div class="ui-container">
        <div class="mb-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between md:mb-12">
          <div>
            <span class="ui-eyebrow">Keep exploring</span>
            <h2 class="ui-h2 mt-5">Other <span class="ui-accent text-brand-600">openings</span></h2>
          </div>
          <a href="{{ route('site.careers') }}#openings" class="ui-btn ui-btn--outline ui-btn--sm self-start sm:self-auto">
            View all roles
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-3">
          @foreach($otherOpenings as $other)
            <a href="{{ $other['url'] }}" class="ui-card ui-card--hover group flex h-full flex-col p-6 sm:p-7">
              <span class="absolute top-0 left-0 h-1 w-0 rounded-tl-3xl bg-gradient-to-r from-brand-500 to-lagoon-400 transition-all duration-700 ease-out-expo group-hover:w-full" aria-hidden="true"></span>
              <div class="flex items-start justify-between gap-4">
                @if(!empty($other['department']))
                  <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100">{{ $other['department'] }}</span>
                @else
                  <span></span>
                @endif
                <span class="ui-icon-btn h-10 w-10 shrink-0 border border-ink-200 bg-white text-ink-700 group-hover:border-ink-900 group-hover:bg-ink-900 group-hover:text-white" aria-hidden="true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                </span>
              </div>
              <h3 class="mt-6 text-xl font-bold leading-snug tracking-tight text-ink-900 transition-colors group-hover:text-brand-600">{{ $other['title'] }}</h3>
              <div class="mt-auto flex flex-wrap gap-2 pt-5">
                @if(!empty($other['company']))
                  <span class="ui-chip max-w-full bg-sand-50 py-1.5">{!! $svg('company', 14, 'shrink-0 text-ink-400') !!}<span class="truncate">{{ $other['company'] }}</span></span>
                @endif
                @if(!empty($other['location']))
                  <span class="ui-chip max-w-full bg-sand-50 py-1.5">{!! $svg('location', 14, 'shrink-0 text-ink-400') !!}<span class="truncate">{{ $other['location'] }}</span></span>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <x-job-apply-modal />
</div>
@endsection
