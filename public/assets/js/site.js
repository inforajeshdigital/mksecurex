/* MK Securex – public site interactions (vanilla JS, no jQuery) */
(function () {
    'use strict';

    // Fit the homepage banner in the first screen: measure top bar + header height
    const heroEl = document.querySelector('.hero');
    if (heroEl) {
        const setHeroOffset = () => {
            const top = Math.round(heroEl.getBoundingClientRect().top + window.scrollY);
            document.documentElement.style.setProperty('--hero-offset', top + 'px');
        };
        setHeroOffset();
        window.addEventListener('resize', setHeroOffset);
        window.addEventListener('load', setHeroOffset);
    }

    // Sticky header shadow + back-to-top
    const header = document.querySelector('.site-header');
    const backTop = document.querySelector('.back-top');
    const onScroll = () => {
        const y = window.scrollY;
        header && header.classList.toggle('scrolled', y > 30);
        backTop && backTop.classList.toggle('show', y > 600);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    backTop && backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Mobile off-canvas menu
    const collapse = document.getElementById('mainNav');
    const backdrop = document.querySelector('.menu-backdrop');
    const toggleMenu = (open) => {
        if (!collapse) return;
        collapse.classList.toggle('show', open);
        backdrop && backdrop.classList.toggle('show', open);
        document.body.style.overflow = open ? 'hidden' : '';
    };
    document.querySelectorAll('[data-menu-open]').forEach(b => b.addEventListener('click', () => toggleMenu(true)));
    document.querySelectorAll('[data-menu-close]').forEach(b => b.addEventListener('click', () => toggleMenu(false)));
    backdrop && backdrop.addEventListener('click', () => toggleMenu(false));

    // Hero slider
    document.querySelectorAll('[data-hero]').forEach(hero => {
        const slides = [...hero.querySelectorAll('.hero-slide')];
        const dots = [...hero.querySelectorAll('.hero-dots button')];
        if (slides.length < 2) return;
        let i = 0, timer;
        const go = (n) => {
            slides[i].classList.remove('active'); dots[i] && dots[i].classList.remove('active');
            i = (n + slides.length) % slides.length;
            slides[i].classList.add('active'); dots[i] && dots[i].classList.add('active');
        };
        const play = () => { clearInterval(timer); timer = setInterval(() => go(i + 1), 6500); };
        dots.forEach((d, n) => d.addEventListener('click', () => { go(n); play(); }));
        hero.querySelector('[data-prev]')?.addEventListener('click', () => { go(i - 1); play(); });
        hero.querySelector('[data-next]')?.addEventListener('click', () => { go(i + 1); play(); });
        hero.addEventListener('mouseenter', () => clearInterval(timer));
        hero.addEventListener('mouseleave', play);
        let sx = null;
        hero.addEventListener('touchstart', e => sx = e.touches[0].clientX, { passive: true });
        hero.addEventListener('touchend', e => { if (sx === null) return; const dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 50) { go(i + (dx < 0 ? 1 : -1)); play(); } sx = null; });
        play();
    });

    // Reveal on scroll + counters
    const animateCount = (el) => {
        const target = parseFloat(el.dataset.count) || 0;
        const dur = 1600, start = performance.now();
        const step = (t) => {
            const p = Math.min((t - start) / dur, 1);
            el.textContent = Math.floor(target * (1 - Math.pow(1 - p, 3))).toLocaleString('en-IN');
            if (p < 1) requestAnimationFrame(step); else el.textContent = target.toLocaleString('en-IN');
        };
        requestAnimationFrame(step);
    };
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.classList.add('in');
                if (e.target.dataset.count !== undefined) animateCount(e.target);
                io.unobserve(e.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.reveal, [data-count]').forEach(el => io.observe(el));
    } else {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('in'));
    }

    // Testimonial slider arrows
    document.querySelectorAll('[data-track]').forEach(wrap => {
        const track = wrap.querySelector('.testi-track');
        const by = () => track.firstElementChild ? track.firstElementChild.getBoundingClientRect().width + 24 : 300;
        wrap.querySelector('[data-prev]')?.addEventListener('click', () => track.scrollBy({ left: -by(), behavior: 'smooth' }));
        wrap.querySelector('[data-next]')?.addEventListener('click', () => track.scrollBy({ left: by(), behavior: 'smooth' }));
    });

    // AJAX enquiry forms (graceful fallback to normal POST)
    document.querySelectorAll('form[data-ajax-enquiry]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('[type=submit]');
            const box = form.querySelector('.form-result');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback.js').forEach(el => el.remove());
            if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
            const label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending…';
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    box.innerHTML = '<div class="alert alert-success form-alert mb-3"><i class="fa-solid fa-circle-check me-2"></i>' + escapeHtml(data.message) + '</div>';
                    form.reset(); form.classList.remove('was-validated');
                } else if (res.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([name, msgs]) => {
                        const field = form.querySelector('[name="' + name + '"]');
                        if (!field) return;
                        field.classList.add('is-invalid');
                        const fb = document.createElement('div');
                        fb.className = 'invalid-feedback js d-block';
                        fb.textContent = msgs[0];
                        (field.closest('.form-check') || field).insertAdjacentElement('afterend', fb);
                    });
                    box.innerHTML = '<div class="alert alert-danger form-alert mb-3">Please correct the highlighted fields.</div>';
                } else if (res.status === 429) {
                    box.innerHTML = '<div class="alert alert-warning form-alert mb-3">Too many attempts. Please wait a minute and try again.</div>';
                } else {
                    box.innerHTML = '<div class="alert alert-danger form-alert mb-3">Something went wrong. Please try again or call us.</div>';
                }
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {
                form.removeAttribute('data-ajax-enquiry');
                form.submit();
            } finally {
                btn.disabled = false;
                btn.innerHTML = label;
            }
        });
    });

    function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
})();
