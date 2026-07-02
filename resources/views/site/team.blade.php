@extends('layouts.site')

@section('content')
@php
  $team = $team ?? \App\Support\SiteData::team();
  $heroImagePath = \App\Models\SiteSetting::getValue('team.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('team.hero.position_y', 50);
@endphp

{{-- Matches src/app/pages/TeamPage.tsx + src/app/components/Team.tsx --}}
<div>
  <section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-[640px]">
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Team hero"
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
        <h1 class="mb-4 text-3xl font-bold text-white max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-6xl md:[text-shadow:none]">Team</h1>
        <p class="mx-auto text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:text-lg md:max-w-3xl md:text-2xl md:[text-shadow:none]">
          Meet the leaders guiding LITUS Group across our portfolio of companies
        </p>
      </div>
    </div>
  </section>

  <section id="team" class="bg-white py-14 md:py-24" data-team-page>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      @if(empty($team))
        @php return; @endphp
      @endif
      <div
        class="site-team-motion-header mb-10 text-center transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        x-data="{
          inView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
            else if (window.matchMedia('(max-width: 767px)').matches) this.inView = true;
          }
        }"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
        :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">
          Meet Our Leadership Team
        </h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Visionary leaders driving excellence across LITUS Group's diverse portfolio of companies
        </p>
      </div>

      @php
        $visibleTeam = array_values(array_filter($team, fn ($m) => ! empty($m['image'])));
      @endphp

      <div class="flex flex-wrap justify-center gap-3 sm:gap-5 lg:gap-6">
        @foreach($visibleTeam as $index => $member)
          <div
            class="site-team-motion-card flex w-[calc(50%-0.375rem)] max-w-[280px] flex-col text-center transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto sm:w-[280px] md:will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            x-data="{
              cardInView: false,
              init() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.cardInView = true;
                else if (window.matchMedia('(max-width: 767px)').matches) this.cardInView = true;
              }
            }"
            x-intersect.once.margin.-100px.-100px.-100px.-100px="cardInView = true"
            :class="cardInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          >
            <div class="group relative mx-auto mb-2 aspect-square w-full overflow-hidden rounded-xl bg-gray-100 shadow-lg sm:mb-3 md:mb-4 md:rounded-lg">
              <img
                src="{{ $member['image'] }}"
                alt="{{ $member['name'] }}"
                class="h-full w-full object-cover object-center transition duration-500 ease-out md:group-hover:scale-105"
                loading="lazy"
                decoding="async"
              />
              @if(!empty($member['linkedin_url']) || !empty($member['email']))
                <div class="absolute inset-0 hidden bg-gradient-to-t from-blue-900/90 via-blue-900/30 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 md:block">
                  <div class="absolute bottom-0 left-0 right-0 flex justify-center gap-3 p-4 sm:p-6">
                    @if(!empty($member['linkedin_url']))
                      <a
                        href="{{ $member['linkedin_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rounded-full bg-white p-2.5 text-blue-900 shadow-lg transition-transform hover:scale-110 hover:bg-blue-50 sm:p-3"
                        aria-label="LinkedIn — {{ $member['name'] }}"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                          <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                          <rect width="4" height="12" x="2" y="9" />
                          <circle cx="4" cy="4" r="2" />
                        </svg>
                      </a>
                    @endif
                    @if(!empty($member['email']))
                      <a
                        href="mailto:{{ $member['email'] }}"
                        class="rounded-full bg-white p-2.5 text-blue-900 shadow-lg transition-transform hover:scale-110 hover:bg-blue-50 sm:p-3"
                        aria-label="Email — {{ $member['name'] }}"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                          <rect width="20" height="16" x="2" y="4" rx="2" />
                          <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                      </a>
                    @endif
                  </div>
                </div>
              @endif
            </div>

            @if(!empty($member['linkedin_url']) || !empty($member['email']))
              <div class="mb-2 flex justify-center gap-2 md:hidden">
                @if(!empty($member['linkedin_url']))
                  <a
                    href="{{ $member['linkedin_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="rounded-full bg-blue-50 p-2 text-blue-900 transition-colors hover:bg-blue-100"
                    aria-label="LinkedIn — {{ $member['name'] }}"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                      <rect width="4" height="12" x="2" y="9" />
                      <circle cx="4" cy="4" r="2" />
                    </svg>
                  </a>
                @endif
                @if(!empty($member['email']))
                  <a
                    href="mailto:{{ $member['email'] }}"
                    class="rounded-full bg-blue-50 p-2 text-blue-900 transition-colors hover:bg-blue-100"
                    aria-label="Email — {{ $member['name'] }}"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                      <rect width="20" height="16" x="2" y="4" rx="2" />
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                  </a>
                @endif
              </div>
            @endif

            <div class="w-full">
              <h3 class="mb-1 text-xs font-bold leading-snug text-gray-900 sm:mb-1.5 sm:text-base md:text-lg">
                {{ $member['name'] }}
              </h3>
              @if(!empty($member['role']))
                <div class="mb-1.5 text-[0.7rem] font-semibold text-blue-600 sm:mb-2 sm:text-xs">
                  {{ $member['role'] }}
                </div>
              @endif
              @if(!empty($member['bio']))
                <p class="mb-2 line-clamp-3 text-[0.7rem] leading-relaxed text-gray-600 sm:line-clamp-none sm:mb-3 sm:text-xs">
                  {{ $member['bio'] }}
                </p>
              @endif
              @if(!empty($member['expertise']))
                <div class="mx-auto inline-block max-w-full border-l-[3px] border-blue-600 pl-2 text-left sm:pl-2.5">
                  <p class="line-clamp-2 text-[0.625rem] font-medium text-gray-500 sm:line-clamp-none sm:text-[0.7rem]">
                    {{ $member['expertise'] }}
                  </p>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
