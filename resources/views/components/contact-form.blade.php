@props([
    'companies' => [],
])

@if (session('status'))
  <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-800" role="status">
    {{ session('status') }}
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
  class="bg-white p-8 rounded-2xl shadow-lg"
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
  <div class="space-y-6">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
      <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name') }}"
        required
        autocomplete="name"
        placeholder="John Doe"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all @error('name') border-red-500 @enderror"
      />
      @error('name')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
      <input
        type="email"
        id="email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="email"
        placeholder="john@example.com"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all @error('email') border-red-500 @enderror"
      />
      @error('email')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
      <input
        type="tel"
        id="phone"
        name="phone"
        value="{{ old('phone') }}"
        autocomplete="tel"
        placeholder="+960 XXX XXXX"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all @error('phone') border-red-500 @enderror"
      />
      @error('phone')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Which company are you interested in?</label>
      <select
        id="company"
        name="company"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all bg-white @error('company') border-red-500 @enderror"
      >
        <option value="">Select a company</option>
        @foreach ($companies as $c)
          <option value="{{ $c['slug'] }}" @selected(old('company') === $c['slug'])>{{ $c['name'] }}</option>
        @endforeach
      </select>
      @error('company')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
      <textarea
        id="message"
        name="message"
        required
        rows="5"
        placeholder="Tell us how we can help you..."
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none @error('message') border-red-500 @enderror"
      >{{ old('message') }}</textarea>
      @error('message')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </div>

    <button
      type="submit"
      :disabled="!canSubmit || submitting"
      :aria-disabled="(!canSubmit || submitting) ? 'true' : 'false'"
      class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
    >
      Send Message
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
        <path d="m21.854 2.147-10.94 10.939" />
      </svg>
    </button>
  </div>
</form>

