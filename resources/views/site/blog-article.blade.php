@extends('layouts.site')

@section('content')
@php
  $post = $post ?? null;

  $relatedPosts = array_slice(array_values(array_filter(
    \App\Support\SiteData::blogPosts(),
    fn ($p) => ($p['slug'] ?? null) !== ($post['slug'] ?? null)
  )), 0, 3);

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $hasImage = !empty($post['image']);
@endphp

<div class="min-h-screen overflow-x-hidden bg-white font-sans antialiased">

  {{-- Hero --}}
  <section class="relative isolate overflow-hidden bg-ink-950 text-white {{ $hasImage ? 'pb-40 sm:pb-52 lg:pb-64' : 'pb-16 md:pb-24' }}">
    <div class="absolute inset-0 -z-10" aria-hidden="true">
      @if($hasImage)
        <img src="{{ $post['image'] }}" alt="" class="h-full w-full scale-110 object-cover opacity-60 blur-[2px]" fetchpriority="high" decoding="async">
      @endif
      <div class="ui-page-hero__glow"></div>
      <div class="absolute inset-0 bg-ink-950/45"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/30"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/70 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/70 to-transparent"></div>
      <div class="ui-grid-texture--light absolute inset-0 opacity-40"></div>
    </div>

    <div class="ui-container pt-32 md:pt-40">
      <div class="site-blogs-hero mx-auto max-w-4xl">
        <a
          href="{{ route('site.blogs') }}"
          class="group inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-white/70 transition-colors hover:text-white"
        >
          <span class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-md transition-all group-hover:-translate-x-0.5 group-hover:bg-white/20">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
          </span>
          News &amp; Media
        </a>

        @if(!empty($post['category']))
          <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-full bg-lagoon-400 px-3 py-1 text-[0.7rem] font-bold uppercase tracking-wider text-ink-950">
              {{ $post['category'] }}
            </span>
          </div>
        @endif

        <h1 class="mt-6 text-[2.1rem] leading-[1.08] font-extrabold tracking-[-0.03em] text-white [overflow-wrap:anywhere] sm:text-5xl md:text-6xl lg:text-[4.25rem]">
          {{ $post['title'] ?? '' }}
        </h1>

        <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-white/10 pt-6 text-sm text-white/65 md:mt-10">
          @if(!empty($post['author']))
            <span class="flex items-center gap-3">
              <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-lagoon-300 ring-1 ring-white/15" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </span>
              <span>
                <span class="block text-[0.7rem] font-bold uppercase tracking-[0.18em] text-white/45">Written by</span>
                <span class="font-semibold text-white">{{ $post['author'] }}</span>
              </span>
            </span>
          @endif

          @if(!empty($post['date']))
            <span class="flex items-center gap-2">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-lagoon-300" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
              {{ $post['date'] }}
            </span>
          @endif

          @if(!empty($post['readTime']))
            <span class="flex items-center gap-2">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-lagoon-300" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              {{ $post['readTime'] }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- Featured image --}}
  @if($hasImage)
    <div class="ui-container relative z-10 -mt-32 sm:-mt-44 lg:-mt-56">
      <x-site.motion variant="fade-up" :duration="700" class="mx-auto max-w-5xl">
        <figure class="overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-lagoon-100 shadow-[0_40px_90px_-30px_rgba(3,11,31,0.55)] ring-1 ring-ink-950/5 sm:rounded-[2rem]">
          <div class="aspect-[4/3] sm:aspect-[16/8]">
            <img src="{{ $post['image'] }}" alt="{{ $post['title'] ?? '' }}" class="h-full w-full object-cover" decoding="async">
          </div>
        </figure>
      </x-site.motion>
    </div>
  @endif

  {{-- Article --}}
  <div class="ui-container pt-12 pb-20 md:pt-20 md:pb-28">
    <article class="mx-auto min-w-0 max-w-[70ch]">

      @if(!empty($post['excerpt']))
        <p class="mb-10 text-xl leading-[1.65] font-medium tracking-[-0.01em] text-ink-900 [overflow-wrap:anywhere] sm:text-[1.4rem] md:mb-12">
          {{ $post['excerpt'] }}
        </p>
        <div class="mb-10 flex items-center gap-3 md:mb-12" aria-hidden="true">
          <span class="h-px w-12 bg-brand-600"></span>
          <span class="h-px flex-1 bg-ink-100"></span>
        </div>
      @endif

      @php
        $blocks = is_array($post['content_blocks'] ?? null)
          ? ($post['content_blocks'] ?? [])
          : [];
      @endphp

      @if(count($blocks) > 0)
        <div class="space-y-7">
          @foreach($blocks as $block)
            @php
              $type = $block['type'] ?? null;
              $data = $block['data'] ?? [];
            @endphp

            @if($type === 'paragraph')
              @php
                $text = $data['text'] ?? null;
                if (!is_string($text) || $text === '') {
                    $text = isset($data['html']) && is_string($data['html']) ? strip_tags($data['html']) : '';
                }
              @endphp
              @if($text !== '')
                <p class="text-[1.075rem] leading-[1.9] text-ink-600 [overflow-wrap:anywhere] whitespace-pre-line sm:text-[1.15rem] {{ $loop->first && empty($post['excerpt']) ? 'first-letter:float-left first-letter:mr-3 first-letter:font-serif first-letter:text-[4.5rem] first-letter:leading-[0.85] first-letter:text-brand-600' : '' }}">
                  {{ $text }}
                </p>
              @endif
            @elseif($type === 'quote')
              <figure class="relative my-12 overflow-hidden rounded-3xl bg-sand-100 px-7 py-9 sm:px-12 sm:py-12">
                <svg class="absolute top-5 right-6 text-brand-600/10 sm:top-6 sm:right-8" width="96" height="96" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.6 5C6 6.4 3.5 9.6 3.5 13.6V19h6.2v-6.2H6.6c0-2.6 1.4-4.6 3.9-5.6L9.6 5Zm10.9 0c-3.6 1.4-6.1 4.6-6.1 8.6V19h6.2v-6.2h-3.1c0-2.6 1.4-4.6 3.9-5.6L20.5 5Z"/></svg>
                <span class="absolute inset-y-8 left-0 w-1 rounded-r-full bg-gradient-to-b from-brand-600 to-lagoon-400" aria-hidden="true"></span>
                <blockquote class="relative">
                  <p class="m-0 font-serif text-[1.6rem] leading-[1.35] text-ink-900 italic whitespace-pre-line sm:text-[2rem]">
                    “{{ $data['text'] ?? '' }}”
                  </p>
                </blockquote>
                @if(!empty($data['attribution']))
                  <figcaption class="relative mt-6 flex items-center gap-3 text-sm font-semibold text-ink-600">
                    <span class="h-px w-8 bg-ink-300" aria-hidden="true"></span>
                    {{ $data['attribution'] }}
                  </figcaption>
                @endif
              </figure>
            @endif
          @endforeach
        </div>
      @elseif(!empty($post['content']))
        <div class="text-[1.075rem] leading-[1.9] text-ink-600 [overflow-wrap:anywhere] whitespace-pre-line sm:text-[1.15rem]">
          {{ $post['content'] }}
        </div>
      @endif

      {{-- Footer actions --}}
      <div class="mt-16 rounded-3xl border border-ink-100 bg-sand-50 p-6 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-8 md:mt-20">
        <div>
          <p class="text-[0.7rem] font-bold uppercase tracking-[0.2em] text-brand-600">Thanks for reading</p>
          <p class="mt-2 text-lg font-bold tracking-[-0.015em] text-ink-900">Have a question about this story?</p>
        </div>
        <div class="mt-5 flex flex-col gap-3 sm:mt-0 sm:flex-row sm:items-center">
          <a
            href="{{ route('site.blogs') }}"
            class="ui-btn ui-btn--outline ui-btn--sm min-h-11"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            All articles
          </a>
          <a
            href="{{ route('site.contact') }}"
            class="ui-btn ui-btn--primary ui-btn--sm min-h-11"
          >
            Contact Us
            {!! $arrow !!}
          </a>
        </div>
      </div>
    </article>
  </div>

  {{-- Related --}}
  @if(count($relatedPosts) > 0)
    <section class="bg-sand-100 py-20 md:py-28">
      <div class="ui-container">
        <x-site.motion variant="fade-up" class="mb-10 flex flex-col gap-5 md:mb-14 md:flex-row md:items-end md:justify-between">
          <div>
            <span class="ui-eyebrow">Keep reading</span>
            <h2 class="ui-h2 mt-5">More <span class="ui-accent text-brand-600">stories</span></h2>
          </div>
          <a href="{{ route('site.blogs') }}" class="ui-btn ui-btn--outline shrink-0 self-start md:self-auto">
            View all
            {!! $arrow !!}
          </a>
        </x-site.motion>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
          @foreach($relatedPosts as $i => $rp)
            <x-site.motion variant="fade-up" :delay="$i * 100">
              <a href="{{ route('site.blog-article', ['slug' => $rp['slug']]) }}" class="ui-card ui-card--hover group flex h-full flex-col p-3">
                <div class="ui-zoom relative aspect-[16/10] overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-lagoon-100">
                  @if(filled($rp['image'] ?? null))
                    <img src="{{ $rp['image'] }}" alt="{{ $rp['title'] }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                  @endif
                  @if(filled($rp['category'] ?? null))
                    <span class="ui-chip absolute top-3 left-3 !border-white/60 !bg-white/90 !text-ink-900 backdrop-blur-md">{{ $rp['category'] }}</span>
                  @endif
                </div>
                <div class="flex flex-grow flex-col px-3 pt-5 pb-3">
                  <div class="flex items-center gap-2 text-xs font-medium text-ink-400">
                    @if(filled($rp['date'] ?? null))<span>{{ $rp['date'] }}</span>@endif
                    @if(filled($rp['readTime'] ?? null))
                      <span class="h-1 w-1 rounded-full bg-ink-300" aria-hidden="true"></span>
                      <span>{{ $rp['readTime'] }}</span>
                    @endif
                  </div>
                  <h3 class="mt-3 line-clamp-2 text-lg font-bold leading-snug tracking-[-0.015em] text-ink-900 transition-colors group-hover:text-brand-600 sm:text-xl">{{ $rp['title'] }}</h3>
                  @if(filled($rp['excerpt'] ?? null))
                    <p class="mt-3 line-clamp-2 flex-grow text-[0.95rem] leading-relaxed text-ink-500">{{ $rp['excerpt'] }}</p>
                  @endif
                  <span class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-ink-900 transition-all group-hover:gap-3 group-hover:text-brand-600">
                    Read story
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  </span>
                </div>
              </a>
            </x-site.motion>
          @endforeach
        </div>
      </div>
    </section>
  @endif

</div>
@endsection
