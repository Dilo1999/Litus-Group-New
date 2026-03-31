<div
  x-show="showApplyModal"
  x-cloak
  class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 bg-black/40 backdrop-blur-sm"
  x-transition.opacity.duration.150ms
  @keydown.escape.window="closeApplyModal()"
  @click.self="closeApplyModal()"
>
  <div
    class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5"
    x-transition.scale.origin.center.duration.150ms
  >
    <div class="px-6 pt-6 pb-4 bg-gradient-to-br from-blue-50 via-white to-white border-b border-gray-100">
      <div class="flex items-start justify-between gap-4">
        <div>
          <h3 class="text-xl font-bold text-gray-900 leading-tight">
            Apply for <span class="text-blue-700" x-text="applyJobTitle || 'Position'"></span>
          </h3>
          <p class="mt-1 text-sm text-gray-600">
            Fill in the essentials and attach your CV. We’ll get back to you shortly.
          </p>
        </div>
        <button
          type="button"
          class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 hover:text-gray-700 hover:bg-white/70 transition"
          @click="closeApplyModal()"
          aria-label="Close"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
    </div>

    <form
      action="{{ route('site.contact') }}"
      method="POST"
      enctype="multipart/form-data"
      class="px-6 py-5 space-y-4"
    >
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Position</label>
          <input
            type="text"
            name="position"
            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            x-model="applyJobTitle"
            :readonly="applyJobTitleLocked"
            :placeholder="applyJobTitleLocked ? '' : 'e.g. Marketing Executive'"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800 mb-1">Full Name</label>
          <input
            type="text"
            name="name"
            required
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            placeholder="Your name"
            autocomplete="name"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800 mb-1">Email</label>
          <input
            type="email"
            name="email"
            required
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            placeholder="you@example.com"
            autocomplete="email"
          />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-800 mb-1">Phone (optional)</label>
          <input
            type="tel"
            name="phone"
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            placeholder="+960 ..."
            autocomplete="tel"
          />
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-800 mb-2">CV / Resume</label>
        <div class="relative rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 p-4">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" y1="3" x2="12" y2="15" />
              </svg>
            </div>
            <div class="flex-1">
              <div class="text-sm font-medium text-gray-900">Upload your CV</div>
              <div class="text-xs text-gray-600 mt-0.5">PDF or DOCX recommended.</div>
            </div>
          </div>
          <input
            type="file"
            name="cv"
            required
            class="mt-3 block w-full text-sm text-gray-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-white file:text-blue-700 hover:file:bg-blue-50"
            accept=".pdf,.doc,.docx"
          />
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 pt-2">
        <button
          type="button"
          class="px-4 py-2 rounded-full text-sm font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
          @click="closeApplyModal()"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-6 py-2.5 rounded-full text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition"
        >
          Submit Application
        </button>
      </div>
    </form>
  </div>
</div>
