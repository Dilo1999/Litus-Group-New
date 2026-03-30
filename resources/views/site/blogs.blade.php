@extends('layouts.site')

@section('content')
{{-- src/app/pages/BlogsPage.tsx — hero, filter/search, featured + grid, pagination, gallery --}}
<div
  class="min-h-screen bg-white pt-20"
  data-blogs-page
  x-data="blogsPage(@js($blogPosts), @js($blogCategories))"
>
  {{-- Hero — motion on load (y 30, 0.8s) --}}
  <section class="relative pb-20 pt-32 overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900">
    <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
      <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNnptMCAxMmMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNnoiIHN0cm9rZT0iI0ZGRiIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==')] opacity-20"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <div class="site-blogs-hero">
        <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">Our Blogs</h1>
        <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto">
          Insights, updates, and stories from across the LITUS Group ecosystem
        </p>
      </div>
    </div>
  </section>

  {{-- Filter & Search — FilterSection useInView once --}}
  <section
    class="py-12 bg-white border-b border-gray-200"
    x-intersect.once="filterInView = true"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-blogs-filter-search mb-8 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
        :class="filterInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <div class="relative max-w-xl mx-auto">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
          <input
            type="search"
            placeholder="Search blogs..."
            class="w-full pl-12 pr-4 py-4 rounded-full border border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-gray-900 placeholder-gray-500"
            :value="searchQuery"
            @input="setSearch($event)"
          />
        </div>
      </div>

      <div
        class="site-blogs-filter-cats flex flex-wrap justify-center gap-3 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
        style="transition-delay: 200ms"
        :class="filterInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <template x-for="category in categories" :key="category">
          <button
            type="button"
            class="px-6 py-2 rounded-full font-semibold transition-all"
            :class="selectedCategory === category ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            @click="selectCategory(category)"
            x-text="category"
          ></button>
        </template>
      </div>
    </div>
  </section>

  {{-- Blog posts --}}
  <section class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div x-show="filteredPosts.length === 0" x-cloak class="text-center py-16">
        <p class="text-xl text-gray-600">No blog posts found matching your criteria.</p>
      </div>

      <div x-show="filteredPosts.length > 0" x-cloak>
        {{-- Featured --}}
        <div
          class="site-blogs-featured mb-16 opacity-0 translate-y-[30px] transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="featuredInView = true"
          :class="featuredInView ? '!opacity-100 !translate-y-0' : ''"
        >
          <template x-if="featuredPost">
            <div>
              <div class="bg-white rounded-3xl overflow-hidden shadow-2xl hover:shadow-2xl transition-all group cursor-pointer">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                  <div class="relative overflow-hidden aspect-[4/3] lg:aspect-auto">
                    <img :src="featuredPost.image" :alt="featuredPost.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                    <div class="absolute top-6 left-6">
                      <div class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">Featured</div>
                    </div>
                  </div>
                  <div class="p-8 lg:p-12 flex flex-col justify-center">
                    <div class="inline-block bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-semibold mb-4 w-fit" x-text="featuredPost.category"></div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors" x-text="featuredPost.title"></h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed" x-text="featuredPost.excerpt"></p>
                    <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-500">
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                        <span x-text="featuredPost.author"></span>
                      </div>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0" aria-hidden="true"><path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /></svg>
                        <span x-text="featuredPost.date"></span>
                      </div>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                        <span x-text="featuredPost.readTime"></span>
                      </div>
                    </div>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full font-semibold transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2 w-fit">
                      Read Full Article
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14" /><path d="m12 5 7 7-7 7" /></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        {{-- Grid --}}
        <div
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="gridInView = true"
          x-show="regularPosts.length > 0"
        >
          <template x-for="(post, index) in regularPosts" :key="post.id">
            <div
              class="site-blogs-card opacity-0 translate-y-[30px] transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
              :style="'transition-delay: ' + (index * 100) + 'ms'"
              :class="gridInView ? '!opacity-100 !translate-y-0' : ''"
            >
              <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all group cursor-pointer h-full flex flex-col">
                <div class="relative overflow-hidden aspect-[16/10]">
                  <img :src="post.image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div class="p-6 flex flex-col flex-grow">
                  <div class="inline-block bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-semibold mb-3 w-fit" x-text="post.category"></div>
                  <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2" x-text="post.title"></h3>
                  <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3 flex-grow" x-text="post.excerpt"></p>
                  <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-3 mb-3 text-sm text-gray-500">
                      <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                        <span class="text-xs" x-text="post.author"></span>
                      </div>
                      <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                        <span class="text-xs" x-text="post.readTime"></span>
                      </div>
                    </div>
                    <div class="text-sm text-gray-500 flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /></svg>
                      <span class="text-xs" x-text="post.date"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        {{-- Pagination --}}
        <div
          x-show="totalPages > 1"
          class="site-blogs-pagination flex justify-center items-center gap-2 mt-16 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
          x-intersect.once="pagInView = true"
          :class="pagInView ? '!opacity-100 !translate-y-0' : ''"
        >
          <button
            type="button"
            class="flex items-center justify-center w-10 h-10 rounded-full font-semibold transition-all"
            :class="currentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-600 hover:text-white shadow-md hover:shadow-lg'"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
            aria-label="Previous page"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6" /></svg>
          </button>
          <div class="flex gap-2">
            <template x-for="(page, idx) in pageNumbers()" :key="'p-' + idx + '-' + page">
              <div class="flex items-center justify-center min-w-[2.5rem]">
                <span class="flex items-center justify-center w-10 h-10 text-gray-500" x-show="page === '...'">...</span>
                <button
                  type="button"
                  class="flex items-center justify-center w-10 h-10 rounded-full font-semibold transition-all"
                  x-show="page !== '...'"
                  :class="currentPage === page ? 'bg-blue-600 text-white shadow-lg scale-110' : 'bg-white text-gray-700 hover:bg-blue-100 hover:text-blue-600 shadow-md hover:shadow-lg'"
                  @click="goToPage(page)"
                  x-text="page"
                ></button>
              </div>
            </template>
          </div>
          <button
            type="button"
            class="flex items-center justify-center w-10 h-10 rounded-full font-semibold transition-all"
            :class="currentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-600 hover:text-white shadow-md hover:shadow-lg'"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
            aria-label="Next page"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
          </button>
        </div>
      </div>
    </div>
  </section>

  {{-- Gallery — GallerySection --}}
  <section
    class="py-20 bg-white"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="galleryInView = true"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-blogs-gallery-head mb-12 opacity-0 translate-y-5 transition-[opacity,transform] duration-[600ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
        :class="galleryInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">Gallery</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($galleryEvents as $index => $item)
          <a
            href="{{ route('site.event', ['slug' => $item['slug']]) }}"
            class="site-blogs-gallery-item group block h-full opacity-0 translate-y-[30px] transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="galleryInView ? '!opacity-100 !translate-y-0' : ''"
          >
            <div class="relative aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 mb-4">
              <img
                src="{{ $item['image'] ?? '' }}"
                alt="{{ $item['image_alt'] ?? $item['title'] }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
            </div>
            <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $item['title'] }}</h3>
          </a>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
