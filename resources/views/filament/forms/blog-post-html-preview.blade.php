{{-- Live, editable HTML preview for the blog post body. Driven by public/js/filament/blog-post-html-preview.js --}}
<div
    class="col-span-full"
    wire:ignore
    data-blog-html-preview
    data-upload-url="{{ route('admin.media-assets.store') }}"
    data-assets-url="{{ route('admin.media-assets.index') }}"
>
    <style>
        .lb-preview{display:flex;flex-direction:column;gap:.5rem;}
        .lb-preview__bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;}
        .lb-preview__title{font-size:.875rem;font-weight:600;color:#0f172a;}
        .lb-preview__actions{display:flex;flex-wrap:wrap;align-items:center;gap:.5rem;}
        .lb-btn{appearance:none;cursor:pointer;border:1px solid #d6dbe6;background:#fff;color:#0f172a;border-radius:8px;padding:.35rem .7rem;font-size:.75rem;font-weight:600;line-height:1.2;}
        .lb-btn:hover{background:#f6f8fb;border-color:#b9c1d3;}
        .lb-btn:disabled{opacity:.45;cursor:not-allowed;}
        .lb-btn--primary{background:#142d87;border-color:#142d87;color:#fff;}
        .lb-btn--primary:hover{background:#0e2675;border-color:#0e2675;}
        .lb-btn--danger{color:#dc2626;border-color:#fca5a5;}
        .lb-btn--danger:hover{background:#fef2f2;border-color:#f87171;}
        .lb-btn--link{border-color:transparent;background:transparent;color:#1e3a9e;}
        .lb-btn--link:hover{background:rgba(30,58,158,.08);border-color:transparent;}
        .lb-preview__status{font-size:.75rem;color:#5b6477;}
        .lb-preview__frame-wrap{overflow:hidden;border:1px solid #d6dbe6;border-radius:14px;background:#fff;box-shadow:0 1px 2px rgba(15,23,42,.06);height:48rem;max-height:85vh;}
        .lb-preview__frame{display:block;width:100%;height:100%;border:0;background:#fff;}

        .lb-assets{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:1rem;}
        .lb-assets.is-open{display:flex;}
        .lb-assets__backdrop{position:absolute;inset:0;background:rgba(3,11,31,.7);}
        .lb-assets__dialog{position:relative;display:flex;flex-direction:column;width:100%;max-width:min(80rem,96vw);height:88vh;overflow:hidden;border-radius:14px;border:1px solid #e6e9f0;background:#fff;color:#0f172a;box-shadow:0 25px 50px -12px rgba(0,0,0,.55);}
        .lb-assets__head,.lb-assets__tools,.lb-assets__foot{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;padding:.6rem 1rem;flex-shrink:0;}
        .lb-assets__head{border-bottom:1px solid #e6e9f0;}
        .lb-assets__tools{border-bottom:1px solid #e6e9f0;background:#f6f8fb;}
        .lb-assets__foot{border-top:1px solid #e6e9f0;background:#f6f8fb;}
        .lb-assets__heading{margin:0;font-size:.9rem;font-weight:700;}
        .lb-assets__sub,.lb-assets__meta{margin:0;font-size:.75rem;color:#5b6477;}
        .lb-assets__search{flex:1 1 14rem;max-width:22rem;border:1px solid #d6dbe6;border-radius:8px;padding:.4rem .65rem;font-size:.8rem;}
        .lb-assets__search:focus{outline:2px solid rgba(30,58,158,.35);border-color:#1e3a9e;}
        .lb-assets__body{flex:1 1 auto;min-height:0;overflow-y:auto;padding:1rem 1.25rem;}
        .lb-assets__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.875rem;}
        @media (min-width:640px){.lb-assets__grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
        @media (min-width:900px){.lb-assets__grid{grid-template-columns:repeat(4,minmax(0,1fr));}}
        @media (min-width:1280px){.lb-assets__grid{grid-template-columns:repeat(6,minmax(0,1fr));}}
        .lb-assets__card{overflow:hidden;padding:0;border:1px solid #e6e9f0;border-radius:10px;background:#fff;text-align:left;cursor:pointer;box-shadow:0 1px 2px rgba(15,23,42,.06);}
        .lb-assets__card:hover,.lb-assets__card:focus{outline:none;border-color:#1e3a9e;box-shadow:0 0 0 2px rgba(30,58,158,.3);}
        .lb-assets__thumb{aspect-ratio:4/3;overflow:hidden;background:#f1f3f8;}
        .lb-assets__thumb img{display:block;width:100%;height:100%;object-fit:cover;}
        .lb-assets__label{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;border-top:1px solid #f1f3f8;padding:.4rem .5rem;font-size:12px;color:#5b6477;}
        .lb-assets__empty{display:flex;min-height:10rem;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;border:1px dashed #d6dbe6;border-radius:10px;padding:2rem 1rem;text-align:center;font-size:.85rem;color:#5b6477;}
        .lb-assets__close{appearance:none;cursor:pointer;border:0;background:transparent;width:2rem;height:2rem;border-radius:8px;color:#5b6477;font-size:1rem;}
        .lb-assets__close:hover{background:#f1f3f8;}
        .lb-assets__pager{display:flex;align-items:center;gap:.5rem;font-size:.75rem;color:#5b6477;}
    </style>

    <div class="lb-preview">
        <div class="lb-preview__bar">
            <span class="lb-preview__title">Preview (editable)</span>
            <div class="lb-preview__actions">
                <button type="button" class="lb-btn" data-blog-html-preview-insert="heading">+ Heading</button>
                <button type="button" class="lb-btn" data-blog-html-preview-insert="paragraph">+ Paragraph</button>
                <button type="button" class="lb-btn" data-blog-html-preview-insert="image">+ Image</button>
                <button type="button" class="lb-btn" data-blog-html-preview-insert="quote">+ Quote</button>
                <button type="button" class="lb-btn" data-blog-html-preview-insert="list">+ List</button>
                <button type="button" class="lb-btn lb-btn--danger" data-blog-html-preview-delete-section>Delete</button>
                <button type="button" class="lb-btn lb-btn--link" data-blog-html-preview-reload>Reload preview</button>
                <span class="lb-preview__status" data-blog-html-preview-status>Click a block, then Insert or Delete</span>
            </div>
        </div>

        <div class="lb-preview__frame-wrap">
            <iframe
                title="Article HTML preview"
                class="lb-preview__frame"
                sandbox="allow-scripts"
                data-blog-html-preview-frame
            ></iframe>
        </div>
    </div>

    <input
        type="file"
        accept="image/*"
        data-blog-html-preview-file
        style="position:fixed;left:-9999px;width:1px;height:1px;opacity:0;"
    >

    <div class="lb-assets" data-blog-asset-picker role="dialog" aria-modal="true" aria-labelledby="lb-assets-title" data-page="1">
        <div class="lb-assets__backdrop" data-blog-asset-picker-backdrop></div>

        <div class="lb-assets__dialog" data-blog-asset-picker-dialog>
            <div class="lb-assets__head">
                <div>
                    <h3 id="lb-assets-title" class="lb-assets__heading">Choose an image</h3>
                    <p class="lb-assets__sub" data-blog-asset-picker-subtitle>Select an image or upload a new one.</p>
                </div>
                <button type="button" class="lb-assets__close" data-blog-asset-picker-close aria-label="Close">✕</button>
            </div>

            <div class="lb-assets__tools">
                <input type="search" class="lb-assets__search" placeholder="Search by file name…" data-blog-asset-picker-search>
                <button type="button" class="lb-btn lb-btn--primary" data-blog-asset-picker-upload>Upload new</button>
            </div>

            <div class="lb-assets__body" data-blog-asset-picker-grid></div>

            <div class="lb-assets__foot">
                <p class="lb-assets__meta" data-blog-asset-picker-meta>Loading…</p>
                <div class="lb-assets__pager">
                    <button type="button" class="lb-btn" data-blog-asset-picker-prev disabled>Prev</button>
                    <span data-blog-asset-picker-page>Page 1 / 1</span>
                    <button type="button" class="lb-btn" data-blog-asset-picker-next disabled>Next</button>
                    <button type="button" class="lb-btn lb-btn--link" data-blog-asset-picker-close>Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
