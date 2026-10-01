/**
 * Alpine component for the AssetPicker form field (app/Filament/Forms/Components/AssetPicker.php).
 * Loaded before Filament's core scripts so it is registered on `alpine:init`.
 */
(function () {
    function storageUrl(path) {
        return '/storage/' + String(path).replace(/^\/+/, '').replace(/^storage\//, '').split('/').map(encodeURIComponent).join('/');
    }

    function sizeLabel(kb) {
        return kb >= 1024 ? (Math.round((kb / 1024) * 10) / 10) + ' MB' : kb + ' KB';
    }

    function litusAssetPicker(config) {
        return {
            state: config.state,
            assetsUrl: config.assetsUrl,
            uploadUrl: config.uploadUrl,
            csrf: config.csrf,
            multiple: !!config.multiple,
            maxKb: config.maxKb || 4096,
            fallbackBaseUrl: config.fallbackBaseUrl || null,
            disabled: !!config.disabled,
            open: false,
            loading: false,
            busy: false,
            search: '',
            assets: [],
            error: '',
            page: 1,
            perPage: 24,
            total: 0,
            hasMore: false,
            labels: {},

            get paths() {
                const list = Array.isArray(this.state) ? this.state : (this.state ? [this.state] : []);
                const clean = list.filter((path) => typeof path === 'string' && path !== '');

                return this.multiple ? clean : clean.slice(0, 1);
            },

            get maxLabel() {
                return sizeLabel(this.maxKb);
            },

            get totalPages() {
                return this.total ? Math.max(1, Math.ceil(this.total / this.perPage)) : 1;
            },

            get metaLabel() {
                if (this.loading) {
                    return 'Loading…';
                }
                if (!this.total) {
                    return 'No images';
                }
                const from = (this.page - 1) * this.perPage + 1;

                return 'Showing ' + from + '–' + Math.min(this.page * this.perPage, this.total) + ' of ' + this.total;
            },

            previewUrl(path) {
                const value = String(path || '');
                if (/^https?:\/\//i.test(value) || value.charAt(0) === '/') {
                    return value;
                }
                // Older values may be a bare file name served from a public folder (e.g. company logos).
                if (value.indexOf('/') === -1 && this.fallbackBaseUrl) {
                    return this.fallbackBaseUrl + encodeURIComponent(value);
                }

                return storageUrl(value);
            },

            labelFor(path) {
                if (this.labels[path]) {
                    return this.labels[path];
                }
                const base = String(path).split('?')[0].split('/').pop() || String(path);

                return decodeURIComponent(base.replace(/\.[a-z0-9]+$/i, ''));
            },

            tooBig(asset) {
                return !!asset && !!asset.size && asset.size > this.maxKb * 1024;
            },

            setPaths(paths) {
                this.state = this.multiple ? paths : (paths[0] || null);
            },

            openPicker() {
                this.error = '';
                this.open = true;
                this.loadAssets(true);
            },

            closePicker() {
                this.open = false;
            },

            changePage(step) {
                const next = this.page + step;
                if (next < 1 || (step > 0 && !this.hasMore) || this.loading) {
                    return;
                }
                this.page = next;
                this.loadAssets(false);
            },

            async loadAssets(resetPage) {
                if (resetPage) {
                    this.page = 1;
                }
                this.loading = true;
                this.error = '';

                try {
                    const url = new URL(this.assetsUrl, window.location.origin);
                    url.searchParams.set('page', String(this.page));
                    url.searchParams.set('per_page', String(this.perPage));
                    if (this.search.trim()) {
                        url.searchParams.set('q', this.search.trim());
                    }

                    const response = await fetch(url.toString(), {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(payload.message || 'Could not load images.');
                    }

                    this.assets = Array.isArray(payload.data) ? payload.data : [];
                    this.total = payload.total || 0;
                    this.hasMore = !!payload.has_more;
                } catch (error) {
                    this.assets = [];
                    this.total = 0;
                    this.hasMore = false;
                    this.error = error.message || 'Could not load images.';
                } finally {
                    this.loading = false;
                }
            },

            selectAsset(asset) {
                if (!asset || !asset.path) {
                    return;
                }
                if (this.tooBig(asset)) {
                    this.error = '“' + asset.name + '” is ' + asset.size_label + '. This field allows images up to ' + this.maxLabel + ' — pick a smaller one or upload a compressed version.';
                    return;
                }

                this.error = '';
                this.labels[asset.path] = asset.name;

                if (this.multiple) {
                    const current = this.paths.slice();
                    const index = current.indexOf(asset.path);
                    // Clicking a picked image again removes it.
                    if (index === -1) {
                        current.push(asset.path);
                    } else {
                        current.splice(index, 1);
                    }
                    this.setPaths(current);
                } else {
                    this.setPaths([asset.path]);
                    this.closePicker();
                }
            },

            remove(path) {
                this.setPaths(this.paths.filter((item) => item !== path));
            },

            move(index, step) {
                const current = this.paths.slice();
                const target = index + step;
                if (target < 0 || target >= current.length) {
                    return;
                }
                [current[index], current[target]] = [current[target], current[index]];
                this.setPaths(current);
            },

            async uploadFiles(event) {
                const input = event.target;
                const files = input.files ? Array.from(input.files) : [];
                input.value = '';
                if (!files.length) {
                    return;
                }

                this.busy = true;
                this.error = '';

                try {
                    for (const file of this.multiple ? files : files.slice(0, 1)) {
                        if (file.size > this.maxKb * 1024) {
                            throw new Error('“' + file.name + '” is larger than ' + this.maxLabel + '. Please compress it and try again.');
                        }

                        const body = new FormData();
                        body.append('image', file);
                        body.append('max_kb', String(this.maxKb));

                        const response = await fetch(this.uploadUrl, {
                            method: 'POST',
                            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                            body,
                        });
                        const payload = await response.json().catch(() => ({}));
                        if (!response.ok || !payload.path) {
                            throw new Error((payload.errors && payload.errors.image && payload.errors.image[0]) || payload.message || 'Upload failed.');
                        }

                        this.labels[payload.path] = payload.name;
                        this.setPaths(this.multiple ? this.paths.concat([payload.path]) : [payload.path]);
                    }

                    if (this.open) {
                        if (this.multiple) {
                            await this.loadAssets(true);
                        } else {
                            this.closePicker();
                        }
                    }
                } catch (error) {
                    this.error = error.message || 'Upload failed.';
                } finally {
                    this.busy = false;
                }
            },
        };
    }

    window.litusAssetPicker = litusAssetPicker;

    document.addEventListener('alpine:init', () => {
        if (window.Alpine && typeof window.Alpine.data === 'function') {
            window.Alpine.data('litusAssetPicker', litusAssetPicker);
        }
    });

    // "Copy URL" on the Assets page (dispatched from the Livewire action).
    window.addEventListener('litus-copy-to-clipboard', (event) => {
        const detail = event.detail;
        const text = typeof detail === 'string' ? detail : (detail && detail.text) || '';
        if (text && navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).catch(() => { /* notification still shows the URL */ });
        }
    });
})();
