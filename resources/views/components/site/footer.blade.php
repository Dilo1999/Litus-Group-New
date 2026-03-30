@php
  $currentYear = now()->year;
  $footerLinks = [
    'LITUS Companies' => [
      ['name' => 'LITUS Group', 'slug' => 'litus-group'],
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
    'Favala Companies' => [
      ['name' => 'Favala Supply', 'slug' => 'favala-supply'],
      ['name' => 'Favala Hardware', 'slug' => 'favala-hardware'],
      ['name' => 'Favala Paint', 'slug' => 'favala-paint'],
    ],
    'Quick Links' => [
      ['name' => 'About Us', 'route' => 'site.about'],
      ['name' => 'Our Companies', 'route' => 'site.our-companies'],
      ['name' => 'Careers', 'route' => 'site.careers'],
      ['name' => 'Contact Us', 'route' => 'site.contact'],
    ],
  ];
@endphp

<footer class="bg-gray-900 text-gray-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
      <div class="lg:col-span-1">
        <a href="{{ route('site.home') }}" class="inline-block mb-4">
          <x-site.logo variant="light" size="sm" />
        </a>
        <p class="text-gray-400 mb-6 leading-relaxed">
          Building excellence across diverse industries in the Maldives.
        </p>
        <div class="flex gap-4">
          <a
            href="#"
            class="bg-gray-800 hover:bg-blue-600 p-2.5 rounded-lg transition-colors text-gray-300 hover:text-white"
            aria-label="LinkedIn"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
              <rect width="4" height="12" x="2" y="9"></rect>
              <circle cx="4" cy="4" r="2"></circle>
            </svg>
          </a>
          <a
            href="#"
            class="bg-gray-800 hover:bg-blue-600 p-2.5 rounded-lg transition-colors text-gray-300 hover:text-white"
            aria-label="Twitter"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
              <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
            </svg>
          </a>
          <a
            href="#"
            class="bg-gray-800 hover:bg-blue-600 p-2.5 rounded-lg transition-colors text-gray-300 hover:text-white"
            aria-label="Facebook"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
          <a
            href="mailto:info@litusgroup.com"
            class="bg-gray-800 hover:bg-blue-600 p-2.5 rounded-lg transition-colors text-gray-300 hover:text-white"
            aria-label="Email"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block" aria-hidden="true">
              <rect width="20" height="16" x="2" y="4" rx="2"></rect>
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
            </svg>
          </a>
        </div>
      </div>

      @foreach($footerLinks as $category => $links)
        <div>
          <h3 class="text-white font-semibold mb-4">{{ $category }}</h3>
          <ul class="space-y-3">
            @foreach($links as $link)
              <li>
                @if(!empty($link['slug']))
                  <a
                    href="{{ route('site.company', ['slug' => $link['slug']]) }}"
                    class="text-gray-400 hover:text-blue-500 transition-colors text-sm"
                  >
                    {{ $link['name'] }}
                  </a>
                @else
                  <a
                    href="{{ route($link['route']) }}"
                    class="text-gray-400 hover:text-blue-500 transition-colors text-sm"
                  >
                    {{ $link['name'] }}
                  </a>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>

    <div class="border-t border-gray-800 pt-8">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-gray-400 text-sm">
          © {{ $currentYear }} LITUS Group. All rights reserved | Developed by LITUS IT.
        </p>
        <div class="flex gap-6 text-sm text-gray-400">
          <a href="#" class="hover:text-blue-500 transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-blue-500 transition-colors">Terms of Service</a>
        </div>
      </div>
    </div>
  </div>
</footer>

