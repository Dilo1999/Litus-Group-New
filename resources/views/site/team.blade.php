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
  {{-- ============================== HERO ============================== --}}
  <section class="relative isolate flex flex-col overflow-hidden bg-ink-950 text-white {{ filled($heroImageUrl) ? 'min-h-[520px] md:min-h-[620px]' : 'min-h-[440px] md:min-h-[520px]' }}">
    <div class="absolute inset-0 -z-10">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Team hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
        <div class="absolute inset-0 bg-ink-950/45"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/10"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/70 to-transparent"></div>
      @else
        <div class="ui-page-hero__glow"></div>
        <div class="ui-grid-texture--light absolute inset-0 opacity-60"></div>
      @endif
    </div>

    <div class="ui-container flex flex-1 flex-col justify-end pt-36 pb-14 md:pt-44 md:pb-20">
      <div class="site-blogs-hero max-w-3xl">
        <span class="ui-eyebrow ui-eyebrow--light">Our People</span>
        <h1 class="ui-h1 mt-6 text-white">
          Our <span class="ui-accent text-lagoon-300">team</span>
        </h1>
        <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-200 sm:text-lg md:mt-8 md:text-xl">
          Meet the leaders guiding LITUS Group across our portfolio of companies
        </p>
      </div>
    </div>
  </section>

  {{-- ============================== TEAM GRID ============================== --}}
  <section id="team" class="ui-section relative bg-sand-100" data-team-page>
    <div class="ui-container">
      @if(empty($team))
        @php return; @endphp
      @endif
      <div
        class="site-team-motion-header mb-12 flex flex-col gap-6 transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform] lg:flex-row lg:items-end lg:justify-between"
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
        <div class="max-w-2xl">
          <span class="ui-eyebrow">Leadership</span>
          <h2 class="ui-h2 mt-5">Meet our <span class="ui-accent text-brand-600">leadership</span> team</h2>
        </div>
        <p class="ui-lead max-w-md lg:text-lg">
          Visionary leaders driving excellence across LITUS Group's diverse portfolio of companies
        </p>
      </div>

      @php
        $visibleTeam = array_values(array_filter($team, fn ($m) => ! empty($m['image'])));
      @endphp

      <div class="flex flex-wrap justify-center gap-x-3 gap-y-8 sm:gap-x-5 sm:gap-y-10 lg:gap-x-6">
        @foreach($visibleTeam as $index => $member)
          <div
            class="site-team-motion-card flex w-[calc(50%-0.375rem)] min-w-0 flex-col sm:w-[calc(50%-0.625rem)] md:w-[calc(33.333%-0.834rem)] {{ count($visibleTeam) >= 4 ? 'lg:w-[calc(25%-1.125rem)]' : 'lg:w-[calc(33.333%-1rem)]' }} transition-[opacity,transform] duration-[800ms] ease-out max-md:will-change-auto md:will-change-[opacity,transform]"
            style="transition-delay: {{ ($index % 4) * 100 }}ms"
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
            <article class="group relative aspect-[4/5] w-full overflow-hidden rounded-3xl bg-gradient-to-br from-ink-800 to-ink-900 shadow-[0_24px_50px_-28px_rgba(6,22,52,0.55)] ring-1 ring-ink-900/10">
              <img
                src="{{ $member['image'] }}"
                alt="{{ $member['name'] }}"
                class="absolute inset-0 h-full w-full object-cover object-top transition-transform duration-[1200ms] ease-out-expo group-hover:scale-105"
                loading="lazy"
                decoding="async"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/15 to-transparent"></div>

              @if(!empty($member['linkedin_url']) || !empty($member['email']))
                <div class="absolute top-2.5 right-2.5 flex gap-1.5 transition-all duration-500 sm:top-3 sm:right-3 sm:gap-2 md:translate-y-[-6px] md:opacity-0 md:group-hover:translate-y-0 md:group-hover:opacity-100 md:group-focus-within:translate-y-0 md:group-focus-within:opacity-100">
                  @if(!empty($member['linkedin_url']))
                    <a
                      href="{{ $member['linkedin_url'] }}"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="ui-icon-btn bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-md hover:bg-white hover:text-ink-900 focus-visible:outline-2 focus-visible:outline-white md:!h-10 md:!w-10"
                      aria-label="LinkedIn — {{ $member['name'] }}"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                        <rect width="4" height="12" x="2" y="9" />
                        <circle cx="4" cy="4" r="2" />
                      </svg>
                    </a>
                  @endif
                  @if(!empty($member['email']))
                    <a
                      href="mailto:{{ $member['email'] }}"
                      class="ui-icon-btn bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-md hover:bg-white hover:text-ink-900 focus-visible:outline-2 focus-visible:outline-white md:!h-10 md:!w-10"
                      aria-label="Email — {{ $member['name'] }}"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="16" x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                      </svg>
                    </a>
                  @endif
                </div>
              @endif

              <div class="absolute inset-x-0 bottom-0 p-3.5 sm:p-5">
                <h3 class="text-sm leading-snug font-bold text-white sm:text-lg">{{ $member['name'] }}</h3>
                @if(!empty($member['role']))
                  <p class="mt-1 text-[0.7rem] leading-snug font-medium text-lagoon-300 sm:text-sm">{{ $member['role'] }}</p>
                @endif
              </div>
            </article>

            @if(!empty($member['bio']) || !empty($member['expertise']))
              <div class="mt-4 px-1">
                @if(!empty($member['bio']))
                  <p class="line-clamp-4 text-xs leading-relaxed text-ink-500 sm:line-clamp-none sm:text-sm">
                    {{ $member['bio'] }}
                  </p>
                @endif
                @if(!empty($member['expertise']))
                  <p class="mt-3 border-l-2 border-brand-500 pl-2.5 text-[0.7rem] leading-snug font-semibold text-ink-700 sm:text-xs">
                    {{ $member['expertise'] }}
                  </p>
                @endif
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
