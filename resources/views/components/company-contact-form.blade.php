@props([
    'companyName' => '',
])

@if (session('status'))
  <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">
    {{ session('status') }}
  </div>
@endif

<form method="POST" action="{{ route('site.contact.submit') }}" class="bg-white p-8 rounded-2xl shadow-lg">
  @csrf
  <input type="hidden" name="company" value="{{ $companyName }}" />

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
