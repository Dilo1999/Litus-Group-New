@props([
    'companies' => [],
])

@if (session('status'))
  <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-900 sm:px-5 sm:text-base" role="status">
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
    </span>
    <span class="pt-1 font-medium">{{ session('status') }}</span>
  </div>
  <script>
    window.addEventListener('load', () => {
      const el = document.getElementById('contact-form');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  </script>
@endif

<form
  id="contact-form"
  method="POST"
  action="{{ route('site.contact.submit') }}"
  class="scroll-mt-28"
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
  @csrf
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-x-5 sm:gap-y-6">
    <div class="sm:col-span-2">
      <label for="name" class="ui-label">Full Name <span class="text-brand-600" aria-hidden="true">*</span></label>
      <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name') }}"
        required
        autocomplete="name"
        placeholder="John Doe"
        @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
        class="ui-input @error('name') !border-rose-400 !bg-rose-50/40 focus:!ring-rose-500/15 @enderror"
      />
      @error('name')
        <p id="name-error" class="mt-2 flex items-start gap-1.5 text-sm font-medium text-rose-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-[3px] shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <div>
      <label for="email" class="ui-label">Email Address <span class="text-brand-600" aria-hidden="true">*</span></label>
      <input
        type="email"
        id="email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="email"
        placeholder="john@example.com"
        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
        class="ui-input @error('email') !border-rose-400 !bg-rose-50/40 focus:!ring-rose-500/15 @enderror"
      />
      @error('email')
        <p id="email-error" class="mt-2 flex items-start gap-1.5 text-sm font-medium text-rose-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-[3px] shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <div>
      <label for="phone" class="ui-label">Phone Number <span class="font-normal text-ink-400">(optional)</span></label>
      <input
        type="tel"
        id="phone"
        name="phone"
        value="{{ old('phone') }}"
        autocomplete="tel"
        placeholder="+960 XXX XXXX"
        @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
        class="ui-input tabular-nums @error('phone') !border-rose-400 !bg-rose-50/40 focus:!ring-rose-500/15 @enderror"
      />
      @error('phone')
        <p id="phone-error" class="mt-2 flex items-start gap-1.5 text-sm font-medium text-rose-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-[3px] shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <div class="sm:col-span-2">
      <label for="company" class="ui-label">Which company are you interested in?</label>
      <div class="relative">
        <select
          id="company"
          name="company"
          @error('company') aria-invalid="true" aria-describedby="company-error" @enderror
          class="ui-input cursor-pointer appearance-none pr-12 @error('company') !border-rose-400 !bg-rose-50/40 focus:!ring-rose-500/15 @enderror"
        >
          <option value="">Select a company</option>
          @foreach ($companies as $c)
            <option value="{{ $c['slug'] }}" @selected(old('company') === $c['slug'])>{{ $c['name'] }}</option>
          @endforeach
        </select>
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-ink-400" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
      </div>
      @error('company')
        <p id="company-error" class="mt-2 flex items-start gap-1.5 text-sm font-medium text-rose-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-[3px] shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <div class="sm:col-span-2">
      <label for="message" class="ui-label">Message <span class="text-brand-600" aria-hidden="true">*</span></label>
      <textarea
        id="message"
        name="message"
        required
        rows="5"
        placeholder="Tell us how we can help you..."
        @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
        class="ui-input min-h-[9.5rem] resize-none leading-relaxed @error('message') !border-rose-400 !bg-rose-50/40 focus:!ring-rose-500/15 @enderror"
      >{{ old('message') }}</textarea>
      @error('message')
        <p id="message-error" class="mt-2 flex items-start gap-1.5 text-sm font-medium text-rose-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-[3px] shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <div class="flex flex-col-reverse gap-4 pt-1 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
      <p class="text-xs leading-relaxed text-ink-400 sm:max-w-[16rem]">
        Fields marked <span class="text-brand-600">*</span> are required.
      </p>
      <button
        type="submit"
        :disabled="!canSubmit || submitting"
        :aria-disabled="(!canSubmit || submitting) ? 'true' : 'false'"
        class="ui-btn ui-btn--primary w-full disabled:translate-y-0 disabled:cursor-not-allowed disabled:bg-ink-200 disabled:text-ink-500 disabled:shadow-none sm:w-auto"
      >
        Send Message
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
          <path d="m21.854 2.147-10.94 10.939" />
        </svg>
      </button>
    </div>
  </div>
</form>
