@php
  $currentYear = now()->year;
  $groupCompany = \App\Support\SiteData::companyBySlug('litus-group');
  $groupHotline = trim((string) ($groupCompany['hotline'] ?? ''));
  $groupEmail = trim((string) ($groupCompany['email'] ?? '')) ?: 'info@litusgroup.com';

  $footerLinks = [
    'LITUS ' => [
      ['name' => 'LITUS Maldives', 'slug' => 'litus-maldives'],
      ['name' => 'LITUS Shipping', 'slug' => 'litus-shipping'],
      ['name' => 'LITUS Automobiles', 'slug' => 'litus-automobiles'],
      ['name' => 'LITUS Connect', 'slug' => 'litus-connect'],
      ['name' => 'LITUS Constructions', 'slug' => 'litus-constructions'],
    ],
    'Zaha & Al Zaha' => [
      ['name' => 'Zaha Residence & Hotels', 'slug' => 'zaha-residence-hotels'],
      ['name' => 'Zaha Travels', 'slug' => 'zaha-travels'],
      ['name' => 'Al Zaha General Trading', 'slug' => 'al-zaha-general-trading'],
    ],
    'Favala ' => [
      ['name' => 'Favala Supply', 'slug' => 'favala-supply'],
      ['name' => 'Favala Hardware', 'slug' => 'favala-hardware'],
      ['name' => 'Favala Paint', 'slug' => 'favala-paint'],
    ],
    'Quick Links' => [
      ['name' => 'About Us', 'route' => 'site.about'],
      ['name' => 'Our Entities', 'route' => 'site.our-companies'],
      ['name' => 'Careers', 'route' => 'site.careers'],
      ['name' => 'Contact Us', 'route' => 'site.contact'],
    ],
  ];

  // Drop links to companies that are switched off (or missing) in the admin.
  $activeSlugs = array_column(\App\Support\SiteData::companies(), 'slug');
  foreach ($footerLinks as $heading => $links) {
    $footerLinks[$heading] = array_values(array_filter(
      $links,
      fn ($link) => empty($link['slug']) || in_array($link['slug'], $activeSlugs, true)
    ));
  }
  $footerLinks = array_filter($footerLinks);

  $socials = [
    ['label' => 'LinkedIn', 'href' => 'https://www.linkedin.com/company/litus-group-maldives/', 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>'],
    ['label' => 'X', 'href' => 'https://x.com/LITUSmv', 'svg' => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" class="fill-current"><path d="M21.742 21.75l-7.563-11.179 7.056-8.321h-2.456l-5.691 6.714-4.54-6.714H2.359l7.29 10.776L2.25 21.75h2.456l6.035-7.118 4.818 7.118h6.191-.008zM7.739 3.818L18.81 20.182h-2.447L5.29 3.818h2.447z"></path></svg>'],
    ['label' => 'Facebook', 'href' => 'https://www.facebook.com/litusgroupmv', 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>'],
    ['label' => 'Email', 'href' => 'mailto:' . $groupEmail, 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>'],
  ];
@endphp

<footer class="ui-bg-ink !bg-ink-950 text-ink-300">
  <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 opacity-60 [mask-image:linear-gradient(to_bottom,black,transparent_70%)]" aria-hidden="true"></div>

  <div class="ui-container pt-20 pb-10 md:pt-24">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-10">
      <div class="lg:col-span-4">
        <a href="{{ route('site.home') }}" class="inline-block" aria-label="LITUS Group — Home">
          <x-site.logo variant="light" size="lg" />
        </a>
        <p class="mt-5 max-w-xs text-[0.95rem] leading-relaxed text-ink-300">
          Building excellence across diverse industries in the Maldives.
        </p>

        <div class="mt-8 space-y-3">
          <a href="mailto:{{ $groupEmail }}" class="group flex items-center gap-3 text-white transition-colors hover:text-lagoon-300">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/[0.06] ring-1 ring-white/10 transition-colors group-hover:bg-lagoon-500/15">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
            </span>
            <span class="font-semibold">{{ $groupEmail }}</span>
          </a>
          @if($groupHotline !== '')
            <a href="tel:{{ preg_replace('/\s+/', '', $groupHotline) }}" class="group flex items-center gap-3 text-white transition-colors hover:text-lagoon-300">
              <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/[0.06] ring-1 ring-white/10 transition-colors group-hover:bg-lagoon-500/15">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              </span>
              <span class="font-semibold tabular-nums">{{ $groupHotline }}</span>
            </a>
          @endif
        </div>

        <div class="mt-8 flex flex-wrap gap-2.5">
          @foreach($socials as $social)
            <a
              href="{{ $social['href'] }}"
              @if(!str_starts_with($social['href'], 'mailto:')) target="_blank" rel="noopener noreferrer" @endif
              class="ui-icon-btn bg-white/[0.06] text-ink-200 ring-1 ring-white/10 hover:-translate-y-0.5 hover:bg-white hover:text-ink-900"
              aria-label="{{ $social['label'] }}"
            >{!! $social['svg'] !!}</a>
          @endforeach
        </div>
      </div>

      {{-- Desktop / tablet: columns --}}
      <div class="hidden gap-8 md:grid md:grid-cols-4 lg:col-span-8">
        @foreach($footerLinks as $category => $links)
          <div>
            <h3 class="mb-5 text-xs font-bold uppercase tracking-[0.2em] text-white">{{ trim($category) }}</h3>
            <ul class="space-y-3.5">
              @foreach($links as $link)
                <li>
                  <a
                    href="{{ !empty($link['slug']) ? route('site.company', ['slug' => $link['slug']]) : route($link['route']) }}"
                    class="group inline-flex items-center text-[0.9rem] text-ink-300 transition-colors hover:text-white"
                  >
                    <span class="h-px w-0 bg-lagoon-400 transition-all duration-300 group-hover:mr-2 group-hover:w-3"></span>
                    {{ $link['name'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </div>

      {{-- Mobile: accordions --}}
      <div class="space-y-2.5 md:hidden">
        @foreach($footerLinks as $category => $links)
          <details class="group/acc overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03]">
            <summary class="flex cursor-pointer select-none list-none items-center justify-between px-5 py-4 [&::-webkit-details-marker]:hidden">
              <span class="text-sm font-bold uppercase tracking-[0.16em] text-white">{{ trim($category) }}</span>
              <svg class="h-4 w-4 text-ink-300 transition-transform duration-200 group-open/acc:rotate-45" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <path d="M12 5v14M5 12h14" />
              </svg>
            </summary>
            <ul class="space-y-3 px-5 pb-5">
              @foreach($links as $link)
                <li>
                  <a
                    href="{{ !empty($link['slug']) ? route('site.company', ['slug' => $link['slug']]) : route($link['route']) }}"
                    class="block text-[0.95rem] text-ink-300 transition-colors hover:text-white"
                  >
                    {{ $link['name'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          </details>
        @endforeach
      </div>
    </div>

    <div class="pointer-events-none mt-16 select-none overflow-hidden md:mt-20" aria-hidden="true">
      <p class="bg-gradient-to-b from-white/[0.14] to-white/0 bg-clip-text text-center text-[22vw] font-extrabold leading-[0.8] tracking-[-0.06em] text-transparent lg:text-[15.5rem]">
        LITUS
      </p>
    </div>

    <div class="flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-center md:flex-row md:text-left">
      <p class="text-sm text-ink-400">
        © {{ $currentYear }} LITUS Group. All rights reserved <span class="mx-1.5 text-ink-600">|</span> Developed by LITUS IT.
      </p>
      <a href="#main" class="group inline-flex items-center gap-2 text-sm font-semibold text-ink-300 transition-colors hover:text-white">
        Back to top
        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/[0.06] ring-1 ring-white/10 transition-transform duration-300 group-hover:-translate-y-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
        </span>
      </a>
    </div>
  </div>
</footer>
