@extends('layouts.site')

@section('content')
@php
  $post = $post ?? null;
@endphp

<div class="min-h-screen overflow-x-hidden bg-[#f9f8f6] font-sans antialiased">

  {{-- Hero --}}
  <section class="relative overflow-hidden bg-[#0c1a2e] pt-20 max-sm:pt-[4.75rem]">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/[0.06] via-transparent to-white/[0.03]" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
      <div class="absolute -right-[200px] -top-[300px] size-[700px] rounded-full border border-white/5"></div>
      <div class="absolute -bottom-[200px] -left-[100px] size-[400px] rounded-full border border-white/5"></div>
    </div>

    <div class="relative mx-auto max-w-[860px] px-4 pb-0 pt-14 max-sm:px-4 max-sm:pt-9 ps-[max(1rem,env(safe-area-inset-left))] pe-[max(1rem,env(safe-area-inset-right))]">
      <a
        href="{{ route('site.blogs') }}"
        class="inline-flex items-center gap-1.5 text-[13px] font-medium uppercase tracking-wide text-white/45 transition-colors hover:text-white/85"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        News &amp; Media
      </a>

      @if(!empty($post['category']))
        <div class="mt-8 max-sm:mt-5">
          <span class="inline-block rounded-full border border-white/12 bg-white/[0.08] px-3.5 py-1.5 text-[11px] font-medium uppercase tracking-[0.12em] text-[#93bdf7]">
            {{ $post['category'] }}
          </span>
        </div>
      @endif

      <h1 class="mt-5 font-serif text-[clamp(2rem,5vw,3.25rem)] font-bold leading-[1.18] tracking-tight text-white max-sm:mt-4 max-sm:text-[clamp(1.375rem,calc(1.1rem+2.8vw),2.125rem)]">
        {{ $post['title'] ?? '' }}
      </h1>

      <div class="mt-7 flex flex-wrap items-center gap-6 pb-10 max-sm:mt-5 max-sm:gap-x-4 max-sm:gap-y-1.5 max-sm:pb-7">
        @if(!empty($post['author']))
          <span class="flex items-center gap-1.5 text-[13px] text-white/40 max-sm:text-xs">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ $post['author'] }}
          </span>
          <span class="hidden h-[3px] w-[3px] rounded-full bg-white/20 min-[521px]:block" aria-hidden="true"></span>
        @endif

        @if(!empty($post['date']))
          <span class="flex items-center gap-1.5 text-[13px] text-white/40 max-sm:text-xs">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            {{ $post['date'] }}
          </span>
          <span class="hidden h-[3px] w-[3px] rounded-full bg-white/20 min-[521px]:block" aria-hidden="true"></span>
        @endif

        @if(!empty($post['readTime']))
          <span class="flex items-center gap-1.5 text-[13px] text-white/40 max-sm:text-xs">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $post['readTime'] }}
          </span>
        @endif
      </div>
    </div>
  </section>

  {{-- Featured image --}}
  @if(!empty($post['image']))
    <div class="mx-auto max-w-[860px] px-4 pt-8 ps-[max(1rem,env(safe-area-inset-left))] pe-[max(1rem,env(safe-area-inset-right))] max-sm:px-4 max-sm:pt-5">
      <div class="aspect-[16/7] overflow-hidden rounded-[20px] bg-[#dde3ee] shadow-[0_32px_80px_rgba(0,0,0,0.18),0_0_0_1px_rgba(0,0,0,0.06)] max-sm:aspect-[4/3] max-sm:rounded-xl max-sm:shadow-[0_16px_40px_rgba(0,0,0,0.12),0_0_0_1px_rgba(0,0,0,0.05)]">
        <img src="{{ $post['image'] }}" alt="{{ $post['title'] ?? '' }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
      </div>
    </div>
  @endif

  {{-- Article --}}
  <div class="mx-auto max-w-[860px] px-4 pb-24 pt-9 ps-[max(1rem,env(safe-area-inset-left))] pe-[max(1rem,env(safe-area-inset-right))] max-[900px]:px-5 max-[900px]:pb-[4.5rem] max-[900px]:pt-7 max-[480px]:px-4 max-[480px]:pb-14 max-[480px]:pt-6">
    <article class="min-w-0">

      @if(!empty($post['excerpt']))
        <p class="mb-7 border-b border-[#e5e2db] pb-7 text-[1.175rem] leading-[1.75] text-[#2a3244] [overflow-wrap:anywhere] max-[480px]:mb-5 max-[480px]:pb-5 max-[480px]:text-[1.05rem]">
          {{ $post['excerpt'] }}
        </p>
      @endif

      @php
        $blocks = is_array($post['content_blocks'] ?? null)
          ? ($post['content_blocks'] ?? [])
          : [];
      @endphp

      @if(count($blocks) > 0)
        <div class="space-y-6">
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
                <p class="text-[1.05rem] leading-[1.85] text-gray-600 [overflow-wrap:anywhere] whitespace-pre-line max-[480px]:text-base">
                  {{ $text }}
                </p>
              @endif
            @elseif($type === 'quote')
              <div class="rounded-r-xl border-l-[3px] border-blue-600 bg-[#eef3fc] px-8 py-7 max-[480px]:px-5 max-[480px]:py-5">
                <p class="m-0 font-serif text-xl italic leading-relaxed text-[#1e3a6e] max-[480px]:text-[1.1rem] whitespace-pre-line">
                  “{{ $data['text'] ?? '' }}”
                </p>
                @if(!empty($data['attribution']))
                  <p class="mt-4 text-sm font-medium text-[#1e3a6e]/80">— {{ $data['attribution'] }}</p>
                @endif
              </div>
            @endif
          @endforeach
        </div>
      @elseif(!empty($post['content']))
        <div class="[&>p]:mb-[1.4rem] [&>p]:text-[1.05rem] [&>p]:leading-[1.85] [&>p]:text-gray-600 [&>p]:[overflow-wrap:anywhere] max-[480px]:[&>p]:text-base whitespace-pre-line">
          {{ $post['content'] }}
        </div>
      @endif

      <div class="my-9 flex items-center gap-3" aria-hidden="true">
        <span class="h-px flex-1 bg-[#e5e2db]"></span>
        <span class="size-1.5 shrink-0 rounded-full bg-slate-300"></span>
        <span class="size-1.5 shrink-0 rounded-full bg-slate-300"></span>
        <span class="size-1.5 shrink-0 rounded-full bg-slate-300"></span>
        <span class="h-px flex-1 bg-[#e5e2db]"></span>
      </div>

    </article>
  </div>

  {{-- Footer actions --}}
  <div class="mx-auto flex max-w-[860px] flex-wrap items-center justify-between gap-4 border-t border-[#e5e2db] px-8 py-8 pb-16 ps-[max(1rem,env(safe-area-inset-left))] pe-[max(1rem,env(safe-area-inset-right))] max-sm:flex-col max-sm:items-stretch max-sm:gap-4 max-sm:px-4 max-sm:py-6 max-sm:pb-10">
    <a
      href="{{ route('site.blogs') }}"
      class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 transition-[gap] duration-200 hover:gap-3 max-sm:justify-center max-sm:py-2"
    >
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
      All articles
    </a>
    <a
      href="{{ route('site.contact') }}"
      class="inline-flex items-center gap-2 rounded-full bg-[#1e3a6e] px-7 py-3 text-sm font-medium text-white transition hover:-translate-y-px hover:bg-[#162d56] max-sm:min-h-11 max-sm:w-full max-sm:justify-center"
    >
      Contact Us
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
  </div>

</div>
@endsection
