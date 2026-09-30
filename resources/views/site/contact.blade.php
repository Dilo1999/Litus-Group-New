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
  {{-- ============================== HERO ============================== --}}
  <section class="ui-page-hero pb-40 md:pb-60">
    <div class="absolute inset-0 -z-20">
      @if(filled($heroImageUrl))
        <img
          src="{{ $heroImageUrl }}"
          alt="Contact hero"
          class="h-full w-full object-cover"
          style="object-position: 50% {{ $heroPosY }}%;"
          fetchpriority="high"
          decoding="async"
        />
      @endif
      <div class="absolute inset-0 bg-ink-950/45"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-ink-950/90 via-ink-950/60 to-ink-950/20"></div>
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink-950 via-ink-950/60 to-transparent"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-ink-950/60 to-transparent"></div>
    </div>
    <div class="ui-page-hero__glow"></div>

    <div class="ui-container">
      <div class="site-blogs-hero max-w-3xl">
        <span class="ui-eyebrow ui-eyebrow--light mb-6">Contact</span>
        <h1 class="ui-h1 text-white">
          Contact <span class="ui-accent text-lagoon-300">us</span>
        </h1>
        <p class="mt-6 max-w-2xl text-base leading-relaxed text-ink-300 sm:text-lg md:mt-8 md:text-xl">
          Reach out to LITUS Group - our team is ready to help
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-2.5 md:mt-10">
          <a href="tel:+9603322288" class="ui-chip ui-chip--dark min-h-11 px-4 py-2 text-sm transition-colors hover:border-white/35 hover:bg-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lagoon-300" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" /></svg>
            <span class="tabular-nums">+960 332 2288</span>
          </a>
          <a href="mailto:info@litusgroup.com" class="ui-chip ui-chip--dark min-h-11 px-4 py-2 text-sm transition-colors hover:border-white/35 hover:bg-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lagoon-300" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /></svg>
            info@litusgroup.com
          </a>
        </div>
      </div>
    </div>
  </section>

  {{-- Contact.tsx: useInView(once, margin -100px) gates header + columns; map uses separate useInView --}}
  <section
    id="contact"
    class="relative flow-root bg-sand-100 pb-16 md:pb-24"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="contactInView = true"
  >
    <div class="ui-container">
      <div class="relative z-10 -mt-28 grid grid-cols-1 overflow-hidden rounded-[2rem] bg-white shadow-[0_2px_4px_rgba(6,22,52,0.04),0_40px_80px_-30px_rgba(6,22,52,0.35)] ring-1 ring-ink-900/5 md:-mt-44 lg:grid-cols-12">

        {{-- Contact form: first on mobile, right column on desktop --}}
        <div class="order-1 p-6 sm:p-10 lg:order-2 lg:col-span-7 lg:p-14">
          <div
            class="site-contact-header mb-8 translate-y-[50px] opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:mb-10 md:will-change-[opacity,transform]"
            :class="contactInView ? '!translate-y-0 !opacity-100' : ''"
          >
            <span class="ui-eyebrow mb-4">Send a message</span>
            <h2 class="ui-h2 !text-[2rem] sm:!text-4xl lg:!text-[2.6rem]">Get in <span class="ui-accent text-brand-600">touch</span></h2>
            <p class="mt-4 max-w-xl text-base leading-relaxed text-ink-500 sm:text-lg">
              Get in touch with us to learn more about our services and how we can help you
            </p>
          </div>

          <div
            class="site-contact-form translate-y-[30px] opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform]"
            style="transition-delay: 400ms"
            :class="contactInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
          >
            <x-contact-form :companies="$companies" />
          </div>
        </div>

        {{-- Contact Information (dark ink panel) --}}
        <div class="ui-bg-ink order-2 p-6 sm:p-10 lg:order-1 lg:col-span-5 lg:p-12">
          <div class="ui-grid-texture--light pointer-events-none absolute inset-0 -z-10 opacity-60 [mask-image:linear-gradient(to_bottom,#000,transparent_75%)]" aria-hidden="true"></div>
          <div
            class="site-contact-left flex h-full translate-y-[30px] flex-col opacity-0 transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:-translate-x-[50px] md:translate-y-0 md:will-change-[opacity,transform]"
            style="transition-delay: 200ms"
            :class="contactInView ? '!translate-x-0 !translate-y-0 !opacity-100' : ''"
          >
            <span class="ui-eyebrow ui-eyebrow--light mb-4">Get in touch</span>
            <h3 class="ui-h3 !text-white">We're here to <span class="ui-accent text-lagoon-300">help</span></h3>
            <p class="mt-4 text-sm leading-relaxed text-ink-300 sm:text-base">
              Whether you're interested in our services, looking for partnership
              opportunities, or have questions about our companies, we're here to help.
            </p>

            <ul class="mt-8 space-y-3">
              <li>
                <a href="tel:+9603322288" class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4 transition-all duration-300 hover:border-white/25 hover:bg-white/[0.1]">
                  <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-lagoon-400/15 text-lagoon-300 ring-1 ring-lagoon-300/25 transition-transform duration-300 group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                  </span>
                  <span class="min-w-0 flex-1">
                    <span class="block text-[0.7rem] font-bold uppercase tracking-[0.18em] text-ink-400">Phone</span>
                    <span class="mt-0.5 block text-base font-semibold tabular-nums text-white">+960 332 2288</span>
                  </span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40 transition-all duration-300 group-hover:translate-x-1 group-hover:text-lagoon-300" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                </a>
              </li>
              <li>
                <a href="mailto:info@litusgroup.com" class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4 transition-all duration-300 hover:border-white/25 hover:bg-white/[0.1]">
                  <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-lagoon-400/15 text-lagoon-300 ring-1 ring-lagoon-300/25 transition-transform duration-300 group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <rect width="20" height="16" x="2" y="4" rx="2" />
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                  </span>
                  <span class="min-w-0 flex-1">
                    <span class="block text-[0.7rem] font-bold uppercase tracking-[0.18em] text-ink-400">Email</span>
                    <span class="mt-0.5 block break-all text-base font-semibold text-white">info@litusgroup.com</span>
                  </span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-white/40 transition-all duration-300 group-hover:translate-x-1 group-hover:text-lagoon-300" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                </a>
              </li>
              <li>
                <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4">
                  <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-lagoon-400/15 text-lagoon-300 ring-1 ring-lagoon-300/25">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                      <circle cx="12" cy="10" r="3" />
                    </svg>
                  </span>
                  <span class="min-w-0 flex-1">
                    <span class="block text-[0.7rem] font-bold uppercase tracking-[0.18em] text-ink-400">Office</span>
                    <span class="mt-0.5 block text-base font-semibold text-white">Male', Republic of Maldives</span>
                  </span>
                </div>
              </li>
            </ul>

            <div class="mt-8 rounded-2xl border border-white/10 bg-ink-950/40 p-5 sm:p-6 lg:mt-auto">
              <div class="mb-4 flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lagoon-300" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <h4 class="text-sm font-bold uppercase tracking-[0.16em] text-white">Office Hours</h4>
              </div>
              <dl class="divide-y divide-white/10 text-sm sm:text-[0.95rem]">
                <div class="flex items-center justify-between gap-4 py-2.5 first:pt-0">
                  <dt class="text-ink-300">Sunday - Thursday</dt>
                  <dd class="shrink-0 font-semibold tabular-nums text-white">8:00 AM - 5:00 PM</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2.5">
                  <dt class="text-ink-300">Saturday</dt>
                  <dd class="shrink-0 font-semibold tabular-nums text-white">9:00 AM - 1:00 PM</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2.5 last:pb-0">
                  <dt class="text-ink-300">Friday</dt>
                  <dd class="shrink-0"><span class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-semibold text-ink-200">Closed</span></dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Google Maps (second useInView in Contact.tsx) --}}
  <section
    class="site-contact-map bg-sand-100 pb-20 opacity-0 transition-opacity duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] max-md:will-change-auto md:pb-28 md:will-change-opacity"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="mapInView = true"
    :class="mapInView ? '!opacity-100' : ''"
  >
    <div class="ui-container">
      <div class="relative isolate h-[440px] overflow-hidden rounded-[2rem] bg-gradient-to-br from-ink-100 via-brand-50 to-lagoon-100 shadow-[0_2px_4px_rgba(6,22,52,0.04),0_30px_60px_-24px_rgba(6,22,52,0.3)] ring-1 ring-ink-900/5 sm:h-[460px] md:h-[540px]">
        <div class="ui-grid-texture absolute inset-0 -z-10" aria-hidden="true"></div>
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
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-56 bg-gradient-to-t from-ink-950/45 via-ink-950/10 to-transparent"></div>
        <div class="pointer-events-none absolute inset-x-4 bottom-4 sm:inset-x-6 sm:bottom-6 md:right-auto md:bottom-8 md:left-8 md:w-[26rem]">
          <div class="pointer-events-auto rounded-3xl border border-white/15 bg-ink-900/85 p-5 text-white shadow-[0_20px_50px_-20px_rgba(3,11,31,0.7)] backdrop-blur-xl sm:p-6">
            <span class="ui-eyebrow ui-eyebrow--light mb-3">Location</span>
            <h3 class="text-xl font-bold tracking-[-0.015em] text-white sm:text-2xl">
              Visit Our <span class="ui-accent text-lagoon-300">Office</span>
            </h3>
            <p class="mt-1.5 text-sm text-ink-300">Male', Republic of Maldives</p>
            <a
              href="https://maps.app.goo.gl/4ATBypfyR4cKs5Dj7"
              target="_blank"
              rel="noopener noreferrer"
              class="ui-btn ui-btn--light ui-btn--sm mt-5 min-h-11"
              aria-label="Open location in Google Maps"
            >
              <span>Open in Google Maps</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
