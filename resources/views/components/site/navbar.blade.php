@php
  use App\Support\SiteData;

  $navItems = [
    ['label' => 'Home', 'route' => 'site.home'],
    ['label' => 'Our Companies', 'route' => 'site.our-companies', 'dropdown' => true],
    ['label' => 'About Us', 'route' => 'site.about'],
    ['label' => 'Team', 'route' => 'site.team'],
    ['label' => 'Careers', 'route' => 'site.careers'],
    ['label' => 'News & Media', 'route' => 'site.blogs'],
    ['label' => 'Contact Us', 'route' => 'site.contact'],
  ];

  $companies = SiteData::companies();
  $companyLogoUrls = array_values(array_filter(array_map(
    static fn (array $c) => SiteData::companyLogoUrl($c['logo'] ?? null),
    $companies
  )));
  // Pages whose top section is dark behind the navbar.
  // On these pages we use light (white) ink when the navbar is transparent.
  $heroTopIsDark = request()->routeIs([
    'site.home',
    'site.company',
    'site.our-companies',
    'site.blogs',
    'site.event',
    'site.about',
    'site.team',
    'site.careers',
    'site.contact',
  ]);
@endphp

<div
  data-company-logo-urls='@json($companyLogoUrls)'
  x-data="siteNavbar({ heroTopIsDark: @js($heroTopIsDark), companyLogoUrls: @js($companyLogoUrls) })"
