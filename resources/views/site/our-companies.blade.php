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
@endphp

{{-- PageHero --}}
<section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-[640px]">
  <div class="absolute inset-0 z-0">
    @if(filled($heroImageUrl))
      <img
        src="{{ $heroImageUrl }}"
        alt="Our Companies hero"
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
  <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 md:py-24 lg:px-8">
    <div class="site-our-companies-hero mx-auto max-w-3xl text-center text-white">
      <h1 class="mb-4 text-3xl font-bold max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-6xl md:[text-shadow:none]">Our Entities</h1>
      <p class="mx-auto text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:text-lg md:max-w-3xl md:text-2xl md:[text-shadow:none]">
        Explore our diverse portfolio of 16 specialized companies delivering excellence across multiple industries
      </p>
    </div>
  </div>
</section>

{{-- CompaniesByDivision --}}
<section
  class="bg-white py-14 md:py-24"
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
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="space-y-12 md:space-y-20">
      @foreach($divisionOrder as $divIndex => $divisionKey)
        @php
          $division = $divisions[$divisionKey] ?? null;
          $divisionCompanies = array_values(array_filter($companies, fn ($c) => ($c['division'] ?? '') === $divisionKey));
        @endphp

        @if($division && count($divisionCompanies))
          <div>
            <div
              class="site-companies-motion-division transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
              :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
              style="transition-delay: {{ $divIndex * 100 }}ms"
            >
              <div class="mb-6 text-center md:mb-8 md:text-left">
                <h2 class="mb-2 text-xl font-bold text-gray-900 sm:text-2xl md:text-4xl">{{ $division['title'] }}</h2>
                <p class="text-sm leading-relaxed text-gray-600 sm:text-base md:text-lg">{{ $division['description'] }}</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 md:gap-6 lg:grid-cols-3">
              @foreach($divisionCompanies as $index => $company)
                <div
                  class="site-companies-motion-card h-full transition-[opacity,transform] duration-500 ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
                  :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                  style="transition-delay: {{ $divIndex * 100 + $index * 50 }}ms"
                >
                  <div class="group relative flex h-full cursor-pointer flex-col rounded-xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-3 text-center transition-all max-md:items-center sm:rounded-2xl sm:p-6 md:text-left md:hover:border-blue-300 md:hover:shadow-xl">
                    <a
                      href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                      class="absolute inset-0 z-10 rounded-2xl"
                      aria-label="View {{ $company['name'] }}"
                    ></a>

                    <div class="relative z-0 flex min-h-0 flex-1 flex-col">
                      @php
                        $logoSrc = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
                      @endphp
                      @if($logoSrc)
                        <div class="mb-2 flex h-12 w-full items-center justify-center sm:mb-4 sm:h-20">
                          <img
                            src="{{ $logoSrc }}"
                            alt="{{ $company['name'] }}"
                            class="max-h-10 max-w-full object-contain sm:max-h-16 sm:max-w-[200px]"
                            loading="lazy"
                            decoding="async"
                          />
                        </div>
                      @else
                        <div class="relative z-0 mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 transition-all group-hover:bg-blue-600 sm:mb-4 sm:h-16 sm:w-16 md:mx-0">
                          <x-site.lucide-icon
                            :name="$company['icon'] ?? 'building2'"
                            class="h-6 w-6 text-blue-600 transition-colors group-hover:text-white sm:h-8 sm:w-8"
                          />
                        </div>
                      @endif

                      <div class="flex-1 w-full">
                        <h3 class="mb-1 line-clamp-2 text-xs font-bold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 sm:mb-2 sm:text-xl sm:line-clamp-none">
                          {{ $company['name'] }}
                        </h3>
                        <p class="mb-1.5 text-[0.65rem] font-medium text-blue-600 sm:mb-3 sm:text-sm">{{ $company['category'] }}</p>
                        <p class="mb-2 line-clamp-3 text-[0.65rem] leading-snug text-gray-600 sm:mb-4 sm:line-clamp-none sm:text-sm">{{ $company['description'] }}</p>
                      </div>
                    </div>

                    <div class="relative z-20 mt-auto flex w-full flex-col gap-2 border-t border-gray-200 pt-2 sm:gap-3 sm:pt-4 md:flex-row md:items-center md:justify-between">
                      <a
                        href="tel:{{ $company['hotline'] }}"
                        class="flex min-h-[2.25rem] w-full items-center justify-center gap-1.5 rounded-full bg-gray-50 px-2 text-gray-700 transition-colors hover:bg-blue-50 hover:text-blue-600 sm:min-h-[2.75rem] sm:gap-2 sm:px-4 md:min-h-0 md:w-auto md:justify-start md:rounded-none md:bg-transparent md:px-0"
                        onclick="event.stopPropagation()"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 sm:h-4 sm:w-4" aria-hidden="true">
                          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <span class="truncate text-[0.65rem] font-semibold sm:text-sm">{{ $company['hotline'] }}</span>
                      </a>
                      <div class="pointer-events-none flex items-center justify-center gap-0.5 text-[0.65rem] font-semibold text-blue-600 transition-all group-hover:gap-2 sm:text-sm md:justify-end">
                        <span class="max-md:sr-only">View Company</span>
                        <span class="md:hidden" aria-hidden="true">View</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 sm:h-4 sm:w-4" aria-hidden="true">
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
