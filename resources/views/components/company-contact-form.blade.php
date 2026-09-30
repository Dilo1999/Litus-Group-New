@props([
    'companyName' => '',
    'companyId' => null,
])

@if (session('status'))
  <div class="mb-6 flex items-start gap-3 rounded-2xl border border-lagoon-300/60 bg-lagoon-100/60 px-5 py-4 text-ink-800" role="status">
    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-lagoon-500 text-white">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
    </span>
    <span class="text-sm font-medium leading-relaxed sm:text-[0.95rem]">{{ session('status') }}</span>
  </div>
  <script>
    window.addEventListener('load', () => {
      const el = document.getElementById('company-contact-form');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  </script>
@endif

<form
  id="company-contact-form"
  method="POST"
  action="{{ route('site.contact.submit') }}"
  class="relative scroll-mt-28 overflow-hidden rounded-[2rem] border border-ink-100 bg-white p-5 shadow-[0_2px_4px_rgba(6,22,52,0.04),0_40px_80px_-40px_rgba(6,22,52,0.3)] sm:p-8 lg:p-10"
  x-data="{
    canSubmit: false,
    submitting: false,
    update() {
      this.canSubmit = this.$el.checkValidity();
    },
    init() {
      queueMicrotask(() => this.update());
    }
  }"
  @input.debounce.50ms="update()"
  @change.debounce.50ms="update()"
  @submit="submitting = true; update()"
>
  <span class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-600 via-brand-400 to-lagoon-400" aria-hidden="true"></span>
  @csrf
  <input type="hidden" name="company" value="{{ $companyName }}" />
  @if(! empty($companyId))
    <input type="hidden" name="company_id" value="{{ $companyId }}" />
  @endif

  <div class="mb-6 sm:mb-8">
    <h3 class="ui-h3">Send us a message</h3>
  </div>

  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
      <label for="company-contact-name" class="ui-label">Full Name</label>
      <input
        type="text"
        id="company-contact-name"
        name="name"
        value="{{ old('name') }}"
        required
        autocomplete="name"
        class="ui-input"
      />
      @error('name')
        <div class="mt-2 text-sm font-medium text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="company-contact-email" class="ui-label">Email Address</label>
      <input
        type="email"
        id="company-contact-email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="email"
        class="ui-input"
      />
      @error('email')
        <div class="mt-2 text-sm font-medium text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div class="sm:col-span-2">
      <label for="company-contact-phone" class="ui-label">Phone Number <span class="font-normal text-ink-400">(optional)</span></label>
      <input
        type="tel"
        id="company-contact-phone"
        name="phone"
        value="{{ old('phone') }}"
        autocomplete="tel"
        class="ui-input"
      />
      @error('phone')
        <div class="mt-2 text-sm font-medium text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div class="sm:col-span-2">
      <label for="company-contact-message" class="ui-label">Message</label>
      <textarea
        id="company-contact-message"
        name="message"
        required
        rows="5"
        class="ui-input resize-none"
      >{{ old('message') }}</textarea>
      @error('message')
        <div class="mt-2 text-sm font-medium text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div class="sm:col-span-2">
      <button
        type="submit"
        :disabled="!canSubmit || submitting"
        :aria-disabled="(!canSubmit || submitting) ? 'true' : 'false'"
        class="ui-btn ui-btn--primary w-full disabled:cursor-not-allowed disabled:bg-brand-600/45 disabled:shadow-none disabled:hover:translate-y-0"
      >
        Send Message
        <x-site.lucide-icon name="send" class="h-5 w-5 text-white" />
      </button>
    </div>
  </div>
</form>
