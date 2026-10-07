@extends('layouts.site')

@section('content')
@php
  $otherOpenings = $otherOpenings ?? [];

  $careersAlpineConfig = [
    'reopenJobModal' => $errors->any(),
    'jobModalTitle' => old('position', $job['title']),
    'jobModalLocked' => (string) old('apply_title_locked', '1') === '1',
  ];

  $facts = array_filter([
    'Company' => $job['company'] ?? null,
    'Department' => $job['department'] ?? null,
    'Location' => $job['location'] ?? null,
    'Employment type' => $job['type'] ?? null,
  ]);

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
@endphp
<div
  data-careers-page
  x-data="careersPage({{ \Illuminate\Support\Js::from($careersAlpineConfig) }})"
>
  <section class="bg-sand-100 pt-28 pb-16 md:pt-36 md:pb-24">
    <div class="ui-container">
      {{-- ============================== HEADER ============================== --}}
      <a href="{{ route('site.careers') }}#openings" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-500 transition-colors hover:text-brand-600">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        All open roles
      </a>

      <div class="mt-6 flex flex-col gap-6 border-b border-ink-100 pb-8 md:mt-8 md:pb-10 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0">
          @if(!empty($job['department']))
            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100">{{ $job['department'] }}</span>
          @endif
          <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-ink-900 sm:text-4xl">{{ $job['title'] }}</h1>
          <div class="mt-4 flex flex-wrap gap-2">
            @if(!empty($job['company']))
              <span class="inline-flex items-center gap-2 rounded-full border border-ink-100 bg-white px-3.5 py-1.5 text-sm font-medium text-ink-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>
                {{ $job['company'] }}
              </span>
            @endif
            @if(!empty($job['location']))
              <span class="inline-flex items-center gap-2 rounded-full border border-ink-100 bg-white px-3.5 py-1.5 text-sm font-medium text-ink-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $job['location'] }}
              </span>
            @endif
            @if(!empty($job['type']))
              <span class="inline-flex items-center gap-2 rounded-full border border-ink-100 bg-white px-3.5 py-1.5 text-sm font-medium text-ink-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-ink-400" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ $job['type'] }}
              </span>
            @endif
          </div>
        </div>
        <button type="button" class="ui-btn ui-btn--primary shrink-0 self-start lg:self-auto" @click="openApplyModal(@js($job['title']))">
          Apply now
          {!! $arrow !!}
        </button>
      </div>

      {{-- ============================== DETAILS ============================== --}}
      <div class="mt-8 md:mt-10">
      @if (session('job_apply_success'))
        <div class="mb-10 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-900 sm:text-base" role="status">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          <span>{{ session('job_apply_success') }}</span>
        </div>
      @endif

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
        <article class="ui-card p-6 sm:p-10 lg:col-span-8">
          <h2 class="text-[0.7rem] font-bold uppercase tracking-[0.2em] text-ink-400">About the role</h2>
          @if(!empty($job['description']))
            <div class="mt-4 whitespace-pre-line text-base leading-relaxed text-ink-700 sm:text-lg">{{ $job['description'] }}</div>
          @else
            <p class="mt-4 text-base leading-relaxed text-ink-600 sm:text-lg">
              Interested in this role? Send us your CV and our HR team will get in touch with the full details.
            </p>
          @endif
        </article>

        <aside class="lg:col-span-4">
          <div class="ui-card p-6 sm:p-8 lg:sticky lg:top-28">
            <h2 class="text-lg font-bold tracking-tight text-ink-900">Job summary</h2>
            <dl class="mt-5 divide-y divide-ink-100">
              @foreach($facts as $label => $value)
                <div class="flex items-start justify-between gap-4 py-3">
                  <dt class="text-sm text-ink-500">{{ $label }}</dt>
                  <dd class="text-right text-sm font-semibold text-ink-900">{{ $value }}</dd>
                </div>
              @endforeach
            </dl>
            <button type="button" class="ui-btn ui-btn--primary mt-6 w-full" @click="openApplyModal(@js($job['title']))">
              Apply for this role
              {!! $arrow !!}
            </button>
            <p class="mt-3 text-center text-xs text-ink-400">PDF, DOC or DOCX · up to 10 MB</p>
          </div>
        </aside>
      </div>

      {{-- ============================== OTHER OPENINGS ============================== --}}
      @if(count($otherOpenings) > 0)
        <div class="mt-14 md:mt-16">
          <div class="mb-6 flex items-end justify-between gap-4">
            <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Other openings</h2>
            <a href="{{ route('site.careers') }}#openings" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
              View all roles {!! $arrow !!}
            </a>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach($otherOpenings as $other)
              <a href="{{ $other['url'] }}" class="ui-card ui-card--hover group flex h-full flex-col p-6 sm:p-7">
                @if(!empty($other['department']))
                  <span class="self-start rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100">{{ $other['department'] }}</span>
                @endif
                <h3 class="mt-4 text-lg font-bold leading-snug tracking-tight text-ink-900 transition-colors group-hover:text-brand-600">{{ $other['title'] }}</h3>
                <p class="mt-2 text-sm text-ink-500">{{ implode(' · ', array_filter([$other['company'] ?? null, $other['location'] ?? null])) }}</p>
                <span class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-brand-600">View role {!! $arrow !!}</span>
              </a>
            @endforeach
          </div>
        </div>
      @endif
      </div>
    </div>
  </section>

  <x-job-apply-modal />
</div>
@endsection
