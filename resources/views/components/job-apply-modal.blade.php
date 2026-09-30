<div
  x-show="showApplyModal"
  x-cloak
  class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto bg-ink-950/60 p-3 backdrop-blur-md sm:items-center sm:p-6"
  x-transition.opacity.duration.200ms
  @keydown.escape.window="closeApplyModal()"
  @click.self="closeApplyModal()"
>
  <div
    class="relative my-auto w-full max-w-xl overflow-hidden rounded-[2rem] bg-white shadow-[0_40px_120px_-30px_rgba(3,12,32,0.7)] ring-1 ring-ink-900/5"
    role="dialog"
    aria-modal="true"
    aria-labelledby="job-apply-modal-title"
    x-transition:enter="transition duration-300 ease-out"
    x-transition:enter-start="translate-y-4 scale-[0.98] opacity-0"
    x-transition:enter-end="translate-y-0 scale-100 opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="translate-y-2 opacity-0"
  >
    {{-- Header --}}
    <div class="ui-bg-ink relative px-6 pt-6 pb-6 sm:px-8 sm:pt-8">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <span class="ui-eyebrow ui-eyebrow--light">Application</span>
          <h3 id="job-apply-modal-title" class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-[1.7rem]">
            Apply for
            <span class="ui-accent block break-words text-lagoon-300" x-text="applyJobTitle || 'Position'"></span>
          </h3>
          <p class="mt-2 text-sm leading-relaxed text-ink-300">
            Fill in the essentials and attach your CV. We’ll get back to you shortly.
          </p>
        </div>
        <button
          type="button"
          class="ui-icon-btn border border-white/15 bg-white/10 text-white hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lagoon-300"
          @click="closeApplyModal()"
          aria-label="Close"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
    </div>

    <form
      action="{{ route('site.careers.apply') }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-5 px-6 py-6 sm:px-8 sm:py-7"
    >
      @csrf
      <input type="hidden" name="apply_title_locked" :value="applyJobTitleLocked ? '1' : '0'" />
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
          <label for="job-apply-position" class="ui-label">Position</label>
          <div class="relative">
            <input
              id="job-apply-position"
              type="text"
              name="position"
              class="ui-input read-only:cursor-default read-only:border-ink-100 read-only:bg-ink-50/60 read-only:pr-11 read-only:font-semibold read-only:focus:ring-0 @error('position') !border-red-400 @enderror"
              x-model="applyJobTitle"
              :readonly="applyJobTitleLocked"
              :placeholder="applyJobTitleLocked ? '' : 'e.g. Marketing Executive'"
            />
            <svg x-show="applyJobTitleLocked" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-ink-400" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          @error('position')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="job-apply-name" class="ui-label">Full Name</label>
          <input
            id="job-apply-name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            class="ui-input @error('name') !border-red-400 @enderror"
            placeholder="Your name"
            autocomplete="name"
          />
          @error('name')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="job-apply-email" class="ui-label">Email</label>
          <input
            id="job-apply-email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            class="ui-input @error('email') !border-red-400 @enderror"
            placeholder="you@example.com"
            autocomplete="email"
          />
          @error('email')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div class="md:col-span-2">
          <label for="job-apply-phone" class="ui-label">Phone <span class="font-normal text-ink-400">(optional)</span></label>
          <input
            id="job-apply-phone"
            type="tel"
            name="phone"
            value="{{ old('phone') }}"
            class="ui-input @error('phone') !border-red-400 @enderror"
            placeholder="+960 ..."
            autocomplete="tel"
          />
          @error('phone')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div x-data="{ cvName: '' }">
        <span class="ui-label">CV / Resume</span>
        <label
          for="job-apply-cv"
          class="group relative flex cursor-pointer items-center gap-4 rounded-3xl border-2 border-dashed px-4 py-4 text-left transition-all duration-200 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/15 hover:border-brand-400 hover:bg-brand-50/60 sm:px-5 sm:py-5 @error('cv') border-red-300 bg-red-50/40 @else border-ink-200 bg-sand-50 @enderror"
          :class="cvName ? '!border-solid !border-brand-300 !bg-brand-50/70' : ''"
        >
          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-sm ring-1 ring-ink-100 transition-colors group-hover:bg-brand-600 group-hover:text-white group-hover:ring-brand-600">
            <svg x-show="!cvName" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="17 8 12 3 7 8" />
              <line x1="12" y1="3" x2="12" y2="15" />
            </svg>
            <svg x-show="cvName" x-cloak xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
              <path d="M14 2v4a2 2 0 0 0 2 2h4" />
              <path d="m9 15 2 2 4-4" />
            </svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-semibold text-ink-900" x-text="cvName || 'Upload your CV'">Upload your CV</span>
            <span class="mt-0.5 block text-xs text-ink-500" x-text="cvName ? 'Click to choose a different file' : 'PDF or DOCX recommended.'">PDF or DOCX recommended.</span>
          </span>
          <span class="hidden min-h-11 items-center rounded-full sm:inline-flex border border-ink-200 bg-white px-4 text-sm font-semibold text-ink-900 transition-colors group-hover:border-ink-900">
            Browse
          </span>
          <input
            id="job-apply-cv"
            type="file"
            name="cv"
            required
            class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
            accept=".pdf,.doc,.docx"
            @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''"
          />
        </label>
        @error('cv')
          <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex flex-col-reverse gap-3 border-t border-ink-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
        <button
          type="button"
          class="ui-btn ui-btn--sm text-ink-600 hover:bg-ink-50 hover:text-ink-900"
          @click="closeApplyModal()"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="ui-btn ui-btn--primary ui-btn--sm"
        >
          Submit Application
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
      </div>
    </form>
  </div>
</div>
