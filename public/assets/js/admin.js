/* MK Securex – Admin panel interactions */
(function ($) {
    'use strict';
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // Sidebar toggle (mobile)
    $(document).on('click', '[data-sidebar-toggle]', () => { $('.a-sidebar, .a-backdrop').toggleClass('show'); });
    $(document).on('click', '.a-backdrop', () => { $('.a-sidebar, .a-backdrop').removeClass('show'); });
    // keep the active sidebar item visible
    const activeLink = document.querySelector('.a-nav a.active');
    activeLink && activeLink.scrollIntoView({ block: 'center' });

    // Toast helper
    window.adminToast = function (msg, type = 'success') {
        const el = $(`<div class="toast-a alert alert-${type} shadow d-flex align-items-center gap-2"><i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i><span></span></div>`);
        el.find('span').text(msg);
        $('body').append(el);
        setTimeout(() => el.fadeOut(300, () => el.remove()), 2600);
    };
    setTimeout(() => $('.alert-auto').fadeOut(400), 5000);

    // Confirm before destructive actions
    $(document).on('submit', 'form[data-confirm]', function (e) {
        if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });

    // Rich text editors
    if ($.fn.summernote) {
        $('textarea.rich').each(function () {
            $(this).summernote({
                height: $(this).data('height') || 220,
                toolbar: [['style', ['style']], ['font', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['insert', ['link', 'picture', 'table', 'hr']], ['view', ['codeview', 'fullscreen']]],
                styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote'],
            });
        });
    }

    // Slug auto-fill
    const slugify = s => s.toString().toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');
    $(document).on('input', '[data-slug-source]', function () {
        const target = $(this.dataset.slugSource);
        if (target.length && !target.data('touched')) target.val(slugify(this.value)).trigger('input');
    });
    $(document).on('input', '[data-slug-target]', function (e) { if (e.originalEvent) $(this).data('touched', true); });

    // Image field: preview + remove + picker
    $(document).on('change', '.img-field input[type=file]', function () {
        const f = this.files && this.files[0];
        if (!f) return;
        const box = $(this).closest('.img-field');
        const url = URL.createObjectURL(f);
        box.find('.preview').html(`<img src="${url}" alt="">`);
        box.find('input[type=checkbox][name$="_remove"]').prop('checked', false);
    });
    $(document).on('change', '.img-field input[name$="_remove"]', function () {
        if (this.checked) $(this).closest('.img-field').find('.preview').html('<i class="fa-regular fa-image"></i>');
    });

    // Media picker modal
    let pickerTarget = null;
    $(document).on('click', '[data-media-pick]', function () {
        pickerTarget = $(this).closest('.img-field');
        const modal = $('#mediaPicker');
        modal.find('.media-grid').html('<div class="text-muted p-3">Loading…</div>');
        bootstrap.Modal.getOrCreateInstance(modal[0]).show();
        loadPicker('');
    });
    function loadPicker(q) {
        $.getJSON(window.ADMIN.pickerUrl, { q }, function (items) {
            const grid = $('#mediaPicker .media-grid').empty();
            if (!items.length) grid.html('<div class="text-muted p-3">No images yet. Upload one below.</div>');
            items.forEach(m => {
                const el = $(`<div class="media-item" title=""><div class="mi-img"></div><div class="mi-cap"></div></div>`);
                el.find('.mi-img').css('background-image', `url('${m.url}')`);
                el.find('.mi-cap').text(m.title || m.path);
                el.attr('title', m.alt || m.title || '');
                el.on('click', () => {
                    pickerTarget.find('input[name$="_path"]').val(m.path);
                    pickerTarget.find('.preview').html(`<img src="${m.url}" alt="">`);
                    pickerTarget.find('input[type=file]').val('');
                    pickerTarget.find('input[name$="_remove"]').prop('checked', false);
                    bootstrap.Modal.getInstance(document.getElementById('mediaPicker')).hide();
                });
                grid.append(el);
            });
        });
    }
    let pickT;
    $(document).on('input', '#mediaPicker [data-picker-search]', function () { clearTimeout(pickT); const v = this.value; pickT = setTimeout(() => loadPicker(v), 300); });
    $(document).on('change', '#mediaPicker [data-picker-upload]', function () {
        const fd = new FormData();
        [...this.files].forEach(f => fd.append('files[]', f));
        fd.append('_token', csrf);
        $.ajax({ url: window.ADMIN.mediaStoreUrl, method: 'POST', data: fd, processData: false, contentType: false, headers: { Accept: 'application/json' } })
            .done(() => { loadPicker(''); adminToast('Uploaded'); })
            .fail(x => adminToast((x.responseJSON && x.responseJSON.message) || 'Upload failed', 'danger'));
        this.value = '';
    });

    // Drag & drop sorting (SortableJS)
    if (window.Sortable) {
        document.querySelectorAll('[data-sortable]').forEach(list => {
            Sortable.create(list, {
                handle: '.drag-handle', animation: 180, ghostClass: 'sortable-ghost',
                onEnd: () => {
                    const ids = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
                    $.post(window.ADMIN.reorderUrl, { _token: csrf, model: list.dataset.sortable, ids })
                        .done(r => adminToast(r.message || 'Order saved'))
                        .fail(() => adminToast('Could not save order', 'danger'));
                }
            });
        });
        document.querySelectorAll('[data-repeater-list]').forEach(list => Sortable.create(list, { handle: '.rr-drag', animation: 150, onEnd: () => renumber(list) }));
    }

    // Repeater
    function renumber(list) {
        $(list).children('.repeater-row').each(function (i) {
            $(this).find('.rr-num').text('Item ' + (i + 1));
        });
    }
    $(document).on('click', '[data-repeater-add]', function () {
        const wrap = $(this).closest('[data-repeater]');
        const list = wrap.find('[data-repeater-list]').first();
        const tpl = wrap.find('template').first().html();
        const idx = Date.now();
        list.append(tpl.replace(/__INDEX__/g, idx));
        renumber(list[0]);
    });
    $(document).on('click', '[data-repeater-remove]', function () {
        const list = $(this).closest('[data-repeater-list]');
        $(this).closest('.repeater-row').remove();
        renumber(list[0]);
    });

    // Character counters
    function counter(el) {
        const max = +el.dataset.counter, len = el.value.length;
        let c = el.parentElement.querySelector('.char-count');
        if (!c) { c = document.createElement('span'); c.className = 'char-count'; el.parentElement.querySelector('label')?.appendChild(c); }
        if (!c) return;
        c.textContent = len + ' / ' + max;
        c.className = 'char-count' + (len > max ? ' bad' : (len > max * .9 || (len > 0 && len < max * .45) ? ' warn' : ''));
    }
    $('[data-counter]').each(function () { counter(this); }).on('input', function () { counter(this); });

    // SEO live preview
    function seoPreview() {
        const box = $('[data-seo-preview]');
        if (!box.length) return;
        const title = $('[name="seo[meta_title]"]').val() || box.data('default-title');
        const desc = $('[name="seo[meta_description]"]').val() || box.data('default-desc');
        box.find('.s-title').text(title);
        box.find('.s-desc').text(desc.length > 160 ? desc.slice(0, 157) + '…' : desc);
        const ogT = $('[name="seo[og_title]"]').val() || title, ogD = $('[name="seo[og_description]"]').val() || desc;
        $('.social-card .sc-title').text(ogT);
        $('.social-card .sc-desc').text(ogD);
        // Recommendations (not blocking)
        const kw = ($('[name="seo[focus_keyword]"]').val() || '').toLowerCase().trim();
        const tips = [];
        const ok = (c, t) => tips.push(`<li class="${c ? 'text-success' : 'text-warning'}"><i class="fa-solid ${c ? 'fa-circle-check' : 'fa-circle-exclamation'} me-1"></i>${t}</li>`);
        ok(title.length >= 30 && title.length <= 65, `Title length ${title.length} (recommended 30–65)`);
        ok(desc.length >= 70 && desc.length <= 160, `Description length ${desc.length} (recommended 70–160)`);
        if (kw) {
            ok(title.toLowerCase().includes(kw), 'Focus keyword appears in title');
            ok(desc.toLowerCase().includes(kw), 'Focus keyword appears in description');
        } else tips.push('<li class="text-muted"><i class="fa-regular fa-circle me-1"></i>Add a focus keyword to get keyword tips</li>');
        $('[data-seo-tips]').html(tips.join(''));
    }
    $(document).on('input', '[name^="seo["]', seoPreview);
    seoPreview();

    // Ajax toggle for section enable/disable
    $(document).on('change', '[data-toggle-url]', function () {
        const el = this;
        $.post(el.dataset.toggleUrl, { _token: csrf }, null, 'json')
            .done(r => { $(el).closest('.section-item').toggleClass('off', !r.active); adminToast(r.active ? 'Section enabled' : 'Section disabled'); })
            .fail(() => { el.checked = !el.checked; adminToast('Could not update', 'danger'); });
    });

    // Select all checkboxes (bulk)
    $(document).on('change', '[data-check-all]', function () { $(this.dataset.checkAll).prop('checked', this.checked); });

    // Media library dropzone
    const dz = document.querySelector('.dropzone');
    if (dz) {
        const input = dz.querySelector('input[type=file]');
        dz.addEventListener('click', e => { if (e.target !== input) input.click(); });
        ['dragenter', 'dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('drag'); }));
        ['dragleave', 'drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('drag'); }));
        dz.addEventListener('drop', e => { input.files = e.dataTransfer.files; dz.closest('form').submit(); });
        input.addEventListener('change', () => dz.closest('form').submit());
    }

    // Section editor: show/hide nothing needed (server renders fields per type)
})(jQuery);
