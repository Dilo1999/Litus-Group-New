@extends('layouts.site')

@section('content')
@php
  $jobOpenings = $jobOpenings ?? \App\Support\SiteData::careerOpenings();
@endphp

{{-- Matches src/app/pages/CareersPage.tsx + src/app/components/Careers.tsx --}}
<div class="pt-20">
  <section id="careers" class="py-24 bg-white" data-careers-page x-data="careersPage()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-careers-header text-center mb-16 opacity-0 translate-y-[50px] transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="careersInView = true"
        :class="careersInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
          Join Our Team
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Build your career with LITUS Group and be part of our diverse,
          dynamic team across multiple industries
        </p>
      </div>

      {{-- Why Join Us --}}
      <div
        class="site-careers-why bg-gradient-to-br from-blue-50 to-white p-8 md:p-12 rounded-2xl mb-12 border border-blue-100 opacity-0 translate-y-[30px] transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
        style="transition-delay: 200ms"
        :class="careersInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <h3 class="text-2xl font-bold text-gray-900 mb-6">
          Why Join LITUS Group?
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div class="text-blue-600 font-semibold mb-2">Career Growth</div>
            <p class="text-gray-600">
              Opportunities to grow across our diverse portfolio of companies
            </p>
          </div>
          <div>
            <div class="text-blue-600 font-semibold mb-2">Competitive Benefits</div>
            <p class="text-gray-600">
              Comprehensive benefits package and competitive compensation
            </p>
          </div>
          <div>
            <div class="text-blue-600 font-semibold mb-2">Innovation Culture</div>
            <p class="text-gray-600">
              Work with cutting-edge technology and innovative solutions
            </p>
          </div>
        </div>
      </div>

      {{-- Job Openings --}}
      <div class="space-y-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">
          Current Openings
        </h3>
        @foreach($jobOpenings as $index => $job)
          <div
            class="site-careers-job bg-white border border-gray-200 p-6 rounded-xl hover:border-blue-300 hover:shadow-lg transition-all group cursor-pointer opacity-0 -translate-x-[50px] duration-500 ease-out will-change-[opacity,transform]"
            style="transition-delay: {{ 300 + $index * 100 }}ms"
            :class="careersInView ? '!opacity-100 !translate-x-0' : ''"
          >
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex-1">
                <h4 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                  {{ $job['title'] }}
                </h4>
                <div class="flex flex-wrap gap-4 text-gray-600">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                      <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                      <rect width="20" height="14" x="2" y="6" rx="2" />
                    </svg>
                    <span>{{ $job['company'] }}</span>
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
              <div class="flex items-center gap-4 flex-wrap">
                <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-sm font-medium">
                  {{ $job['department'] }}
                </span>
                <a
                  href="{{ route('site.contact') }}"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-semibold transition-all flex items-center gap-2 group-hover:gap-3"
                >
                  Apply
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- CTA --}}
      <div
        class="site-careers-cta text-center mt-12 p-8 bg-gray-50 rounded-2xl opacity-0 translate-y-[30px] transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
        style="transition-delay: 800ms"
        :class="careersInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <p class="text-gray-600 mb-4">
          Don't see a position that matches your skills?
        </p>
        <a
          href="{{ route('site.contact') }}"
          class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold transition-all shadow-lg hover:shadow-xl"
        >
          Send Us Your Resume
        </a>
      </div>
    </div>
  </section>
</div>
@endsection
