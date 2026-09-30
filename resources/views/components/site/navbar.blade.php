@php
  use App\Support\SiteData;

  $navItems = [
    ['label' => 'Home', 'route' => 'site.home', 'active' => ['site.home']],
    ['label' => 'Our Entities', 'route' => 'site.our-companies', 'dropdown' => true, 'active' => ['site.our-companies', 'site.company']],
    ['label' => 'About Us', 'route' => 'site.about', 'active' => ['site.about']],
    ['label' => 'Team', 'route' => 'site.team', 'active' => ['site.team']],
    ['label' => 'Careers', 'route' => 'site.careers', 'active' => ['site.careers']],
    ['label' => 'News & Media', 'route' => 'site.blogs', 'active' => ['site.blogs', 'site.blog-article', 'site.event']],
    ['label' => 'Contact Us', 'route' => 'site.contact', 'active' => ['site.contact'], 'cta' => true],
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
    'site.blog-article',
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
  <nav class="fixed inset-x-0 top-0 z-50 transition-[padding] duration-500 ease-out-expo" :class="navSolid ? 'lg:pt-3' : 'lg:pt-0'" aria-label="Primary">
    <div class="mx-auto max-w-7xl lg:px-5">
      <div
        class="flex h-[4.5rem] items-center gap-6 px-5 transition-all duration-500 ease-out-expo sm:px-6 lg:h-[4.75rem] lg:rounded-full lg:pl-6 lg:pr-3"
        :class="navSolid
          ? 'border-b border-ink-100 bg-white/90 shadow-[0_12px_40px_-18px_rgba(6,22,52,0.35)] backdrop-blur-xl lg:border lg:border-white/60 lg:h-16'
          : 'border-b border-transparent bg-transparent'"
      >
        <div class="flex min-w-0 flex-1 items-center">
          <a href="{{ route('site.home') }}" class="flex shrink-0 select-none items-center" aria-label="LITUS Group — Home">
            <img
              src="{{ SiteData::brandLogoUrl() }}"
              alt="LITUS Group"
              class="w-auto transition-all duration-500 ease-out-expo"
              :class="[navOnDarkHero ? 'brightness-0 invert' : '', navSolid ? 'h-12 lg:h-14' : 'h-14 lg:h-[4.5rem]']"
            />
          </a>
        </div>

        <div class="hidden shrink-0 items-center gap-1 lg:flex">
          @foreach($navItems as $item)
            @continue(!empty($item['cta']))
            @php $isActive = request()->routeIs($item['active']); @endphp
            @if(!empty($item['dropdown']))
              {{-- CSS-only hover (site-nav-companies): cannot stick open like Alpine companiesOpen state --}}
              <div class="site-nav-companies group relative">
                <a
                  href="{{ route($item['route']) }}"
                  @class(['relative flex items-center gap-1 rounded-full px-3.5 py-2 text-[0.9rem] font-semibold transition-colors xl:px-4'])
                  :class="navOnDarkHero
                    ? '{{ $isActive ? 'text-white bg-white/10' : 'text-white/85 hover:text-white hover:bg-white/10' }}'
                    : '{{ $isActive ? 'text-ink-900 bg-ink-100' : 'text-ink-600 hover:text-ink-900 hover:bg-ink-100/70' }}'"
                  @if($isActive) aria-current="page" @endif
                >
                  {{ $item['label'] }}
                  <svg class="h-4 w-4 opacity-70 transition-transform duration-300 group-hover:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 0 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                  </svg>
                </a>

                <div class="site-nav-companies__panel absolute left-1/2 top-full z-[60] pt-4">
                  <div class="flex w-[820px] overflow-hidden rounded-[1.75rem] border border-ink-100 bg-white shadow-[0_40px_80px_-30px_rgba(6,22,52,0.45)]">
                    <div class="ui-bg-ink flex w-60 shrink-0 flex-col justify-between p-7">
                      <div>
                        <span class="ui-eyebrow ui-eyebrow--light">Our Entities</span>
                        <p class="mt-4 text-2xl font-bold leading-tight tracking-tight text-white">
                          {{ count($companies) }} companies.
                          <span class="ui-accent text-lagoon-300">One vision.</span>
                        </p>
                        <p class="mt-3 text-sm leading-relaxed text-ink-300">
                          Hospitality, construction, automotive, technology and trading across the Maldives.
                        </p>
                      </div>
                      <a href="{{ route('site.our-companies') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-white transition-colors hover:text-lagoon-300">
                        View all entities
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                      </a>
                    </div>
                    <div class="grid flex-1 grid-cols-2 gap-1 p-3">
                      @foreach($companies as $company)
                        @php
                          $logoSrc = SiteData::companyLogoUrl($company['logo'] ?? null);
                        @endphp
                        <a
                          href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                          class="group/company-row flex items-center gap-3 rounded-2xl px-3 py-2.5 text-left transition-colors hover:bg-sand-100"
                        >
                          <div class="flex h-10 w-14 shrink-0 items-center justify-center rounded-xl bg-white p-1.5 ring-1 ring-ink-100 transition-all group-hover/company-row:ring-brand-100">
                            @if($logoSrc)
                              <img
                                :src="logoSrc(@js($logoSrc))"
                                :key="(logosWarmed ? 'cached-' : 'pending-') + @js($logoSrc)"
                                alt=""
                                width="56"
                                height="40"
                                loading="eager"
                                decoding="sync"
                                class="max-h-full max-w-full object-contain"
                              />
                            @else
                              <span class="text-ink-400 group-hover/company-row:text-brand-600 [&_svg]:size-5">
                                <x-site.lucide-icon :name="$company['icon'] ?? 'building2'" />
                              </span>
                            @endif
                          </div>
                          <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-ink-900 transition-colors group-hover/company-row:text-brand-600">{{ $company['name'] }}</span>
                            @if(!empty($company['category']))
                              <span class="block truncate text-xs text-ink-400">{{ $company['category'] }}</span>
                            @endif
                          </span>
                        </a>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            @else
              <a
                href="{{ route($item['route']) }}"
                class="relative rounded-full px-3.5 py-2 text-[0.9rem] font-semibold transition-colors xl:px-4"
                :class="navOnDarkHero
                  ? '{{ $isActive ? 'text-white bg-white/10' : 'text-white/85 hover:text-white hover:bg-white/10' }}'
                  : '{{ $isActive ? 'text-ink-900 bg-ink-100' : 'text-ink-600 hover:text-ink-900 hover:bg-ink-100/70' }}'"
                @if($isActive) aria-current="page" @endif
              >
                {{ $item['label'] }}
              </a>
            @endif
          @endforeach
        </div>

        <div class="flex min-w-0 flex-1 items-center justify-end gap-2">
          <a
            href="{{ route('site.contact') }}"
            class="ui-btn ui-btn--sm hidden lg:inline-flex"
            :class="navOnDarkHero ? 'ui-btn--light' : 'ui-btn--dark'"
          >
            Contact Us
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
          </a>

          <button
            type="button"
            class="ui-icon-btn lg:hidden"
            :class="navOnDarkHero ? 'bg-white/10 text-white ring-1 ring-white/25 backdrop-blur-md' : 'bg-ink-900 text-white'"
            @click="mobileOpen = !mobileOpen"
            :aria-expanded="mobileOpen"
            aria-label="Toggle menu"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
              <path d="M4 8h16M4 16h10" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </nav>

  {{-- Mobile sheet: full-screen navy panel with large type --}}
  <div
    x-show="mobileOpen"
    x-cloak
    x-transition:enter="transition-[transform,opacity] duration-[450ms] ease-[cubic-bezier(0.22,1,0.36,1)]"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition-[transform,opacity] duration-350 ease-[cubic-bezier(0.45,0,0.55,1)]"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="ui-bg-ink fixed inset-0 z-[60] flex h-[100dvh] max-h-[100dvh] w-full min-h-0 flex-col overflow-x-hidden lg:hidden"
    @keydown.escape.window="mobileOpen = false"
    role="dialog"
    aria-modal="true"
    aria-label="Menu"
  >
    <div class="flex h-[4.5rem] shrink-0 items-center justify-between px-5 pt-[env(safe-area-inset-top)] sm:px-6">
      <a href="{{ route('site.home') }}" class="flex min-w-0 items-center" @click="mobileOpen = false">
        <img
          src="{{ SiteData::brandLogoUrl() }}"
          alt="LITUS Group"
          class="h-14 w-auto max-w-[min(260px,70vw)] object-contain object-left brightness-0 invert"
        />
      </a>
      <button
        type="button"
        class="ui-icon-btn bg-white/10 text-white ring-1 ring-white/20"
        @click="mobileOpen = false"
        aria-label="Close menu"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M18 6 6 18M6 6l12 12" />
        </svg>
      </button>
    </div>

    <nav class="site-mobile-nav-list min-h-0 flex-1 overflow-y-auto overscroll-y-contain px-5 pt-6 sm:px-6" aria-label="Mobile">
      @foreach($navItems as $i => $item)
        @php $isActive = request()->routeIs($item['active']); @endphp
        @if(!empty($item['dropdown']))
          <div class="border-b border-white/10">
            <button
              type="button"
              class="flex w-full items-center justify-between gap-4 py-4 text-left"
              @click="mobileCompaniesOpen = !mobileCompaniesOpen"
              :aria-expanded="mobileCompaniesOpen"
            >
              <span class="flex items-baseline gap-4">
                <span class="w-6 text-xs font-semibold tabular-nums text-lagoon-300/80">{{ sprintf('%02d', $i + 1) }}</span>
                <span @class(['text-[1.65rem] font-bold leading-tight tracking-tight', 'text-lagoon-300' => $isActive, 'text-white' => !$isActive])>{{ $item['label'] }}</span>
              </span>
              <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition-transform duration-300" :class="mobileCompaniesOpen ? 'rotate-45' : ''">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
              </span>
            </button>

            {{-- No max-height animation: animating toward 2000px causes heavy reflow on mobile. --}}
            <div x-show="mobileCompaniesOpen" x-collapse.duration.200ms class="overflow-hidden">
              <div class="grid grid-cols-1 gap-1.5 pb-5 pl-10 sm:grid-cols-2">
                @foreach($companies as $company)
                  @php
                    $logoSrc = SiteData::companyLogoUrl($company['logo'] ?? null);
                  @endphp
                  <a
                    href="{{ route('site.company', ['slug' => $company['slug']]) }}"
                    class="flex items-center gap-3 rounded-2xl px-2 py-2 text-sm font-semibold text-white/85 transition-colors hover:bg-white/5 hover:text-white active:bg-white/10"
                    @click="mobileOpen = false; mobileCompaniesOpen = false"
                  >
                    <span class="flex h-9 w-12 shrink-0 items-center justify-center rounded-xl bg-white p-1.5">
                      @if($logoSrc)
                        <img
                          :src="logoSrc(@js($logoSrc))"
                          :key="(logosWarmed ? 'cached-' : 'pending-') + @js($logoSrc)"
                          alt=""
                          width="48"
                          height="36"
                          loading="eager"
                          decoding="sync"
                          class="max-h-full max-w-full object-contain"
                        />
                      @else
                        <span class="text-ink-500 [&_svg]:size-4"><x-site.lucide-icon :name="$company['icon'] ?? 'building2'" /></span>
                      @endif
                    </span>
                    <span class="min-w-0">{{ $company['name'] }}</span>
                  </a>
                @endforeach
              </div>
            </div>
          </div>
        @else
          <a
            href="{{ route($item['route']) }}"
            class="flex items-baseline gap-4 border-b border-white/10 py-4"
            @click="mobileOpen = false"
            @if($isActive) aria-current="page" @endif
          >
            <span class="w-6 text-xs font-semibold tabular-nums text-lagoon-300/80">{{ sprintf('%02d', $i + 1) }}</span>
            <span @class(['text-[1.65rem] font-bold leading-tight tracking-tight transition-colors', 'text-lagoon-300' => $isActive, 'text-white' => !$isActive])>{{ $item['label'] }}</span>
          </a>
        @endif
      @endforeach
    </nav>

    <div class="shrink-0 px-5 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-5 sm:px-6">
      <a href="mailto:info@litusgroup.com" class="ui-btn ui-btn--light w-full">
        info@litusgroup.com
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
      </a>
    </div>
  </div>
</div>
