@extends('layouts.site')

@section('content')
@php
  $company = $company ?? null;
  $heroLogo = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
@endphp

{{-- Matches src/app/pages/CompanyPage.tsx --}}
<div data-company-detail>
  {{-- CompanyHero — initial opacity 0 y 30 → animate 0.8s on load --}}
  <section class="relative pt-32 pb-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-20 right-10 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 left-10 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="site-company-hero text-center text-white">
        <div class="flex items-center justify-center mx-auto mb-6 h-20">
          @if($heroLogo)
            <img
              src="{{ $heroLogo }}"
              alt="{{ $company['name'] }}"
              class="h-full w-auto max-w-[min(100%,320px)] object-contain brightness-0 invert scale-[1.618]"
            />
          @endif
        </div>

        <h1 class="text-5xl md:text-6xl font-bold mb-4">{{ $company['name'] }}</h1>
        <p class="text-2xl text-blue-100 mb-8">{{ $company['tagline'] }}</p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="tel:{{ $company['hotline'] }}"
            class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-100 text-blue-900 px-8 py-4 rounded-full text-lg font-semibold transition-all shadow-lg hover:shadow-xl"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            {{ $company['hotline'] }}
          </a>
          @if(!empty($company['email']))
            <a
              href="mailto:{{ $company['email'] }}"
              class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-full text-lg font-semibold transition-all border border-white/30"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                <rect width="20" height="16" x="2" y="4" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
              Email Us
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- AboutCompany — useInView(ref on left in TS); Alpine: intersect on grid + x-data (same node) + init viewport check so reveal always runs --}}
  <section class="overflow-x-clip bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2"
        x-data="{
          aboutInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
              this.aboutInView = true;
              return;
            }
            queueMicrotask(() => {
              const el = this.$el;
              if (!(el instanceof Element)) return;
              const r = el.getBoundingClientRect();
              const vh = window.innerHeight || document.documentElement.clientHeight;
              if (r.top < vh && r.bottom > 0) {
                this.aboutInView = true;
              }
            });
          },
        }"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="aboutInView = true"
      >
        <div
          class="site-company-motion-about-left will-change-[opacity,transform] transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
          :class="aboutInView ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[50px]'"
        >
          <div class="inline-block px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-sm font-medium mb-4">
            {{ $company['category'] }}
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            About {{ $company['name'] }}
          </h2>
          <p class="text-lg text-gray-600 mb-6 leading-relaxed">
            {{ $company['description'] }}
          </p>
          <p class="text-lg text-gray-600 leading-relaxed">
            As a proud member of the LITUS Group family, {{ $company['name'] }} brings decades of expertise and a commitment to excellence that sets us apart in the industry. We leverage the strength and resources of our parent organization to deliver exceptional value to our clients.
          </p>
        </div>

        <div
          class="site-company-motion-about-right relative will-change-[opacity,transform] transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)]"
          style="transition-delay: 200ms"
          :class="aboutInView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[50px]'"
        >
          <div class="relative rounded-2xl overflow-hidden shadow-2xl">
            <img
              src="https://images.unsplash.com/photo-1630487656049-6db93a53a7e9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXNpbmVzcyUyMHRlYW0lMjBtZWV0aW5nJTIwb2ZmaWNlfGVufDF8fHx8MTc3MTY3OTUyNnww&ixlib=rb-4.1.0&q=80&w=1080"
              alt="{{ $company['name'] }}"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ServicesSection — ref on header; cards share isInView + stagger --}}
  <section
    class="py-24 bg-gray-50"
    x-data="{
      servicesInView: false,
      init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.servicesInView = true;
      }
    }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-company-motion-services-header text-center mb-16 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="servicesInView = true"
        :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Our Services</h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Comprehensive solutions tailored to meet your needs
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(($company['services'] ?? []) as $index => $service)
          @php
            $svcIcon = \App\Support\CompanyPageIcons::serviceIcon($service);
          @endphp
          <div
            class="site-company-motion-service-card h-full transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="servicesInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
          >
            {{-- Inner card: hover matches motion.div transition-all in CompanyPage.tsx (separate from 0.5s layout tween) --}}
            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-300 hover:shadow-lg">
              <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                <x-site.lucide-icon :name="$svcIcon" class="h-6 w-6 text-blue-600" />
              </div>
              <h3 class="text-lg font-bold text-gray-900">{{ $service }}</h3>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- WhyChoose --}}
  <section
    class="py-24 bg-white"
    x-data="{
      whyInView: false,
      init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.whyInView = true;
      }
    }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-company-motion-why-header text-center mb-16 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
        x-intersect.once.margin.-100px.-100px.-100px.-100px="whyInView = true"
        :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[50px]'"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
          Why Choose {{ $company['name'] }}
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Experience the difference that sets us apart from the competition
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach(($company['strengths'] ?? []) as $index => $strength)
          @php
            $strIcon = \App\Support\CompanyPageIcons::strengthIcon($strength);
          @endphp
          <div
            class="site-company-motion-why-card text-center transition-[opacity,transform] duration-[500ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
            style="transition-delay: {{ $index * 100 }}ms"
            :class="whyInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
          >
            <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <x-site.lucide-icon :name="$strIcon" class="w-8 h-8 text-white" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $strength }}</h3>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ContactSection — ref on left column --}}
  <section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 lg:grid-cols-2 gap-12"
        x-data="{
          contactInView: false,
          init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.contactInView = true;
          }
        }"
      >
        <div
          class="site-company-motion-contact-left transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
          x-intersect.once.margin.-100px.-100px.-100px.-100px="contactInView = true"
          :class="contactInView ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[50px]'"
        >
          <h2 class="text-4xl font-bold text-gray-900 mb-6">Get In Touch</h2>
          <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            Have questions or ready to get started? Contact us today and discover how {{ $company['name'] }} can serve you.
          </p>

          <div class="space-y-6">
            <div class="flex items-start gap-4">
              <div class="bg-blue-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-gray-900 mb-1">Hotline</div>
                <a href="tel:{{ $company['hotline'] }}" class="text-gray-600 hover:text-blue-600 transition-colors text-lg">
                  {{ $company['hotline'] }}
                </a>
              </div>
            </div>

            @if(!empty($company['email']))
              <div class="flex items-start gap-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600" aria-hidden="true">
                    <rect width="20" height="16" x="2" y="4" rx="2" />
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-gray-900 mb-1">Email</div>
                  <a href="mailto:{{ $company['email'] }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                    {{ $company['email'] }}
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>

        <div
          class="site-company-motion-contact-right transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="contactInView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[50px]'"
        >
          @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">
              {{ session('status') }}
            </div>
          @endif

          <form method="POST" action="{{ route('site.contact.submit') }}" class="bg-white p-8 rounded-2xl shadow-lg">
            @csrf
            <input type="hidden" name="company" value="{{ $company['name'] }}" />

            <h3 class="text-2xl font-bold text-gray-900 mb-6">Send Us A Message</h3>

            <div class="space-y-6">
              <div>
                <label for="company-contact-name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input
                  type="text"
                  id="company-contact-name"
                  name="name"
                  value="{{ old('name') }}"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                />
                @error('name')
                  <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                @enderror
              </div>

              <div>
                <label for="company-contact-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input
                  type="email"
                  id="company-contact-email"
                  name="email"
                  value="{{ old('email') }}"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                />
                @error('email')
                  <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                @enderror
              </div>

              <div>
                <label for="company-contact-phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input
                  type="tel"
                  id="company-contact-phone"
                  name="phone"
                  value="{{ old('phone') }}"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                />
                @error('phone')
                  <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                @enderror
              </div>

              <div>
                <label for="company-contact-message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea
                  id="company-contact-message"
                  name="message"
                  required
                  rows="5"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"
                >{{ old('message') }}</textarea>
                @error('message')
                  <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                @enderror
              </div>

              <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
              >
                Send Message
                <x-site.lucide-icon name="send" class="w-5 h-5 text-white" />
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
