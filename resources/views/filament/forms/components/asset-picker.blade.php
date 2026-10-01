{{-- Assets library image field. Behaviour: public/js/filament/asset-picker.js --}}
@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$statePath"
>
    <div
        wire:ignore
        x-data="litusAssetPicker({
            state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
            assetsUrl: @js($getAssetsUrl()),
            uploadUrl: @js($getUploadUrl()),
            csrf: @js(csrf_token()),
            multiple: @js($isMultiple()),
            maxKb: @js($getMaxSizeKb()),
            fallbackBaseUrl: @js($getFallbackBaseUrl()),
            disabled: @js($isDisabled()),
        })"
        class="lap"
        style="--lap-preview-h: {{ $getImagePreviewHeight() }}"
    >
        <div class="lap-selected" :class="multiple ? 'lap-selected--multi' : ''" x-show="paths.length" x-cloak>
            <template x-for="(path, index) in paths" :key="path">
                <figure class="lap-item">
                    <img :src="previewUrl(path)" :alt="labelFor(path)" loading="lazy">
                    <figcaption class="lap-item__bar">
                        <span class="lap-item__name" x-text="labelFor(path)" :title="path"></span>
                        <span class="lap-item__actions" x-show="!disabled">
                            <button type="button" class="lap-icon-btn" x-show="multiple && index > 0" x-on:click.prevent="move(index, -1)" title="Move left" aria-label="Move left">←</button>
                            <button type="button" class="lap-icon-btn" x-show="multiple && index < paths.length - 1" x-on:click.prevent="move(index, 1)" title="Move right" aria-label="Move right">→</button>
                            <button type="button" class="lap-icon-btn lap-icon-btn--danger" x-on:click.prevent="remove(path)" title="Remove (the file stays in Assets)" aria-label="Remove">✕</button>
                        </span>
                    </figcaption>
                </figure>
            </template>
        </div>

        <div class="lap-empty" x-show="!paths.length">No image selected</div>

        <div class="lap-toolbar" x-show="!disabled">
            <button type="button" class="lap-btn lap-btn--primary" x-on:click.prevent="openPicker()">
                <span x-text="multiple ? 'Add from Assets' : (paths.length ? 'Change image' : 'Choose from Assets')"></span>
            </button>
            <button type="button" class="lap-btn" x-on:click.prevent="$refs.fileInput.click()" x-bind:disabled="busy">
                <span x-text="busy ? 'Uploading…' : 'Upload new'"></span>
            </button>
            <span class="lap-hint" x-text="'Max ' + maxLabel + ' · saved to the shared Assets library'"></span>
        </div>

        <p class="lap-error" x-show="error" x-text="error" x-cloak></p>

        <input
            type="file"
            accept="image/*,.svg"
            x-ref="fileInput"
            x-bind:multiple="multiple"
            x-on:change="uploadFiles($event)"
            style="position:fixed;left:-9999px;width:1px;height:1px;opacity:0;"
        >

        <template x-teleport="body">
            <div class="lap-overlay" x-show="open" x-cloak x-on:keydown.escape.window="open && closePicker()">
                <div class="lap-backdrop" x-on:click="closePicker()"></div>

                <div class="lap-dialog" role="dialog" aria-modal="true" aria-label="Choose an image from Assets">
                    <div class="lap-dialog__head">
                        <div>
                            <h3 class="lap-dialog__title">Choose from Assets</h3>
                            <p class="lap-dialog__sub" x-text="multiple ? 'Click images to add them. Close when you are done.' : 'Click an image to use it.'"></p>
                        </div>
                        <button type="button" class="lap-close" x-on:click.prevent="closePicker()" aria-label="Close">✕</button>
                    </div>

                    <div class="lap-dialog__tools">
                        <input
                            type="search"
                            class="lap-search"
                            placeholder="Search by name…"
                            x-model="search"
                            x-on:input.debounce.350ms="loadAssets(true)"
                            x-on:keydown.enter.prevent="loadAssets(true)"
                            autocomplete="off"
                        >
                        <button type="button" class="lap-btn lap-btn--primary" x-on:click.prevent="$refs.fileInput.click()" x-bind:disabled="busy">
                            <span x-text="busy ? 'Uploading…' : 'Upload new'"></span>
                        </button>
                    </div>

                    <div class="lap-dialog__body">
                        <p class="lap-error" x-show="error" x-text="error"></p>

                        <div class="lap-grid" x-show="loading">
                            <template x-for="i in 12" :key="'sk-' + i"><div class="lap-skeleton"></div></template>
                        </div>

                        <div class="lap-none" x-show="!loading && !assets.length">
                            <span x-text="search ? 'No images match your search.' : 'No images yet. Upload one to get started.'"></span>
                        </div>

                        <div class="lap-grid" x-show="!loading && assets.length">
                            <template x-for="asset in assets" :key="asset.id">
                                <button
                                    type="button"
                                    class="lap-card"
                                    :class="{ 'is-picked': paths.includes(asset.path), 'is-too-big': tooBig(asset) }"
                                    x-on:click.prevent="selectAsset(asset)"
                                    :title="asset.name + (tooBig(asset) ? ' — larger than ' + maxLabel : '')"
                                >
                                    <span class="lap-card__thumb"><img :src="asset.url" alt="" loading="lazy"></span>
                                    <span class="lap-card__label" x-text="asset.name"></span>
                                    <span class="lap-card__size" x-text="asset.size_label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="lap-dialog__foot">
                        <span x-text="metaLabel"></span>
                        <span class="lap-pager">
                            <button type="button" class="lap-btn" x-on:click.prevent="changePage(-1)" x-bind:disabled="loading || page <= 1">Prev</button>
                            <span x-text="'Page ' + page + ' / ' + totalPages"></span>
                            <button type="button" class="lap-btn" x-on:click.prevent="changePage(1)" x-bind:disabled="loading || !hasMore">Next</button>
                            <button type="button" class="lap-btn lap-btn--primary" x-on:click.prevent="closePicker()" x-text="multiple ? 'Done' : 'Close'"></button>
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-dynamic-component>