>
  <nav
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="navSolid ? 'bg-white shadow-md lg:bg-white/95 lg:backdrop-blur-md' : 'bg-transparent'"
  >
    <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
      <div class="flex items-center h-20">
        <div class="flex min-w-0 flex-1 items-center justify-start">
          <a href="{{ route('site.home') }}" class="flex shrink-0 cursor-pointer select-none items-center">
            <img
              src="{{ SiteData::brandLogoUrl() }}"
              alt="LITUS Group"
              class="h-16 md:h-20 w-auto transition-all duration-300"
              :class="navOnDarkHero ? 'brightness-0 invert' : ''"
            />
          </a>
        </div>

        <div class="hidden shrink-0 items-center space-x-8 lg:flex lg:space-x-10">
          @foreach($navItems as $item)
            @if(!empty($item['dropdown']))
              {{-- CSS-only hover (site-nav-companies): cannot stick open like Alpine companiesOpen state --}}
              <div class="site-nav-companies relative">
                <a
                  href="{{ route($item['route']) }}"
                  class="transition-colors font-semibold text-base flex items-center gap-1.5"
                  :class="navOnDarkHero ? 'text-white hover:text-blue-300' : 'text-gray-700 hover:text-blue-600'"
                >
                  {{ $item['label'] }}
                  <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 0 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                  </svg>
                </a>

                <div class="site-nav-companies__panel absolute left-1/2 top-full z-[60] pt-2">
                  <div class="w-[600px] rounded-2xl border border-gray-100 bg-white p-8 shadow-2xl">
                  <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                    @foreach($companies as $company)
                      @php
                        $logoSrc = SiteData::companyLogoUrl($company['logo'] ?? null);
                      @endphp
                      <a
                        href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                        class="block"
                      >
                        <div class="flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group/company-row w-full">
                          @if($logoSrc)
                            <div
                              class="w-16 h-8 flex shrink-0 items-center justify-center"
                            >
                              <img
                                :src="logoSrc(@js($logoSrc))"
                                :key="(logosWarmed ? 'cached-' : 'pending-') + @js($logoSrc)"
                                alt=""
                                width="64"
                                height="32"
                                loading="eager"
                                decoding="sync"
                                class="max-h-full max-w-full object-contain object-left"
                              />
                            </div>
                          @else
                            <div class="flex h-8 w-16 shrink-0 items-center justify-center text-gray-400 group-hover/company-row:text-blue-600 [&_svg]:size-6">
                              <x-site.lucide-icon :name="$company['icon'] ?? 'building2'" />
                            </div>
                          @endif
                          <span class="font-medium group-hover/company-row:text-blue-600 transition-colors text-sm">
                            {{ $company['name'] }}
                          </span>
                        </div>
                      </a>
                    @endforeach
                  </div>
                  </div>
                </div>
              </div>
            @else
              <div>
                <a
                  href="{{ route($item['route']) }}"
                  class="transition-colors font-semibold text-base"
                  :class="navOnDarkHero ? 'text-white hover:text-blue-300' : 'text-gray-700 hover:text-blue-600'"
                >
                  {{ $item['label'] }}
                </a>
              </div>
            @endif
          @endforeach
        </div>

        <div class="flex min-w-0 flex-1 items-center justify-end">
        <button
          type="button"
          class="lg:hidden p-2"
          :class="navOnDarkHero ? 'text-white' : 'text-gray-900'"
          @click="mobileOpen = !mobileOpen"
          :aria-expanded="mobileOpen"
          aria-label="Toggle menu"
        >
          <svg x-show="!mobileOpen" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 5h16M4 12h16M4 19h16" />
          </svg>
          <svg x-show="mobileOpen" x-cloak class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 6 6 18M6 6l12 12" />
          </svg>
        </button>
        </div>
      </div>
    </div>
  </nav>

  {{-- Mobile sheet: logo + close header, divider, full-bleed row borders (matches design reference) --}}
  <div
    x-show="mobileOpen"
    x-cloak
    x-transition:enter="transition-[transform,opacity] duration-[450ms] ease-[cubic-bezier(0.22,1,0.36,1)]"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition-[transform,opacity] duration-350 ease-[cubic-bezier(0.45,0,0.55,1)]"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed inset-0 z-[60] flex h-[100dvh] max-h-[100dvh] w-full min-h-0 flex-col overflow-x-hidden bg-white lg:hidden"
    @keydown.escape.window="mobileOpen = false"
  >
    <div class="flex min-h-[4rem] shrink-0 items-center justify-between border-b border-gray-200 px-6 pb-3 pt-[max(0.75rem,env(safe-area-inset-top))]">
      <a
        href="{{ route('site.home') }}"
        class="flex min-w-0 flex-1 items-center pr-4"
        @click="mobileOpen = false"
      >
        <img
          src="{{ SiteData::brandLogoUrl() }}"
          alt="LITUS Group"
          class="h-10 w-auto max-w-[min(240px,60vw)] object-contain object-left"
        />
      </a>
      <button
        type="button"
        class="flex h-11 w-11 shrink-0 items-center justify-center text-[var(--site-mobile-nav-ink)]"
        @click="mobileOpen = false"
        aria-label="Close menu"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M18 6 6 18M6 6l12 12" />
        </svg>
      </button>
    </div>

    <nav class="site-mobile-nav-list min-h-0 flex-1 overflow-y-auto overscroll-y-contain pb-[max(1.5rem,env(safe-area-inset-bottom))]" aria-label="Mobile">
      @foreach($navItems as $item)
        @if(!empty($item['dropdown']))
          <div class="border-b border-gray-100">
            <button
              type="button"
              class="flex min-h-[3.5rem] w-full items-center justify-between gap-4 py-4 pl-6 pr-8 text-left text-lg font-bold leading-snug text-[var(--site-mobile-nav-ink)] transition-colors hover:text-blue-700 active:bg-gray-50/80"
              @click="mobileCompaniesOpen = !mobileCompaniesOpen"
            >
              <span class="min-w-0">{{ $item['label'] }}</span>
              <svg
                class="h-5 w-5 shrink-0 text-[var(--site-mobile-nav-ink)] transition-transform duration-300 ease-out"
                :class="mobileCompaniesOpen ? 'rotate-180' : ''"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>

            {{-- No max-height animation: animating toward 2000px causes heavy reflow on mobile. --}}
            <div
              x-show="mobileCompaniesOpen"
              x-collapse.duration.200ms
              class="overflow-hidden bg-gray-50"
            >
              @foreach($companies as $company)
                @php
                  $logoSrc = SiteData::companyLogoUrl($company['logo'] ?? null);
                @endphp
                <a
                  href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                  class="flex min-h-[3rem] w-full items-center gap-3 border-b border-gray-100 px-6 py-3 text-left text-sm font-semibold text-[var(--site-mobile-nav-ink)] last:border-b-0 hover:bg-blue-50/80 hover:text-blue-700 active:bg-gray-100/80"
                  @click="mobileOpen = false; mobileCompaniesOpen = false"
                >
                  @if($logoSrc)
                    <div
                      class="flex h-6 w-12 shrink-0 items-center justify-center"
                    >
                      <img
                        :src="logoSrc(@js($logoSrc))"
                        :key="(logosWarmed ? 'cached-' : 'pending-') + @js($logoSrc)"
                        alt="{{ $company['name'] }}"
                        width="48"
                        height="24"
                        loading="eager"
                        decoding="sync"
                        class="max-h-full max-w-full object-contain"
                      />
                    </div>
                  @else
                    <div class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-600 [&_svg]:size-5">
                      <x-site.lucide-icon :name="$company['icon'] ?? 'building2'" />
                    </div>
                  @endif
                  <span class="min-w-0">{{ $company['name'] }}</span>
                </a>
              @endforeach
            </div>
          </div>
        @else
          <a
            href="{{ route($item['route']) }}"
            class="flex min-h-[3.5rem] w-full items-center border-b border-gray-100 px-6 py-4 text-left text-lg font-bold leading-snug text-[var(--site-mobile-nav-ink)] transition-colors hover:text-blue-700 active:bg-gray-50/80"
            @click="mobileOpen = false"
          >
            {{ $item['label'] }}
          </a>
        @endif
      @endforeach
    </nav>
  </div>
</div>
