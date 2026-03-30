@extends('layouts.site')

@section('content')
@php
  $team = $team ?? \App\Support\SiteData::team();
@endphp

{{-- Matches src/app/pages/TeamPage.tsx + src/app/components/Team.tsx --}}
<div class="pt-20">
  <section id="team" class="py-24 bg-white" data-team-page>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-team-motion-header text-center mb-16 transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
        x-data="{
          inView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.inView = true;
          }
        }"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="inView = true"
        :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
          Meet Our Leadership Team
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Visionary leaders driving excellence across LITUS Group's diverse portfolio of companies
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach($team as $index => $member)
          <div
            class="site-team-motion-card flex flex-col transition-[opacity,transform] duration-[800ms] ease-out will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            x-data="{
              cardInView: false,
              init() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.cardInView = true;
              }
            }"
            x-intersect.once.margin.-100px.-100px.-100px.-100px="cardInView = true"
            :class="cardInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
          >
            <div class="relative rounded-lg overflow-hidden shadow-lg group aspect-square mb-6 bg-gray-100">
              <img
                src="{{ $member['image'] }}"
                alt="{{ $member['name'] }}"
                class="w-full h-full object-cover object-center"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-6 flex gap-3 justify-center">
                  <button
                    type="button"
                    class="bg-white text-blue-900 p-3 rounded-full hover:bg-blue-50 transition-all transform hover:scale-110 shadow-lg"
                    aria-label="LinkedIn"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                      <rect width="4" height="12" x="2" y="9" />
                      <circle cx="4" cy="4" r="2" />
                    </svg>
                  </button>
                  <button
                    type="button"
                    class="bg-white text-blue-900 p-3 rounded-full hover:bg-blue-50 transition-all transform hover:scale-110 shadow-lg"
                    aria-label="Email"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
                      <rect width="20" height="16" x="2" y="4" rx="2" />
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">
                {{ $member['name'] }}
              </h3>
              <div class="text-sm text-blue-600 font-semibold mb-3">
                {{ $member['role'] }}
              </div>
              <p class="text-sm text-gray-600 leading-relaxed mb-4">
                {{ $member['bio'] }}
              </p>
              <div class="border-l-[3px] border-blue-600 pl-3">
                <p class="text-xs text-gray-500 font-medium">
                  {{ $member['expertise'] }}
                </p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
