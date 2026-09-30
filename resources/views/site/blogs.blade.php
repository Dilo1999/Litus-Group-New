@extends('layouts.site')

@section('content')
{{-- src/app/pages/BlogsPage.tsx — hero, filter/search, featured + grid, pagination, gallery --}}
@php
  $heroImagePath = \App\Models\SiteSetting::getValue('blogs.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('blogs.hero.position_y', 50);

  $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
  $arrowUpRight = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>';
@endphp
<div
  class="min-h-screen bg-white"
  data-blogs-page
  x-data="blogsPage(@js($blogPosts), @js($blogCategories))"
>
  {{-- Hero — motion on load (y 30, 0.8s) --}}
  <section class="relative isolate flex min-h-[560px] items-end overflow-hidden bg-ink-950 text-white md:min-h-[640px]">
    <div class="absolute inset-0 -z-10">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="News & Media hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
      @else
        <div class="ui-page-hero__glow"></div>
      @endif
      <div class="absolute inset-0 bg-ink-950/35"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/55 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/60 to-transparent"></div>
    </div>

    <div class="ui-container pt-36 pb-14 md:pt-44 md:pb-20">
      <div class="site-blogs-hero grid grid-cols-1 items-end gap-8 lg:grid-cols-12">
        <div class="lg:col-span-8">
          <span class="ui-eyebrow ui-eyebrow--light">News &amp; Media</span>
          <h1 class="ui-h1 mt-6 text-white">
            Our <span class="ui-accent text-lagoon-300">stories</span>
          </h1>
          <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg md:text-xl">
            Insights, updates, and stories from across the LITUS Group ecosystem
          </p>
        </div>
        <div class="flex flex-wrap gap-2 lg:col-span-4 lg:justify-end">
          <span class="ui-chip ui-chip--dark">
            <span class="h-1.5 w-1.5 rounded-full bg-lagoon-400" aria-hidden="true"></span>
            <span class="tabular-nums" x-text="posts.length"></span>
            <span x-text="posts.length === 1 ? 'article' : 'articles'"></span>
          </span>
          <a href="#gallery" class="ui-chip ui-chip--dark transition-colors hover:bg-white/20">Event gallery</a>
        </div>
      </div>
    </div>
  </section>

  {{-- Filter & Search — FilterSection useInView once --}}
  <section
    class="relative border-b border-ink-100 bg-white"
    x-intersect.once="filterInView = true"
  >
    <div class="ui-container flex flex-col gap-4 py-5 lg:flex-row-reverse lg:items-center lg:justify-between lg:gap-8">
      <div
        class="site-blogs-filter-search w-full opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)] lg:max-w-sm"
        :class="filterInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <label class="relative block">
          <span class="sr-only">Search blogs</span>
          <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ink-400" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
          <input
            type="search"
            placeholder="Search blogs..."
            class="ui-input !rounded-full !py-3 !pl-11 !pr-4"
            :value="searchQuery"
            @input="setSearch($event)"
          />
        </label>
      </div>

      <div
        class="site-blogs-filter-cats -mx-5 -my-2 flex gap-2 overflow-x-auto px-5 py-2 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)] [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:my-0 sm:overflow-visible sm:px-0 sm:py-0 [&::-webkit-scrollbar]:hidden"
        style="transition-delay: 200ms"
        :class="filterInView ? '!opacity-100 !translate-y-0' : ''"
        role="group"
        aria-label="Filter by category"
      >
        <template x-for="category in categories" :key="category">
          <button
            type="button"
            class="inline-flex min-h-11 shrink-0 items-center rounded-full border px-5 text-sm font-semibold whitespace-nowrap transition-all duration-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
            :class="selectedCategory === category ? 'border-ink-900 bg-ink-900 text-white shadow-[0_6px_14px_-8px_rgba(6,22,52,0.55)]' : 'border-ink-200 bg-white text-ink-600 hover:border-ink-400 hover:text-ink-900'"
            :aria-pressed="selectedCategory === category ? 'true' : 'false'"
            @click="selectCategory(category)"
            x-text="category"
          ></button>
        </template>
      </div>
    </div>
  </section>

  {{-- Blog posts --}}
  <section class="bg-sand-100 py-16 md:py-24">
    <div class="ui-container">
      <div x-show="filteredPosts.length === 0" x-cloak class="ui-card mx-auto max-w-xl px-8 py-16 text-center">
        <div class="ui-icon-tile mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
        </div>
        <p class="mt-6 text-lg font-semibold text-ink-900 md:text-xl">No blog posts found matching your criteria.</p>
        <p class="mt-2 text-sm text-ink-500">Try a different keyword or category.</p>
        <button type="button" class="ui-btn ui-btn--outline ui-btn--sm mt-6" @click="selectCategory('All'); searchQuery = ''">Clear filters</button>
      </div>

      <div x-show="filteredPosts.length > 0" x-cloak>
        {{-- Featured --}}
        <div
          class="site-blogs-featured opacity-0 translate-y-[30px] transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="featuredInView = true"
          :class="featuredInView ? '!opacity-100 !translate-y-0' : ''"
        >
          <template x-if="featuredPost">
            <div>
              <a
                class="group relative isolate block min-h-[30rem] overflow-hidden rounded-[2rem] bg-gradient-to-br from-ink-800 via-ink-900 to-ink-950 shadow-[0_30px_80px_-30px_rgba(6,22,52,0.55)] sm:min-h-[34rem] lg:min-h-[38rem]"
                :href="'{{ url('/blogs') }}/' + featuredPost.slug"
              >
                <div class="ui-zoom absolute inset-0 -z-10">
                  <img x-show="featuredPost.image" :src="featuredPost.image" :alt="featuredPost.title" class="h-full w-full object-cover" />
                </div>
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink-950 via-ink-950/55 to-ink-950/5"></div>
                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink-950/70 via-transparent to-transparent"></div>

                <div class="absolute top-5 left-5 sm:top-8 sm:left-8">
                  <span class="inline-flex items-center gap-2 rounded-full bg-lagoon-400 px-3 py-1 text-[0.7rem] font-bold uppercase tracking-wider text-ink-950">
                    <span class="h-1.5 w-1.5 rounded-full bg-ink-950" aria-hidden="true"></span>
                    Featured
                  </span>
                </div>
                <span class="ui-icon-btn absolute top-5 right-5 border border-white/20 bg-white/10 text-white backdrop-blur-md transition-all group-hover:bg-white group-hover:text-ink-900 sm:top-8 sm:right-8" aria-hidden="true">
                  {!! $arrowUpRight !!}
                </span>

                <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10 lg:p-14">
                  <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                    <span class="ui-chip ui-chip--dark" x-text="featuredPost.category"></span>
                    <span class="text-sm text-white/70" x-text="featuredPost.date"></span>
                  </div>
                  <h2 class="mt-5 max-w-4xl text-[1.75rem] font-extrabold leading-[1.1] tracking-[-0.025em] text-white sm:text-4xl lg:text-[3.25rem]" x-text="featuredPost.title"></h2>
                  <p class="mt-5 line-clamp-3 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg" x-text="featuredPost.excerpt"></p>
                  <div class="mt-8 flex flex-col gap-5 border-t border-white/15 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-white/65">
                      <div class="flex items-center gap-2" x-show="featuredPost.author">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                        <span x-text="featuredPost.author"></span>
                      </div>
                      <div class="flex items-center gap-2" x-show="featuredPost.readTime">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                        <span x-text="featuredPost.readTime"></span>
                      </div>
                    </div>
                    <span class="ui-btn ui-btn--light self-start sm:self-auto">
                      Read Full Article
                      {!! $arrow !!}
                    </span>
                  </div>
                </div>
              </a>
            </div>
          </template>
        </div>

        {{-- Grid --}}
        <div x-show="regularPosts.length > 0" class="mt-16 mb-8 flex items-end justify-between gap-4 md:mt-20">
          <div>
            <span class="ui-eyebrow">Latest</span>
            <h2 class="mt-3 text-2xl font-extrabold tracking-[-0.025em] text-ink-900 sm:text-3xl">More <span class="ui-accent text-brand-600">stories</span></h2>
          </div>
          <p class="text-sm text-ink-500 tabular-nums" x-show="totalPages > 1">
            Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
          </p>
        </div>
        <div
          class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="gridInView = true"
          x-show="regularPosts.length > 0"
        >
          <template x-for="(post, index) in regularPosts" :key="post.id">
            <div
              class="site-blogs-card opacity-0 translate-y-[30px] transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
              :style="'transition-delay: ' + (index * 100) + 'ms'"
              :class="gridInView ? '!opacity-100 !translate-y-0' : ''"
            >
              <a
                class="ui-card ui-card--hover group flex h-full flex-col p-3"
                :href="'{{ url('/blogs') }}/' + post.slug"
              >
                <div class="ui-zoom relative aspect-[16/10] overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-lagoon-100">
                  <img x-show="post.image" :src="post.image" :alt="post.title" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                  <span class="ui-chip absolute top-3 left-3 !border-white/60 !bg-white/90 !text-ink-900 backdrop-blur-md" x-text="post.category"></span>
                </div>
                <div class="flex flex-grow flex-col px-3 pt-5 pb-3">
                  <div class="flex items-center gap-2 text-xs font-medium text-ink-400">
                    <span x-text="post.date"></span>
                    <span class="h-1 w-1 rounded-full bg-ink-300" x-show="post.readTime" aria-hidden="true"></span>
                    <span x-text="post.readTime"></span>
                  </div>
                  <h3 class="mt-3 line-clamp-2 text-lg font-bold leading-snug tracking-[-0.015em] text-ink-900 transition-colors group-hover:text-brand-600 sm:text-xl" x-text="post.title"></h3>
                  <p class="mt-3 line-clamp-3 flex-grow text-[0.95rem] leading-relaxed text-ink-500" x-text="post.excerpt"></p>
                  <div class="mt-6 flex items-center justify-between border-t border-ink-100 pt-4">
                    <div class="flex min-w-0 items-center gap-2.5">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-ink-100 text-ink-500" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                      </span>
                      <span class="truncate text-sm font-semibold text-ink-700" x-text="post.author"></span>
                    </div>
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-ink-200 text-ink-900 transition-all duration-300 group-hover:border-brand-600 group-hover:bg-brand-600 group-hover:text-white" aria-hidden="true">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                    </span>
                  </div>
                </div>
              </a>
            </div>
          </template>
        </div>

        {{-- Pagination --}}
        <div
          x-show="totalPages > 1"
          class="site-blogs-pagination mt-14 flex items-center justify-center opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)] md:mt-20"
          x-intersect.once="pagInView = true"
          :class="pagInView ? '!opacity-100 !translate-y-0' : ''"
        >
          <nav class="inline-flex items-center gap-1 rounded-full border border-ink-100 bg-white p-1.5 shadow-[0_8px_24px_-12px_rgba(6,22,52,0.15)]" aria-label="Pagination">
            <button
              type="button"
              class="flex h-11 w-11 items-center justify-center rounded-full transition-all"
              :class="currentPage === 1 ? 'cursor-not-allowed text-ink-300' : 'text-ink-900 hover:bg-ink-100'"
              :disabled="currentPage === 1"
              @click="goToPage(currentPage - 1)"
              aria-label="Previous page"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6" /></svg>
            </button>
            <div class="flex gap-1">
              <template x-for="(page, idx) in pageNumbers()" :key="'p-' + idx + '-' + page">
                <div class="flex items-center justify-center">
                  <span class="flex h-11 w-8 items-center justify-center text-ink-400" x-show="page === '...'">...</span>
                  <button
                    type="button"
                    class="flex h-11 min-w-11 items-center justify-center rounded-full px-3 text-sm font-semibold tabular-nums transition-all"
                    x-show="page !== '...'"
                    :class="currentPage === page ? 'bg-ink-900 text-white shadow-[0_8px_20px_-10px_rgba(6,22,52,0.7)]' : 'text-ink-600 hover:bg-ink-100 hover:text-ink-900'"
                    :aria-current="currentPage === page ? 'page' : null"
                    @click="goToPage(page)"
                    x-text="page"
                  ></button>
                </div>
              </template>
            </div>
            <button
              type="button"
              class="flex h-11 w-11 items-center justify-center rounded-full transition-all"
              :class="currentPage === totalPages ? 'cursor-not-allowed text-ink-300' : 'text-ink-900 hover:bg-ink-100'"
              :disabled="currentPage === totalPages"
              @click="goToPage(currentPage + 1)"
              aria-label="Next page"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </section>

  {{-- Gallery — GallerySection --}}
  <section
    id="gallery"
    class="relative scroll-mt-24 bg-white py-20 md:py-28"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="galleryInView = true"
  >
    @php
      $gCount = count($galleryEvents);
      $gGrid = match (true) {
        $gCount <= 1 => 'grid-cols-1',
        $gCount === 2 => 'grid-cols-1 sm:grid-cols-2',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
      };
    @endphp
    <div class="ui-container">
      <div
        class="site-blogs-gallery-head mb-12 flex flex-col gap-5 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)] md:mb-16 lg:flex-row lg:items-end lg:justify-between"
        :class="galleryInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <div>
          <span class="ui-eyebrow">Events</span>
          <h2 class="ui-h2 mt-5">Gallery <span class="ui-accent text-brand-600">moments</span></h2>
        </div>
        <p class="ui-lead max-w-md">Browse photos from events across the LITUS Group.</p>
      </div>

      @if(count($galleryEvents) === 0)
        <p class="rounded-3xl border border-ink-100 bg-sand-50 px-6 py-12 text-center text-ink-500">No events have been added yet.</p>
      @endif

      <div class="grid gap-4 sm:gap-5 {{ $gGrid }}">
        @foreach ($galleryEvents as $index => $item)
          <a
            href="{{ route('site.event', ['slug' => $item['slug']]) }}"
            class="site-blogs-gallery-item group relative block min-w-0 overflow-hidden rounded-3xl bg-gradient-to-br from-ink-700 via-ink-800 to-ink-950 opacity-0 translate-y-[30px] shadow-[0_20px_50px_-24px_rgba(6,22,52,0.45)] transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] {{ $index === 0 && $gCount > 2 ? 'sm:col-span-2 lg:row-span-2' : '' }}"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="galleryInView ? '!opacity-100 !translate-y-0' : ''"
          >
            <div class="ui-zoom relative h-full {{ $gCount === 1 ? 'aspect-[4/3] sm:aspect-[21/9]' : ($gCount === 2 ? 'aspect-[4/3] lg:aspect-[16/10]' : ($index === 0 ? 'aspect-[4/3] lg:aspect-auto lg:min-h-full' : 'aspect-[4/3]')) }}">
              @if(filled($item['image'] ?? null))
                <img
                  src="{{ $item['image'] }}"
                  alt="{{ $item['image_alt'] ?? $item['title'] }}"
                  class="absolute inset-0 h-full w-full object-cover"
                  loading="lazy"
                  decoding="async"
                />
              @else
                <div class="ui-grid-texture--light absolute inset-0" aria-hidden="true"></div>
                <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-brand-500/30 blur-3xl" aria-hidden="true"></div>
                <div class="absolute inset-0 flex items-center justify-center text-white/25" aria-hidden="true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
              @endif
              <div class="absolute inset-0 bg-gradient-to-t from-ink-950/95 via-ink-950/30 to-transparent"></div>
              <span class="ui-icon-btn absolute top-4 right-4 border border-white/20 bg-white/10 text-white opacity-0 backdrop-blur-md transition-all duration-300 group-hover:opacity-100 max-md:opacity-100" aria-hidden="true">
                {!! $arrowUpRight !!}
              </span>
              <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                @if(filled($item['date'] ?? null))
                  <p class="text-xs font-semibold uppercase tracking-[0.18em] text-lagoon-300">{{ $item['date'] }}</p>
                @endif
                <h3 class="mt-2 text-lg font-bold leading-snug tracking-[-0.015em] text-white sm:text-xl">{{ $item['title'] }}</h3>
                @php $photoCount = count($item['gallery_images'] ?? []); @endphp
                @if($photoCount > 0)
                  <p class="mt-2 text-sm text-white/65 tabular-nums">{{ $photoCount }} {{ \Illuminate\Support\Str::plural('photo', $photoCount) }}</p>
                @endif
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
