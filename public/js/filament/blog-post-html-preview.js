/**
 * Blog post HTML editor (Filament admin).
 *
 * The "body" textarea holds the article HTML. Next to it, a sandboxed iframe renders that
 * HTML with the website's article styles and makes it editable in place: click text to
 * type and format it, click an image to resize / align / replace it, insert or delete blocks.
 * Every edit is cleaned (editor-only attributes removed) and written back into the textarea,
 * so saving the form saves exactly the HTML that is shown on the website.
 *
 * Parent page <-> iframe communication uses postMessage (the iframe has an opaque origin).
 */
(function () {
    const MSG = {
        // iframe -> parent
        html: 'lb-preview-html',
        status: 'lb-preview-status',
        scroll: 'lb-preview-scroll',
        pickImage: 'lb-preview-pick-image',
        askLink: 'lb-preview-ask-link',
        askAlt: 'lb-preview-ask-alt',
        // parent -> iframe
        insert: 'lb-preview-insert',
        deleteBlock: 'lb-preview-delete',
        setImage: 'lb-preview-set-image',
        setLink: 'lb-preview-set-link',
        setAlt: 'lb-preview-set-alt',
    };

    const EMPTY_HINT = '<p data-lb-empty-hint>Start writing: use the buttons above to add a heading, paragraph, image, quote or list — or paste HTML into the field above.</p>';

    /* Same typography as .blog-article-body on the public article page (resources/css/app.css). */
    const ARTICLE_CSS = [
        'html,body{margin:0;padding:0;background:#fff;}',
        'body{box-sizing:border-box;max-width:calc(70ch + 3rem);margin:0 auto;padding:5.5rem 1.5rem 3rem;overflow-x:hidden;font-family:"Plus Jakarta Sans",ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;font-size:1.075rem;line-height:1.9;color:#334672;overflow-wrap:anywhere;-webkit-font-smoothing:antialiased;}',
        '*{box-sizing:border-box;}',
        'body>:first-child{margin-top:0;}',
        'p{margin:0 0 1.75rem;}',
        'h1,h2,h3,h4,h5,h6{color:#061634;font-weight:800;line-height:1.25;letter-spacing:-0.02em;}',
        'h1{font-size:2.25rem;margin:3rem 0 1rem;}',
        'h2{font-size:1.75rem;margin:3rem 0 1rem;}',
        'h3{font-size:1.375rem;margin:2.25rem 0 .75rem;}',
        'h4,h5,h6{font-size:1.125rem;margin:2rem 0 .75rem;}',
        'a{color:#1f4fe0;text-decoration:underline;text-underline-offset:3px;}',
        'strong,b{color:#061634;font-weight:700;}',
        'ul,ol{margin:0 0 1.75rem;padding-left:1.5rem;}',
        'li{margin:0 0 .5rem;padding-left:.25rem;}',
        'li::marker{color:#1f4fe0;}',
        'img{display:block;max-width:100%;height:auto;margin:0 auto;border-radius:1rem;}',
        'figure{margin:2.5rem 0;}',
        'figcaption{margin-top:.75rem;text-align:center;font-size:.875rem;color:#5b6a8c;}',
        'blockquote{position:relative;margin:3rem 0;padding:2.25rem 1.75rem;border-radius:1.5rem;background:#f6f3ee;overflow:hidden;}',
        'blockquote::before{content:"";position:absolute;top:2rem;bottom:2rem;left:0;width:4px;border-radius:0 9999px 9999px 0;background:linear-gradient(to bottom,#1f4fe0,#3ddbe6);}',
        'blockquote p{margin:0;font-family:"Instrument Serif",ui-serif,Georgia,serif;font-size:1.6rem;line-height:1.35;font-style:italic;color:#061634;}',
        'blockquote p+p{margin-top:1rem;}',
        'blockquote cite{display:block;margin-top:1.5rem;font-style:normal;font-size:.875rem;font-weight:600;color:#334672;}',
        'blockquote cite::before{content:"";display:inline-block;width:2rem;height:1px;margin-right:.75rem;vertical-align:middle;background:#b9c1d3;}',
        'table{width:100%;margin:0 0 1.75rem;border-collapse:collapse;font-size:1rem;}',
        'th,td{border:1px solid #dde2ec;padding:.6rem .8rem;text-align:left;vertical-align:top;}',
        'th{background:#eef1f6;color:#061634;font-weight:700;}',
        'hr{margin:3rem 0;border:0;border-top:1px solid #dde2ec;}',
        'pre,code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.9em;}',
        'pre{margin:0 0 1.75rem;padding:1rem;border-radius:.75rem;background:#eef1f6;overflow-x:auto;}',
        '@media (min-width:640px){body{font-size:1.15rem;}blockquote{padding:3rem;}blockquote p{font-size:2rem;}}',
    ].join('');

    /* Editor chrome inside the iframe (never saved). */
    const EDITOR_CSS = [
        '[data-lb-empty-hint]{color:#8a96b0;font-size:.95rem;}',
        '[data-lb-text]{cursor:text;border-radius:4px;transition:outline-color .15s,background-color .15s;}',
        '[data-lb-text]:hover{outline:2px dashed rgba(30,58,158,.4);outline-offset:3px;}',
        '[data-lb-text]:focus{outline:2px solid rgba(30,58,158,.8);outline-offset:3px;background:rgba(30,58,158,.04);}',
        'img[data-lb-img]{cursor:pointer;}',
        'img[data-lb-img]:hover{outline:3px solid #3ddbe6;outline-offset:3px;}',
        '[data-lb-selected]{outline:3px solid #1e3a9e!important;outline-offset:5px;box-shadow:0 0 0 6px rgba(30,58,158,.12);}',
        '#lb-bar{position:fixed;top:0;left:0;right:0;z-index:9999;display:none;flex-wrap:wrap;align-items:center;gap:6px;padding:8px 10px;background:#061634;border-bottom:1px solid #15306b;box-shadow:0 8px 20px rgba(0,0,0,.18);font:12px/1.2 system-ui,-apple-system,"Segoe UI",sans-serif;color:#dde2ec;}',
        '#lb-bar.is-open{display:flex;}',
        '#lb-bar button,#lb-bar select{appearance:none;height:28px;padding:0 8px;border:1px solid #15306b;border-radius:6px;background:#0b2150;color:#fff;font:inherit;cursor:pointer;}',
        '#lb-bar select{padding-right:6px;}',
        '#lb-bar button:hover,#lb-bar select:hover{background:#15306b;}',
        '#lb-bar b,#lb-bar i,#lb-bar u{color:inherit;font-weight:700;}',
        '#lb-bar button[data-active="1"]{background:#3ddbe6;border-color:#3ddbe6;color:#061634;}',
        '#lb-bar .lb-sep{width:1px;height:18px;margin:0 2px;background:#15306b;}',
        '#lb-bar .lb-label{margin-right:2px;color:#8a96b0;}',
        '#lb-bar .lb-group{display:inline-flex;align-items:center;gap:6px;}',
        '#lb-bar .lb-danger{border-color:#f87171;color:#fecaca;}',
        '#lb-bar[data-mode="text"] [data-for="image"],#lb-bar[data-mode="image"] [data-for="text"],#lb-bar[data-mode="block"] [data-for="text"],#lb-bar[data-mode="block"] [data-for="image"]{display:none!important;}',
        '#lb-bar [data-for="tag"][hidden]{display:none!important;}',
    ].join('');

    const TOOLBAR =
        '<div id="lb-bar" data-mode="block">' +
        '<span class="lb-group" data-for="text">' +
        '<select data-tag title="Text style" data-for="tag">' +
        '<option value="p">Paragraph</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option>' +
        '</select>' +
        '<button type="button" data-cmd="bold" title="Bold (Ctrl+B)"><b>B</b></button>' +
        '<button type="button" data-cmd="italic" title="Italic (Ctrl+I)"><i>I</i></button>' +
        '<button type="button" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>' +
        '<button type="button" data-link title="Add or edit a link on the selected text">Link</button>' +
        '<button type="button" data-cmd="unlink" title="Remove link">Unlink</button>' +
        '<span class="lb-sep"></span>' +
        '<button type="button" data-size-step="-1" title="Smaller text">A-</button>' +
        '<select data-font-size title="Font size"><option value="">Size</option>' +
        '<option value="14">14</option><option value="16">16</option><option value="18">18</option><option value="20">20</option><option value="24">24</option><option value="28">28</option><option value="32">32</option>' +
        '</select>' +
        '<button type="button" data-size-step="1" title="Larger text">A+</button>' +
        '<span class="lb-sep"></span>' +
        '<button type="button" data-text-align="left" title="Align left">L</button>' +
        '<button type="button" data-text-align="center" title="Align center">C</button>' +
        '<button type="button" data-text-align="right" title="Align right">R</button>' +
        '<button type="button" data-cmd="removeFormat" title="Clear formatting of the selected text">Clear</button>' +
        '</span>' +
        '<span class="lb-group" data-for="image">' +
        '<span class="lb-label">Image</span>' +
        '<button type="button" data-img-step="-1" title="Smaller">W-</button>' +
        '<select data-img-width title="Image width"><option value="">Width</option>' +
        '<option value="25">25%</option><option value="40">40%</option><option value="50">50%</option><option value="60">60%</option><option value="75">75%</option><option value="85">85%</option><option value="100">100%</option>' +
        '</select>' +
        '<button type="button" data-img-step="1" title="Larger">W+</button>' +
        '<span class="lb-sep"></span>' +
        '<button type="button" data-img-align="left" title="Align left">L</button>' +
        '<button type="button" data-img-align="center" title="Align center">C</button>' +
        '<button type="button" data-img-align="right" title="Align right">R</button>' +
        '<span class="lb-sep"></span>' +
        '<button type="button" data-img-replace title="Replace this image">Replace</button>' +
        '<button type="button" data-img-alt title="Alt text (for SEO and screen readers)">Alt text</button>' +
        '</span>' +
        '<span class="lb-sep"></span>' +
        '<span class="lb-group">' +
        '<button type="button" data-position title="Where new blocks are inserted">Insert ↓</button>' +
        '<button type="button" data-insert="paragraph">+ Paragraph</button>' +
        '<button type="button" data-insert="heading">+ Heading</button>' +
        '<button type="button" data-insert="image">+ Image</button>' +
        '<button type="button" data-insert="quote">+ Quote</button>' +
        '<button type="button" data-insert="list">+ List</button>' +
        '<span class="lb-sep"></span>' +
        '<button type="button" class="lb-danger" data-delete title="Delete the selected block">Delete</button>' +
        '</span>' +
        '</div>';

    /**
     * Runs INSIDE the iframe. It is serialised with toString(), so it must not use
     * anything from the surrounding scope — everything it needs comes in through `cfg`.
     */
    function previewBridge(cfg) {
        const MSG = cfg.msg;
        const TEXT_SEL = 'p,h1,h2,h3,h4,h5,h6,li,blockquote,figcaption,cite,td,th,dt,dd';
        const BLOCK_CHILD_SEL = 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,blockquote,table,div,section,article,figure,header,footer,pre';
        const UNIT_SEL = 'li,p,h1,h2,h3,h4,h5,h6,pre,hr,dl,div,section,article';
        const ATOMIC_SEL = 'blockquote,figure,table';
        const HEADING_TAGS = ['P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'];
        const SIZE_STEPS = [14, 16, 18, 20, 24, 28, 32];
        const WIDTH_STEPS = [25, 40, 50, 60, 75, 85, 100];

        const bar = document.getElementById('lb-bar');
        let activeText = null;
        let activeImage = null;
        let selected = null;
        let position = 'after';
        let savedRange = null;
        let postTimer = null;
        let lastPosted = null;
        let imageSeq = 0;
        let userScrolled = false;

        const send = (payload) => window.parent.postMessage(payload, '*');
        const status = (text) => send({ type: MSG.status, text });
        const inBar = (el) => !!(el && el.closest && el.closest('#lb-bar'));
        const isContent = (el) => !!el && el.nodeType === 1 && el !== document.body && !inBar(el) && el.tagName !== 'SCRIPT' && el.tagName !== 'STYLE';

        function hasMedia(el) {
            return !!el.querySelector('img,video,iframe,svg,hr,table');
        }

        function isBlank(el) {
            return (el.textContent || '').replace(/ /g, ' ').trim() === '' && !hasMedia(el);
        }

        /* ---------- marking editable content ---------- */

        function markImages() {
            document.querySelectorAll('img').forEach((img) => {
                if (inBar(img)) {
                    return;
                }
                img.setAttribute('data-lb-img', '1');
                if (!img.getAttribute('data-lb-img-id')) {
                    img.setAttribute('data-lb-img-id', 'img-' + (imageSeq++));
                }
            });
        }

        function makeEditable(el) {
            el.setAttribute('contenteditable', 'true');
            el.setAttribute('data-lb-text', '1');
        }

        function markText() {
            document.querySelectorAll(TEXT_SEL + ',div').forEach((el) => {
                if (!isContent(el) || el.hasAttribute('data-lb-empty-hint')) {
                    return;
                }
                if (el.querySelector(BLOCK_CHILD_SEL)) {
                    return;
                }
                if (el.tagName === 'DIV' && isBlank(el)) {
                    return;
                }
                makeEditable(el);
            });
        }

        /* Links must not navigate inside the preview; the real href is restored on save. */
        function neutralizeLinks() {
            document.querySelectorAll('a[href]').forEach((a) => {
                if (inBar(a)) {
                    return;
                }
                const href = a.getAttribute('href') || '';
                if (href === '#' && a.hasAttribute('data-lb-href')) {
                    return;
                }
                a.setAttribute('data-lb-href', href);
                if (!(href.charAt(0) === '#' && href.length > 1)) {
                    a.setAttribute('href', '#');
                }
            });
        }

        function refresh() {
            const hint = document.querySelector('[data-lb-empty-hint]');
            if (hint && Array.from(document.body.children).some((child) => isContent(child) && child !== hint)) {
                hint.remove();
            }
            neutralizeLinks();
            markImages();
            markText();
        }

        /* ---------- clean HTML that is written back to the textarea ---------- */

        function cleanHtml() {
            const clone = document.body.cloneNode(true);
            clone.querySelectorAll('script,style,#lb-bar,[data-lb-empty-hint]').forEach((el) => el.remove());
            clone.querySelectorAll('[data-lb-href]').forEach((a) => {
                a.setAttribute('href', a.getAttribute('data-lb-href') || '#');
                a.removeAttribute('data-lb-href');
            });
            clone.querySelectorAll('[data-lb-text],[data-lb-img],[data-lb-selected],[contenteditable]').forEach((el) => {
                ['data-lb-text', 'data-lb-img', 'data-lb-img-id', 'data-lb-selected', 'contenteditable'].forEach((attr) => el.removeAttribute(attr));
            });
            clone.querySelectorAll('[style=""]').forEach((el) => el.removeAttribute('style'));

            return clone.innerHTML.trim();
        }

        function postHtml() {
            clearTimeout(postTimer);
            const html = cleanHtml();
            // Only real edits are sent; re-serialising untouched HTML would mark the form as changed.
            if (html === lastPosted) {
                return;
            }
            lastPosted = html;
            send({ type: MSG.html, html });
        }

        function schedulePost() {
            clearTimeout(postTimer);
            postTimer = setTimeout(postHtml, 250);
        }

        /* ---------- selection ---------- */

        function findUnit(el) {
            if (!isContent(el)) {
                return null;
            }
            const atomic = el.closest(ATOMIC_SEL);
            if (atomic && isContent(atomic)) {
                return atomic;
            }
            if (el.tagName === 'IMG') {
                const wrap = el.parentElement;
                // An image alone in its paragraph/div is one block; otherwise the image itself.
                if (wrap && isContent(wrap) && wrap.matches('p,div') && wrap.children.length === 1 && (wrap.textContent || '').trim() === '') {
                    return wrap;
                }
                return el;
            }
            const unit = el.closest(UNIT_SEL);
            return unit && isContent(unit) ? unit : el;
        }

        function clearSelected() {
            document.querySelectorAll('[data-lb-selected]').forEach((el) => el.removeAttribute('data-lb-selected'));
        }

        function select(el) {
            const unit = findUnit(el);
            clearSelected();
            selected = unit;
            if (unit) {
                unit.setAttribute('data-lb-selected', '1');
            }
            return unit;
        }

        function setMode(mode) {
            bar.setAttribute('data-mode', mode);
            bar.classList.add('is-open');
            syncBar();
        }

        function hideBar() {
            bar.classList.remove('is-open');
        }

        function selectImage(img) {
            activeText = null;
            activeImage = img;
            select(img);
            setMode('image');
            status('Image selected. Resize, align, replace it or edit its alt text.');
        }

        /* ---------- inserting / deleting blocks ---------- */

        function insertAnchor() {
            let anchor = selected && selected.isConnected ? selected : null;
            if (!anchor) {
                return null;
            }
            // New blocks never go inside a list, a table cell or next to an inline image.
            const list = anchor.closest('ul,ol');
            if (list && isContent(list)) {
                anchor = list;
            }
            if (anchor.tagName === 'IMG' && anchor.parentElement && isContent(anchor.parentElement)) {
                anchor = anchor.parentElement;
            }
            return anchor;
        }

        function place(nodes, where) {
            const anchor = insertAnchor();
            const pos = where || position;
            let ref;
            let parent;

            if (!anchor) {
                parent = document.body;
                ref = pos === 'before' ? firstContentChild() : endOfContent();
            } else {
                parent = anchor.parentNode;
                ref = pos === 'before' ? anchor : anchor.nextSibling;
            }

            nodes.forEach((node) => parent.insertBefore(node, ref));
        }

        function firstContentChild() {
            let child = document.body.firstElementChild;
            while (child && !isContent(child)) {
                child = child.nextElementSibling;
            }
            return child;
        }

        function endOfContent() {
            const scripts = document.body.querySelectorAll(':scope > script');
            return scripts.length ? scripts[0] : null;
        }

        function el(tag, text) {
            const node = document.createElement(tag);
            if (text) {
                node.textContent = text;
            }
            return node;
        }

        function focusText(node, selectAll) {
            if (!node || !node.focus) {
                return;
            }
            node.focus();
            const range = document.createRange();
            range.selectNodeContents(node);
            if (!selectAll) {
                range.collapse(false);
            }
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }

        function insertBlock(kind, data) {
            let nodes = [];
            let focus = null;

            if (kind === 'paragraph') {
                focus = el('p', 'New paragraph — click to edit');
                nodes = [focus];
            } else if (kind === 'heading') {
                const heading = el('h2', 'New heading');
                const p = el('p', 'New paragraph — click to edit');
                nodes = [heading, p];
                focus = heading;
            } else if (kind === 'quote') {
                const quote = el('blockquote');
                focus = el('p', 'Quote text — click to edit');
                quote.appendChild(focus);
                quote.appendChild(el('cite', 'Name, role (optional)'));
                nodes = [quote];
            } else if (kind === 'list') {
                const list = el('ul');
                focus = el('li', 'First item');
                list.appendChild(focus);
                list.appendChild(el('li', 'Second item'));
                nodes = [list];
            } else if (kind === 'image' && data && data.url) {
                const p = el('p');
                const img = el('img');
                img.setAttribute('src', data.url);
                img.setAttribute('alt', data.alt || '');
                p.appendChild(img);
                nodes = [p];
                place(nodes, data.position);
                refresh();
                selectImage(img);
                postHtml();
                return;
            } else {
                return;
            }

            place(nodes, data && data.position);
            refresh();
            activeImage = null;
            activeText = focus;
            select(focus);
            focusText(focus, true);
            setMode('text');
            postHtml();
            status('Block inserted. Type to replace the placeholder text.');
        }

        function deleteSelected() {
            const target = selected && selected.isConnected ? selected : null;
            if (!target) {
                status('Select a block first, then click Delete.');
                return;
            }

            let parent = target.parentElement;
            target.remove();

            // Remove wrappers left empty (e.g. a list whose last item was deleted).
            while (parent && isContent(parent) && isBlank(parent) && !parent.matches('td,th')) {
                const next = parent.parentElement;
                parent.remove();
                parent = next;
            }

            selected = null;
            activeText = null;
            activeImage = null;
            clearSelected();
            hideBar();
            postHtml();
            status('Block deleted.');
        }

        /* ---------- text formatting ---------- */

        function exec(cmd, value) {
            if (!activeText) {
                return;
            }
            activeText.focus();
            restoreRange();
            try {
                document.execCommand('styleWithCSS', false, false);
            } catch (e) { /* not supported */ }
            document.execCommand(cmd, false, value || null);
            neutralizeLinks();
            schedulePost();
            syncBar();
        }

        function saveRange() {
            const sel = window.getSelection();
            if (sel && sel.rangeCount && activeText && activeText.contains(sel.anchorNode)) {
                savedRange = sel.getRangeAt(0).cloneRange();
            }
        }

        function restoreRange() {
            if (!savedRange || !activeText || !activeText.contains(savedRange.startContainer)) {
                return;
            }
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(savedRange);
        }

        function changeTag(tag) {
            if (!activeText || HEADING_TAGS.indexOf(activeText.tagName) === -1 || activeText.tagName === tag.toUpperCase()) {
                return;
            }
            const replacement = document.createElement(tag);
            Array.from(activeText.attributes).forEach((attr) => replacement.setAttribute(attr.name, attr.value));
            while (activeText.firstChild) {
                replacement.appendChild(activeText.firstChild);
            }
            activeText.replaceWith(replacement);
            activeText = replacement;
            select(replacement);
            focusText(replacement, false);
            schedulePost();
            syncBar();
        }

        function fontPx(node) {
            const px = parseFloat(window.getComputedStyle(node).fontSize || '16');
            return isNaN(px) ? 16 : Math.round(px);
        }

        function nearest(steps, value) {
            let best = 0;
            steps.forEach((step, i) => {
                if (Math.abs(step - value) < Math.abs(steps[best] - value)) {
                    best = i;
                }
            });
            return best;
        }

        function applyFontSize(px) {
            if (!activeText) {
                return;
            }
            restoreRange();
            const sel = window.getSelection();
            if (sel && sel.rangeCount && !sel.isCollapsed && activeText.contains(sel.anchorNode)) {
                // Wrap only the selected text in a sized span.
                const range = sel.getRangeAt(0);
                const span = document.createElement('span');
                span.style.fontSize = px + 'px';
                try {
                    span.appendChild(range.extractContents());
                    range.insertNode(span);
                    sel.removeAllRanges();
                    const after = document.createRange();
                    after.selectNodeContents(span);
                    sel.addRange(after);
                    savedRange = after.cloneRange();
                } catch (e) {
                    activeText.style.fontSize = px + 'px';
                }
            } else {
                activeText.style.fontSize = px + 'px';
            }
            schedulePost();
            syncBar();
        }

        function applyTextAlign(align) {
            if (!activeText) {
                return;
            }
            activeText.style.textAlign = align === 'left' ? '' : align;
            schedulePost();
            syncBar();
        }

        /* Enter splits the block like a normal editor; Shift+Enter keeps a line break. */
        function handleEnter(event) {
            const block = event.target;
            if (!block || block.getAttribute('data-lb-text') !== '1' || event.shiftKey) {
                return;
            }
            const tag = block.tagName;
            event.preventDefault();
            if (HEADING_TAGS.indexOf(tag) === -1 && tag !== 'LI') {
                // Quotes, captions, table cells…: a line break instead of a new block.
                document.execCommand('insertLineBreak');
                schedulePost();
                return;
            }

            if (tag === 'LI' && isBlank(block)) {
                // Enter on an empty list item leaves the list.
                const list = block.closest('ul,ol');
                const p = el('p');
                p.appendChild(document.createElement('br'));
                list.parentNode.insertBefore(p, list.nextSibling);
                block.remove();
                if (!list.querySelector('li')) {
                    list.remove();
                }
                afterSplit(p);
                return;
            }

            const sel = window.getSelection();
            if (!sel.rangeCount) {
                return;
            }
            const range = sel.getRangeAt(0);
            range.deleteContents();
            const tail = document.createRange();
            tail.setStart(range.startContainer, range.startOffset);
            tail.setEnd(block, block.childNodes.length);
            const rest = tail.extractContents();

            const next = document.createElement(tag === 'LI' ? 'li' : 'p');
            if (tag === 'P' && block.style.textAlign) {
                next.style.textAlign = block.style.textAlign;
            }
            next.appendChild(rest);
            if (isBlank(next)) {
                next.innerHTML = '<br>';
            }
            if (isBlank(block)) {
                block.innerHTML = '<br>';
            }
            block.parentNode.insertBefore(next, block.nextSibling);
            afterSplit(next, true);
        }

        function afterSplit(node, atStart) {
            makeEditable(node);
            activeText = node;
            select(node);
            node.focus();
            const range = document.createRange();
            range.selectNodeContents(node);
            range.collapse(!!atStart);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            schedulePost();
            syncBar();
        }

        /* Backspace in an empty block removes it and jumps to the previous one. */
        function handleBackspace(event) {
            const block = event.target;
            if (!block || block.getAttribute('data-lb-text') !== '1' || !isBlank(block)) {
                return;
            }
            const editable = Array.from(document.querySelectorAll('[data-lb-text]'));
            const index = editable.indexOf(block);
            const previous = index > 0 ? editable[index - 1] : null;
            if (!previous) {
                return;
            }
            event.preventDefault();
            const list = block.tagName === 'LI' ? block.closest('ul,ol') : null;
            block.remove();
            if (list && !list.querySelector('li')) {
                list.remove();
            }
            activeText = previous;
            select(previous);
            focusText(previous, false);
            schedulePost();
        }

        /* ---------- images ---------- */

        function imageWidthPct(img) {
            const width = (img.style && img.style.width) || '';
            if (width.indexOf('%') !== -1) {
                return Math.max(10, Math.min(100, parseInt(width, 10) || 100));
            }
            const parent = img.parentElement;
            if (parent && parent.clientWidth > 0) {
                return Math.max(10, Math.min(100, Math.round((img.getBoundingClientRect().width / parent.clientWidth) * 100) || 100));
            }
            return 100;
        }

        function imageAlign(img) {
            const ml = img.style.marginLeft;
            const mr = img.style.marginRight;
            if (ml === '0px' || ml === '0') {
                return 'left';
            }
            if ((mr === '0px' || mr === '0') && ml === 'auto') {
                return 'right';
            }
            return 'center';
        }

        function applyImageWidth(pct) {
            if (!activeImage) {
                return;
            }
            pct = Math.max(10, Math.min(100, parseInt(pct, 10) || 100));
            activeImage.style.width = pct + '%';
            schedulePost();
            syncBar();
        }

        function applyImageAlign(align) {
            if (!activeImage) {
                return;
            }
            activeImage.style.marginLeft = align === 'left' ? '0' : align === 'right' ? 'auto' : '';
            activeImage.style.marginRight = align === 'right' ? '0' : align === 'left' ? 'auto' : '';
            schedulePost();
            syncBar();
        }

        function findImage(id) {
            return id ? document.querySelector('img[data-lb-img-id="' + String(id).replace(/"/g, '') + '"]') : null;
        }

        /* ---------- toolbar ---------- */

        function syncBar() {
            const mode = bar.getAttribute('data-mode');
            bar.querySelector('[data-position]').textContent = position === 'before' ? 'Insert ↑' : 'Insert ↓';

            if (mode === 'image' && activeImage) {
                bar.querySelector('[data-img-width]').value = String(WIDTH_STEPS[nearest(WIDTH_STEPS, imageWidthPct(activeImage))]);
                const align = imageAlign(activeImage);
                bar.querySelectorAll('[data-img-align]').forEach((btn) => btn.setAttribute('data-active', btn.getAttribute('data-img-align') === align ? '1' : '0'));
                return;
            }

            if (mode !== 'text' || !activeText) {
                return;
            }

            const tagSelect = bar.querySelector('[data-tag]');
            const tag = activeText.tagName.toLowerCase();
            const canRetag = HEADING_TAGS.indexOf(activeText.tagName) !== -1;
            tagSelect.hidden = !canRetag;
            if (canRetag) {
                if (!tagSelect.querySelector('option[value="' + tag + '"]')) {
                    tagSelect.appendChild(new Option(tag.toUpperCase(), tag));
                }
                tagSelect.value = tag;
            }

            bar.querySelectorAll('[data-cmd]').forEach((btn) => {
                let on = false;
                try {
                    on = document.queryCommandState(btn.getAttribute('data-cmd'));
                } catch (e) { /* not supported */ }
                btn.setAttribute('data-active', on ? '1' : '0');
            });

            const align = activeText.style.textAlign || 'left';
            bar.querySelectorAll('[data-text-align]').forEach((btn) => btn.setAttribute('data-active', btn.getAttribute('data-text-align') === align ? '1' : '0'));

            const size = bar.querySelector('[data-font-size]');
            size.value = String(fontPx(activeText));
        }

        bar.addEventListener('mousedown', (event) => {
            saveRange();
            // Keep the text selection while clicking buttons; dropdowns still need to open.
            if (!event.target.closest('select')) {
                event.preventDefault();
            }
        });

        bar.addEventListener('click', (event) => {
            const btn = event.target.closest('button');
            if (!btn) {
                return;
            }

            if (btn.hasAttribute('data-position')) {
                position = position === 'before' ? 'after' : 'before';
                syncBar();
                status(position === 'before' ? 'New blocks will be inserted above the selection.' : 'New blocks will be inserted below the selection.');
            } else if (btn.hasAttribute('data-insert')) {
                const kind = btn.getAttribute('data-insert');
                if (kind === 'image') {
                    send({ type: MSG.pickImage, mode: 'insert', position });
                } else {
                    insertBlock(kind, { position });
                }
            } else if (btn.hasAttribute('data-delete')) {
                deleteSelected();
            } else if (btn.hasAttribute('data-cmd')) {
                exec(btn.getAttribute('data-cmd'));
            } else if (btn.hasAttribute('data-link')) {
                const sel = window.getSelection();
                if (!savedRange || savedRange.collapsed || !activeText.contains(savedRange.startContainer)) {
                    const anchor = sel && sel.anchorNode && sel.anchorNode.parentElement ? sel.anchorNode.parentElement.closest('a') : null;
                    if (!anchor) {
                        status('Select the text you want to turn into a link first.');
                        return;
                    }
                    const range = document.createRange();
                    range.selectNodeContents(anchor);
                    savedRange = range;
                }
                const current = savedRange.commonAncestorContainer;
                const link = (current.nodeType === 1 ? current : current.parentElement).closest('a');
                send({ type: MSG.askLink, href: link ? (link.getAttribute('data-lb-href') || '') : '' });
            } else if (btn.hasAttribute('data-size-step')) {
                const index = nearest(SIZE_STEPS, fontPx(activeText)) + parseInt(btn.getAttribute('data-size-step'), 10);
                applyFontSize(SIZE_STEPS[Math.max(0, Math.min(SIZE_STEPS.length - 1, index))]);
            } else if (btn.hasAttribute('data-text-align')) {
                applyTextAlign(btn.getAttribute('data-text-align'));
            } else if (btn.hasAttribute('data-img-step') && activeImage) {
                const index = nearest(WIDTH_STEPS, imageWidthPct(activeImage)) + parseInt(btn.getAttribute('data-img-step'), 10);
                applyImageWidth(WIDTH_STEPS[Math.max(0, Math.min(WIDTH_STEPS.length - 1, index))]);
            } else if (btn.hasAttribute('data-img-align')) {
                applyImageAlign(btn.getAttribute('data-img-align'));
            } else if (btn.hasAttribute('data-img-replace') && activeImage) {
                send({ type: MSG.pickImage, mode: 'replace', id: activeImage.getAttribute('data-lb-img-id') });
            } else if (btn.hasAttribute('data-img-alt') && activeImage) {
                send({ type: MSG.askAlt, id: activeImage.getAttribute('data-lb-img-id'), alt: activeImage.getAttribute('alt') || '' });
            }
        });

        bar.querySelector('[data-tag]').addEventListener('change', (event) => changeTag(event.target.value));
        bar.querySelector('[data-font-size]').addEventListener('change', (event) => {
            if (event.target.value) {
                applyFontSize(parseInt(event.target.value, 10));
            }
        });
        bar.querySelector('[data-img-width]').addEventListener('change', (event) => {
            if (event.target.value) {
                applyImageWidth(event.target.value);
            }
        });

        /* ---------- document events ---------- */

        document.addEventListener('click', (event) => {
            if (inBar(event.target)) {
                return;
            }
            if (event.target.closest('a[href]')) {
                event.preventDefault();
            }
            const img = event.target.closest('img[data-lb-img]');
            if (img) {
                event.preventDefault();
                selectImage(img);
                return;
            }
            activeImage = null;
            const unit = select(event.target);
            if (!unit) {
                hideBar();
                return;
            }
            if (!event.target.closest('[data-lb-text]')) {
                activeText = null;
                setMode('block');
            }
            status('Block selected. Insert above/below it, or Delete it.');
        }, true);

        document.addEventListener('dblclick', (event) => {
            const img = event.target.closest('img[data-lb-img]');
            if (img) {
                event.preventDefault();
                selectImage(img);
                send({ type: MSG.pickImage, mode: 'replace', id: img.getAttribute('data-lb-img-id') });
            }
        }, true);

        document.addEventListener('submit', (event) => event.preventDefault(), true);

        document.addEventListener('focusin', (event) => {
            const target = event.target;
            if (target && target.getAttribute && target.getAttribute('data-lb-text') === '1') {
                activeImage = null;
                activeText = target;
                select(target);
                setMode('text');
            }
        });

        document.addEventListener('focusout', (event) => {
            if (event.target && event.target.getAttribute && event.target.getAttribute('data-lb-text') === '1') {
                postHtml();
            }
        });

        document.addEventListener('input', (event) => {
            if (event.target && event.target.getAttribute && event.target.getAttribute('data-lb-text') === '1') {
                schedulePost();
            }
        });

        document.addEventListener('keydown', (event) => {
            userScrolled = true;
            if (event.key === 'Enter') {
                handleEnter(event);
            } else if (event.key === 'Backspace') {
                handleBackspace(event);
            }
        });

        /* Paste as plain text so Word / web styles don't end up in the article. */
        document.addEventListener('paste', (event) => {
            if (!event.target.closest || !event.target.closest('[data-lb-text]')) {
                return;
            }
            event.preventDefault();
            const text = (event.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });

        document.addEventListener('selectionchange', () => {
            if (activeText) {
                saveRange();
            }
        });
        document.addEventListener('keyup', syncBar);
        document.addEventListener('mouseup', syncBar);

        window.addEventListener('message', (event) => {
            const data = event.data;
            if (event.source !== window.parent || !data || typeof data.type !== 'string') {
                return;
            }

            if (data.type === MSG.insert) {
                insertBlock(data.kind, data);
            } else if (data.type === MSG.deleteBlock) {
                deleteSelected();
            } else if (data.type === MSG.setImage) {
                const img = findImage(data.id);
                if (img && data.url) {
                    img.setAttribute('src', data.url);
                    ['srcset', 'sizes', 'data-src', 'data-srcset'].forEach((attr) => img.removeAttribute(attr));
                    if (!img.getAttribute('alt') && data.alt) {
                        img.setAttribute('alt', data.alt);
                    }
                    selectImage(img);
                    postHtml();
                    status('Image replaced.');
                }
            } else if (data.type === MSG.setAlt) {
                const img = findImage(data.id);
                if (img) {
                    img.setAttribute('alt', data.alt || '');
                    postHtml();
                    status('Alt text updated.');
                }
            } else if (data.type === MSG.setLink) {
                if (!activeText) {
                    return;
                }
                exec(data.url ? 'createLink' : 'unlink', data.url || null);
                postHtml();
                status(data.url ? 'Link saved.' : 'Link removed.');
            }
        });

        /* The parent rebuilds the preview after HTML-field edits; keep the scroll position. */
        let scrollTimer = null;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => send({ type: MSG.scroll, y: window.scrollY || 0 }), 100);
        }, { passive: true });
        window.addEventListener('wheel', () => { userScrolled = true; }, { passive: true });
        window.addEventListener('touchstart', () => { userScrolled = true; }, { passive: true });
        window.addEventListener('load', () => {
            if (!userScrolled && cfg.scrollY > 0) {
                window.scrollTo(0, cfg.scrollY);
            }
        });

        refresh();
        lastPosted = cleanHtml();
        if (cfg.scrollY > 0) {
            window.scrollTo(0, cfg.scrollY);
        }
    }

    function buildSrcdoc(html, scrollY) {
        const content = html && html.trim() !== '' ? html : EMPTY_HINT;
        const cfg = JSON.stringify({ msg: MSG, scrollY: scrollY || 0 }).replace(/</g, '\\u003c');

        return '<!DOCTYPE html><html><head><meta charset="utf-8">' +
            '<meta name="viewport" content="width=device-width, initial-scale=1">' +
            '<link rel="preconnect" href="https://fonts.googleapis.com">' +
            '<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">' +
            '<style>' + ARTICLE_CSS + EDITOR_CSS + '</style>' +
            '</head><body>' + TOOLBAR + content +
            '<script>(' + previewBridge.toString() + ')(' + cfg + ');</scr' + 'ipt>' +
            '</body></html>';
    }

    /* ================= parent page ================= */

    const state = new WeakMap();

    function stateFor(root) {
        if (!state.has(root)) {
            state.set(root, { lastHtml: null, scrollY: 0, pending: null, page: 1, hasMore: false, search: '', syncTimer: null, searchTimer: null });
        }
        return state.get(root);
    }

    function findTextarea(root) {
        const form = root.closest('form') || document;
        return form.querySelector('textarea[data-blog-post-body]');
    }

    function frameOf(root) {
        return root.querySelector('[data-blog-html-preview-frame]');
    }

    function setStatus(root, text) {
        const el = root.querySelector('[data-blog-html-preview-status]');
        if (el) {
            el.textContent = text;
        }
    }

    function postToFrame(root, payload) {
        const frame = frameOf(root);
        if (frame && frame.contentWindow) {
            frame.contentWindow.postMessage(payload, '*');
        }
    }

    function render(root) {
        const ta = findTextarea(root);
        const frame = frameOf(root);
        if (!ta || !frame) {
            return;
        }
        const s = stateFor(root);
        s.lastHtml = ta.value || '';
        frame.srcdoc = buildSrcdoc(s.lastHtml, s.scrollY);
    }

    /* Write the edited HTML into the textarea; the input event updates Livewire's deferred model. */
    function writeTextarea(root, html) {
        const ta = findTextarea(root);
        if (!ta) {
            setStatus(root, 'Could not find the HTML field to update.');
            return;
        }
        const s = stateFor(root);
        s.lastHtml = html;
        if (ta.value === html) {
            return;
        }
        ta.value = html;
        ta.dispatchEvent(new Event('input', { bubbles: true }));
        ta.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    /* ---------- image picker ---------- */

    function picker(root) {
        return root.querySelector('[data-blog-asset-picker]');
    }

    function openPicker(root, request) {
        const s = stateFor(root);
        s.pending = request;
        s.page = 1;
        const el = picker(root);
        el.classList.add('is-open');
        el.querySelector('[data-blog-asset-picker-subtitle]').textContent = request.mode === 'replace'
            ? 'Select an image to replace the current one, or upload a new one.'
            : 'Select an image to insert, or upload a new one.';
        loadAssets(root, 1);
        const search = el.querySelector('[data-blog-asset-picker-search]');
        if (search) {
            search.focus();
        }
    }

    function closePicker(root) {
        picker(root).classList.remove('is-open');
        stateFor(root).pending = null;
    }

    function useImage(root, url, alt) {
        const request = stateFor(root).pending;
        closePicker(root);
        if (!request) {
            return;
        }
        if (request.mode === 'replace') {
            postToFrame(root, { type: MSG.setImage, id: request.id, url, alt });
        } else {
            postToFrame(root, { type: MSG.insert, kind: 'image', url, alt, position: request.position });
            setStatus(root, 'Image inserted.');
        }
    }

    function shortLabel(name) {
        const base = String(name || 'Image');
        return base.length > 28 ? base.slice(0, 14) + '…' + base.slice(-10) : base;
    }

    function renderAssets(root, rows) {
        const grid = picker(root).querySelector('[data-blog-asset-picker-grid]');
        grid.innerHTML = '';

        if (!rows.length) {
            const empty = document.createElement('div');
            empty.className = 'lb-assets__empty';
            empty.textContent = stateFor(root).search ? 'No images match your search.' : 'No images yet. Upload one to get started.';
            grid.appendChild(empty);
            return;
        }

        const wrap = document.createElement('div');
        wrap.className = 'lb-assets__grid';
        rows.forEach((asset) => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'lb-assets__card';
            card.title = asset.name || '';
            const thumb = document.createElement('div');
            thumb.className = 'lb-assets__thumb';
            const img = document.createElement('img');
            img.src = asset.url;
            img.alt = '';
            img.loading = 'lazy';
            thumb.appendChild(img);
            const label = document.createElement('span');
            label.className = 'lb-assets__label';
            label.textContent = shortLabel(asset.name);
            card.appendChild(thumb);
            card.appendChild(label);
            card.addEventListener('click', () => useImage(root, asset.url, asset.name || ''));
            wrap.appendChild(card);
        });
        grid.appendChild(wrap);
    }

    function updatePager(root, page, perPage, total, hasMore, loading) {
        const el = picker(root);
        const s = stateFor(root);
        s.page = page;
        s.hasMore = hasMore;
        const pages = total > 0 ? Math.ceil(total / perPage) : 1;
        el.querySelector('[data-blog-asset-picker-meta]').textContent = loading
            ? 'Loading…'
            : total ? 'Showing ' + ((page - 1) * perPage + 1) + '–' + Math.min(page * perPage, total) + ' of ' + total : 'No images';
        el.querySelector('[data-blog-asset-picker-page]').textContent = 'Page ' + page + ' / ' + pages;
        el.querySelector('[data-blog-asset-picker-prev]').disabled = loading || page <= 1;
        el.querySelector('[data-blog-asset-picker-next]').disabled = loading || !hasMore;
    }

    async function loadAssets(root, page) {
        const perPage = 24;
        const s = stateFor(root);
        const grid = picker(root).querySelector('[data-blog-asset-picker-grid]');
        grid.innerHTML = '<div class="lb-assets__empty">Loading images…</div>';
        updatePager(root, page, perPage, 0, false, true);

        const url = new URL(root.getAttribute('data-assets-url'), window.location.origin);
        url.searchParams.set('page', String(page));
        url.searchParams.set('per_page', String(perPage));
        if (s.search) {
            url.searchParams.set('q', s.search);
        }

        try {
            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(payload.message || 'Could not load images.');
            }
            renderAssets(root, Array.isArray(payload.data) ? payload.data : []);
            updatePager(root, page, perPage, payload.total || 0, !!payload.has_more, false);
        } catch (error) {
            grid.innerHTML = '';
            const message = document.createElement('div');
            message.className = 'lb-assets__empty';
            message.textContent = error.message || 'Could not load images.';
            grid.appendChild(message);
            updatePager(root, page, perPage, 0, false, false);
        }
    }

    async function upload(root, file) {
        const button = picker(root).querySelector('[data-blog-asset-picker-upload]');
        const body = new FormData();
        body.append('image', file);
        button.disabled = true;
        button.textContent = 'Uploading…';
        setStatus(root, 'Uploading image…');

        try {
            const response = await fetch(root.getAttribute('data-upload-url'), {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body,
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok || !payload.url) {
                throw new Error(payload.message || (payload.errors && payload.errors.image && payload.errors.image[0]) || 'Upload failed.');
            }
            useImage(root, payload.url, payload.name || '');
        } catch (error) {
            setStatus(root, error.message || 'Upload failed. Please try again.');
            window.alert(error.message || 'Upload failed. Please try again.');
        } finally {
            button.disabled = false;
            button.textContent = 'Upload new';
        }
    }

    /* ---------- wiring ---------- */

    function on(root, selector, handler) {
        root.querySelectorAll(selector).forEach((el) => el.addEventListener('click', (event) => {
            event.preventDefault();
            handler(event);
        }));
    }

    function bind(root) {
        const s = stateFor(root);
        const ta = findTextarea(root);

        if (root.dataset.lbBound === '1') {
            // Livewire re-rendered the form: only rebuild if the HTML changed outside the preview.
            if (ta && ta.value !== s.lastHtml) {
                render(root);
            }
            return;
        }
        if (!ta) {
            return;
        }
        root.dataset.lbBound = '1';
        render(root);

        ta.addEventListener('input', () => {
            if (ta.value === s.lastHtml) {
                return;
            }
            clearTimeout(s.syncTimer);
            s.syncTimer = setTimeout(() => render(root), 400);
        });

        on(root, '[data-blog-html-preview-insert]', (event) => {
            const kind = event.currentTarget.getAttribute('data-blog-html-preview-insert');
            if (kind === 'image') {
                openPicker(root, { mode: 'insert' });
            } else {
                postToFrame(root, { type: MSG.insert, kind });
            }
        });
        on(root, '[data-blog-html-preview-delete-section]', () => postToFrame(root, { type: MSG.deleteBlock }));
        on(root, '[data-blog-html-preview-reload]', () => {
            render(root);
            setStatus(root, 'Preview reloaded.');
        });

        const el = picker(root);
        const fileInput = root.querySelector('[data-blog-html-preview-file]');
        on(el, '[data-blog-asset-picker-close]', () => closePicker(root));
        on(el, '[data-blog-asset-picker-backdrop]', () => closePicker(root));
        on(el, '[data-blog-asset-picker-upload]', () => fileInput.click());
        on(el, '[data-blog-asset-picker-prev]', () => loadAssets(root, Math.max(1, s.page - 1)));
        on(el, '[data-blog-asset-picker-next]', () => s.hasMore && loadAssets(root, s.page + 1));

        el.querySelector('[data-blog-asset-picker-search]').addEventListener('input', (event) => {
            clearTimeout(s.searchTimer);
            s.searchTimer = setTimeout(() => {
                s.search = event.target.value.trim();
                loadAssets(root, 1);
            }, 300);
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            fileInput.value = '';
            if (file) {
                upload(root, file);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && el.classList.contains('is-open')) {
                closePicker(root);
            }
        });
    }

    function rootForFrame(source) {
        return Array.from(document.querySelectorAll('[data-blog-html-preview]'))
            .find((root) => frameOf(root) && frameOf(root).contentWindow === source) || null;
    }

    window.addEventListener('message', (event) => {
        const data = event.data;
        const root = data && typeof data.type === 'string' ? rootForFrame(event.source) : null;
        if (!root) {
            return;
        }

        if (data.type === MSG.html && typeof data.html === 'string') {
            writeTextarea(root, data.html);
            setStatus(root, 'Changes copied to the HTML field. Remember to save.');
        } else if (data.type === MSG.status) {
            setStatus(root, String(data.text || ''));
        } else if (data.type === MSG.scroll) {
            stateFor(root).scrollY = Number(data.y) || 0;
        } else if (data.type === MSG.pickImage) {
            openPicker(root, { mode: data.mode === 'replace' ? 'replace' : 'insert', id: data.id, position: data.position });
        } else if (data.type === MSG.askLink) {
            const url = window.prompt('Link URL (leave empty to remove the link):', data.href || 'https://');
            if (url !== null) {
                postToFrame(root, { type: MSG.setLink, url: url.trim() === 'https://' ? '' : url.trim() });
            }
        } else if (data.type === MSG.askAlt) {
            const alt = window.prompt('Image alt text (describe the image for SEO and screen readers):', data.alt || '');
            if (alt !== null) {
                postToFrame(root, { type: MSG.setAlt, id: data.id, alt: alt.trim() });
            }
        }
    });

    function boot() {
        document.querySelectorAll('[data-blog-html-preview]').forEach(bind);
    }

    let hooked = false;
    function hookLivewire() {
        if (!hooked && window.Livewire && typeof window.Livewire.hook === 'function') {
            hooked = true;
            window.Livewire.hook('message.processed', boot);
        }
    }

    hookLivewire();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
    document.addEventListener('livewire:load', () => {
        hookLivewire();
        boot();
    });
})();
