@extends('layouts.site')

@php
  use App\Support\SiteData;
  $companies = SiteData::companies();
@endphp

@section('content')
{{-- Matches src/app/pages/ContactPage.tsx + src/app/components/Contact.tsx --}}
<div class="pt-20" data-contact-page x-data="contactPage()">
  {{-- Contact.tsx: useInView(once, margin -100px) gates header + columns; map uses separate useInView --}}
  <section
    id="contact"
    class="py-24 bg-gray-50 overflow-x-hidden"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="contactInView = true"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="site-contact-header text-center mb-16 opacity-0 translate-y-[50px] transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
        :class="contactInView ? '!opacity-100 !translate-y-0' : ''"
      >
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
          Contact Us
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Get in touch with us to learn more about our services and how we can help you
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Contact Information --}}
        <div
          class="site-contact-left space-y-8 opacity-0 -translate-x-[50px] transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
          style="transition-delay: 200ms"
          :class="contactInView ? '!opacity-100 !translate-x-0' : ''"
        >
          <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-6">
              Get In Touch
            </h3>
            <p class="text-gray-600 mb-8 leading-relaxed">
              Whether you're interested in our services, looking for partnership
              opportunities, or have questions about our companies, we're here to help.
            </p>
          </div>

          <div class="space-y-6">
            <div class="flex items-start gap-4">
              <div class="bg-blue-100 p-3 rounded-lg shrink-0 transition-transform duration-200 ease-out hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-gray-900 mb-1">Phone</div>
                <a
                  href="tel:+9603322288"
                  class="text-gray-600 hover:text-blue-600 transition-colors"
                >
                  +960 332 2288
                </a>
              </div>
            </div>

            <div class="flex items-start gap-4">
              <div class="bg-blue-100 p-3 rounded-lg shrink-0 transition-transform duration-200 ease-out hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600" aria-hidden="true">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-gray-900 mb-1">Email</div>
                <a
                  href="mailto:info@litusgroup.com"
                  class="text-gray-600 hover:text-blue-600 transition-colors"
                >
                  info@litusgroup.com
                </a>
              </div>
            </div>

            <div class="flex items-start gap-4">
              <div class="bg-blue-100 p-3 rounded-lg shrink-0 transition-transform duration-200 ease-out hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600" aria-hidden="true">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-gray-900 mb-1">Office</div>
                <p class="text-gray-600">
                  Male', Republic of Maldives
                </p>
              </div>
            </div>
          </div>

          <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
            <h4 class="font-semibold text-gray-900 mb-3">Office Hours</h4>
            <div class="space-y-2 text-gray-600">
              <div class="flex justify-between gap-4">
                <span>Sunday - Thursday</span>
                <span class="font-medium shrink-0">8:00 AM - 5:00 PM</span>
              </div>
              <div class="flex justify-between gap-4">
                <span>Saturday</span>
                <span class="font-medium shrink-0">9:00 AM - 1:00 PM</span>
              </div>
              <div class="flex justify-between gap-4">
                <span>Friday</span>
                <span class="font-medium shrink-0">Closed</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Contact Form --}}
        <div
          class="site-contact-form opacity-0 translate-x-[50px] transition-[opacity,transform] duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[opacity,transform]"
          style="transition-delay: 400ms"
          :class="contactInView ? '!opacity-100 !translate-x-0' : ''"
        >
          <x-contact-form :companies="$companies" />
        </div>
      </div>
    </div>
  </section>

  {{-- Google Maps (second useInView in Contact.tsx) --}}
  <section
    class="site-contact-map relative w-full h-[400px] md:h-[500px] opacity-0 transition-opacity duration-[800ms] ease-[cubic-bezier(0.4,0,0.2,1)] will-change-opacity"
    x-intersect.once.margin.-100px.-100px.-100px.-100px="mapInView = true"
    :class="mapInView ? '!opacity-100' : ''"
  >
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.3011791593975!2d73.50771731477462!3d4.175148547095843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b3f7f5e8f8f8f8f%3A0x8f8f8f8f8f8f8f8f!2sMale%2C%20Maldives!5e0!3m2!1sen!2s!4v1234567890123!5m2!1sen!2s"
      width="100%"
      height="100%"
      style="border:0"
      allowfullscreen
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="LITUS Group Location"
      class="w-full h-full"
    ></iframe>
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-8 pointer-events-none">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
          Visit Our Office
        </h3>
        <p class="text-gray-200 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          Male', Republic of Maldives
        </p>
      </div>
    </div>
  </section>
</div>
@endsection
