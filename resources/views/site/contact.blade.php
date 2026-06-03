@extends('layouts.site')

@php
  use App\Support\SiteData;
  $companies = SiteData::companies();
  $heroImagePath = \App\Models\SiteSetting::getValue('contact.hero.image_path');
  $heroImageUrl = filled($heroImagePath)
    ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImagePath)
    : null;
  $heroPosY = (int) \App\Models\SiteSetting::getValue('contact.hero.position_y', 50);
@endphp

@section('content')
{{-- Matches src/app/pages/ContactPage.tsx + src/app/components/Contact.tsx --}}
<div data-contact-page x-data="contactPage()">
  <section class="relative flex min-h-[min(72svh,520px)] items-center justify-center overflow-hidden md:min-h-[640px]">
    <div class="absolute inset-0 z-0">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Contact hero"
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
        <h1 class="mb-4 text-3xl font-bold text-white max-md:[text-shadow:0_2px_16px_rgba(0,0,0,0.35)] sm:mb-6 sm:text-4xl md:text-6xl md:[text-shadow:none]">Contact Us</h1>
        <p class="mx-auto text-base leading-relaxed text-blue-100 max-md:[text-shadow:0_1px_12px_rgba(0,0,0,0.3)] sm:text-lg md:max-w-3xl md:text-2xl md:[text-shadow:none]">
          Reach out to LITUS Group - our team is ready to help
        </p>
      </div>
    </div>
  </section>

  {{-- Contact.tsx: useInView(once, margin -100px) gates header + columns; map uses separate useInView --}}
  <section
    id="contact"
    class="overflow-x-hidden bg-gray-50 py-14 md:py-24"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="contactInView = true"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="site-contact-header mb-10 translate-y-[50px] text-center opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-16 md:will-change-[opacity,transform]"
        :class="contactInView ? '!translate-y-0 !opacity-100' : ''"
      >
        <h2 class="mb-3 text-2xl font-bold text-gray-900 sm:mb-4 sm:text-3xl md:text-5xl">Get In Touch</h2>
        <p class="mx-auto max-w-2xl text-base text-gray-600 sm:text-lg md:text-xl">
          Get in touch with us to learn more about our services and how we can help you
        </p>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">
        {{-- Contact form first on mobile, right column on desktop --}}
        <div
          class="site-contact-form order-1 translate-y-[30px] opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform] lg:order-2"
          style="transition-delay: 400ms"
          :class="contactInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
        >
          <x-contact-form :companies="$companies" />
        </div>

        {{-- Contact Information --}}
        <div
          class="site-contact-left order-2 translate-y-[30px] space-y-6 opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto sm:space-y-8 md:-translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform] lg:order-1"
          style="transition-delay: 200ms"
          :class="contactInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
        >
          <div class="text-center lg:text-left">
            <h3 class="mb-4 text-xl font-bold text-gray-900 sm:mb-6 sm:text-2xl">
              Get In Touch
            </h3>
            <p class="text-sm leading-relaxed text-gray-600 sm:text-base">
              Whether you're interested in our services, looking for partnership
              opportunities, or have questions about our companies, we're here to help.
            </p>
          </div>

          <div class="space-y-4 sm:space-y-6">
            <div class="flex items-start gap-3 sm:gap-4">
              <div class="shrink-0 rounded-lg bg-blue-100 p-2.5 transition-transform duration-200 ease-out sm:p-3 md:hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
              </div>
              <div class="min-w-0 text-left">
                <div class="mb-0.5 text-sm font-semibold text-gray-900 sm:mb-1 sm:text-base">Phone</div>
                <a
                  href="tel:+9603322288"
                  class="text-sm text-gray-600 transition-colors hover:text-blue-600 sm:text-base"
                >
                  +960 332 2288
                </a>
              </div>
            </div>

            <div class="flex items-start gap-3 sm:gap-4">
              <div class="shrink-0 rounded-lg bg-blue-100 p-2.5 transition-transform duration-200 ease-out sm:p-3 md:hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </div>
              <div class="min-w-0 text-left">
                <div class="mb-0.5 text-sm font-semibold text-gray-900 sm:mb-1 sm:text-base">Email</div>
                <a
                  href="mailto:info@litusgroup.com"
                  class="break-all text-sm text-gray-600 transition-colors hover:text-blue-600 sm:text-base"
                >
                  info@litusgroup.com
                </a>
              </div>
            </div>

            <div class="flex items-start gap-3 sm:gap-4">
              <div class="shrink-0 rounded-lg bg-blue-100 p-2.5 transition-transform duration-200 ease-out sm:p-3 md:hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 sm:h-6 sm:w-6" aria-hidden="true">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              <div class="min-w-0 text-left">
                <div class="mb-0.5 text-sm font-semibold text-gray-900 sm:mb-1 sm:text-base">Office</div>
                <p class="text-sm text-gray-600 sm:text-base">
                  Male', Republic of Maldives
                </p>
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 sm:p-6">
            <h4 class="mb-3 text-center text-sm font-semibold text-gray-900 sm:text-base lg:text-left">Office Hours</h4>
            <div class="space-y-2.5 text-sm text-gray-600 sm:text-base">
              <div class="flex flex-col gap-0.5 sm:flex-row sm:justify-between sm:gap-4">
                <span>Sunday - Thursday</span>
                <span class="font-medium sm:shrink-0">8:00 AM - 5:00 PM</span>
              </div>
              <div class="flex flex-col gap-0.5 sm:flex-row sm:justify-between sm:gap-4">
                <span>Saturday</span>
                <span class="font-medium sm:shrink-0">9:00 AM - 1:00 PM</span>
              </div>
              <div class="flex flex-col gap-0.5 sm:flex-row sm:justify-between sm:gap-4">
                <span>Friday</span>
                <span class="font-medium sm:shrink-0">Closed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Google Maps (second useInView in Contact.tsx) --}}
  <section
    class="site-contact-map relative h-[min(52vw,280px)] w-full opacity-0 transition-opacity duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto sm:h-[360px] md:h-[500px] md:will-change-opacity"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="mapInView = true"
    :class="mapInView ? '!opacity-100' : ''"
  >
    <iframe
      src="https://www.google.com/maps?q=Mal%C3%A9%2C%20Maldives&z=14&output=embed"
      width="100%"
      height="100%"
      style="border:0"
      allowfullscreen
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="LITUS Group Location"
      class="h-full w-full"
    ></iframe>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 sm:p-6 md:p-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h3 class="mb-1.5 text-lg font-bold text-white sm:mb-2 sm:text-xl md:text-3xl">
          Visit Our Office
        </h3>
        <a
          href="https://maps.app.goo.gl/4ATBypfyR4cKs5Dj7"
          target="_blank"
          rel="noopener noreferrer"
          class="pointer-events-auto inline-flex items-center gap-2 text-sm text-gray-200 transition-colors hover:text-white sm:text-base"
          aria-label="Open location in Google Maps"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          <span>Open in Google Maps</span>
        </a>
      </div>
    </div>
  </section>
</div>
@endsection
