@extends('layouts.site')

@section('content')
@php
  use App\Support\SiteData;
  $divisions = $divisions ?? SiteData::divisions();
  $companies = $companies ?? SiteData::companies();
  $divisionOrder = $divisionOrder ?? [
    'logistics-shipping',
    'automotive',
    'trading',
    'construction',
    'technology-retail',
    'hospitality-lifestyle',
  ];
@endphp

{{-- PageHero (matches reference: gradient hero, title + subtitle, initial motion 0.8s / y≈30) --}}
<section class="relative pt-32 pb-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="site-our-companies-hero text-center text-white">
      <h1 class="text-5xl md:text-6xl font-bold mb-6">Our Companies</h1>
      <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto">
        Explore our diverse portfolio of 16 specialized companies delivering excellence across multiple industries
      </p>
    </div>
  </div>
</section>

{{-- CompaniesByDivision: single inView gate, stagger division 100ms, card 50ms (reference parity) --}}
<section
  class="py-24 bg-white"
  x-data="{
    inView: false,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
    }
  }"
  x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
  data-companies-stagger
>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-20">
      @foreach($divisionOrder as $divIndex => $divisionKey)
        @php
          $division = $divisions[$divisionKey] ?? null;
          $divisionCompanies = array_values(array_filter($companies, fn ($c) => ($c['division'] ?? '') === $divisionKey));
        @endphp

        @if($division && count($divisionCompanies))
          <div>
            <div
              class="site-companies-motion-division will-change-[opacity,transform] transition-[opacity,transform] duration-[800ms] ease-out"
              :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
              style="transition-delay: {{ $divIndex * 100 }}ms"
            >
              <div class="mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $division['title'] }}</h2>
                <p class="text-lg text-gray-600">{{ $division['description'] }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($divisionCompanies as $index => $company)
                <div
                  class="site-companies-motion-card will-change-[opacity,transform] transition-[opacity,transform] duration-500 ease-out h-full"
                  :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                  style="transition-delay: {{ $divIndex * 100 + $index * 50 }}ms"
                >
                  <div class="relative bg-gradient-to-br from-white to-gray-50 border border-gray-200 hover:border-blue-300 rounded-2xl p-6 transition-all hover:shadow-xl group cursor-pointer h-full flex flex-col">
                    <a
                      href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                      class="absolute inset-0 z-10 rounded-2xl"
                      aria-label="View {{ $company['name'] }}"
                    ></a>

                    <div class="relative z-0 flex flex-col flex-1 min-h-0">
                      @php
                        $logoSrc = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
                      @endphp
                      @if($logoSrc)
                        <div class="w-full h-20 flex items-center justify-center mb-4">
                          <img
                            src="{{ $logoSrc }}"
                            alt="{{ $company['name'] }}"
                            class="max-h-16 max-w-[200px] object-contain"
                          />
                        </div>
                      @else
                        <div class="bg-blue-100 group-hover:bg-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4 transition-all relative z-0">
                          <x-site.lucide-icon
                            :name="$company['icon'] ?? 'building2'"
                            class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors"
                          />
                        </div>
                      @endif

                      <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                          {{ $company['name'] }}
                        </h3>
                        <p class="text-sm text-blue-600 font-medium mb-3">{{ $company['category'] }}</p>
                        <p class="text-gray-600 mb-4 leading-relaxed">{{ $company['description'] }}</p>
                      </div>
                    </div>

                    <div class="relative z-20 flex items-center justify-between pt-4 border-t border-gray-200 mt-auto">
                      <a
                        href="tel:{{ $company['hotline'] }}"
                        class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors"
                        onclick="event.stopPropagation()"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <span class="text-sm font-medium">{{ $company['hotline'] }}</span>
                      </a>
                      <div class="flex items-center gap-1 text-blue-600 font-semibold text-sm group-hover:gap-2 transition-all pointer-events-none">
                        View Company
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                          <path d="M5 12h14" />
                          <path d="m12 5 7 7-7 7" />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </div>
</section>
@endsection
