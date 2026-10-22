(() => {
    const trigger = document.querySelector('.account-trigger');
    const panel = document.getElementById('account-panel');
    if (trigger && panel) {
        const close = () => { panel.hidden = true; trigger.setAttribute('aria-expanded', 'false'); };
        trigger.addEventListener('click', () => { const open = panel.hidden; panel.hidden = !open; trigger.setAttribute('aria-expanded', String(open)); });
        document.addEventListener('click', e => { if (!e.target.closest('.account-dropdown')) close(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !panel.hidden) { close(); trigger.focus(); } });
    }
    document.querySelectorAll('[data-teacher-carousel]').forEach(root => {
        const slides = [...root.querySelectorAll('[data-teacher-slide]')];
        const dots = [...root.querySelectorAll('[data-teacher-dot]')];
        const pauseButton = root.querySelector('[data-teacher-pause]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let current = 0, paused = reducedMotion.matches, timer;
        const render = (announce = false) => {
            slides.forEach((slide, index) => {
                const delta = (index - current + slides.length) % slides.length;
                slide.classList.toggle('is-current', delta === 0);
                slide.classList.toggle('is-next', delta === 1);
                slide.classList.toggle('is-previous', delta === slides.length - 1 && delta !== 0 && delta !== 1);
                slide.classList.toggle('is-away', delta > 1 && delta < slides.length - 1);
                slide.querySelector('a').tabIndex = delta === 0 ? 0 : -1;
                dots[index].setAttribute('aria-pressed', String(delta === 0));
            });
            if (announce) root.querySelector('[data-teacher-status]').textContent = slides[current].querySelector('h3').textContent;
            pauseButton.setAttribute('aria-pressed', String(paused));
            pauseButton.setAttribute('aria-label', paused ? 'Tiếp tục chuyển giáo viên' : 'Tạm dừng chuyển giáo viên');
            pauseButton.textContent = paused ? '▶ Tiếp tục' : 'Ⅱ Tạm dừng';
        };
        const stop = () => clearInterval(timer);
        const start = () => { stop(); if (!paused && slides.length > 1 && !document.hidden && !root.contains(document.activeElement) && !root.matches(':hover')) timer = setInterval(() => { current = (current + 1) % slides.length; render(); }, 5000); };
        const move = delta => { current = (current + delta + slides.length) % slides.length; render(true); start(); };
        root.querySelector('[data-teacher-prev]').addEventListener('click', () => move(-1));
        root.querySelector('[data-teacher-next]').addEventListener('click', () => move(1));
        dots.forEach((dot, index) => dot.addEventListener('click', () => { current = index; render(true); start(); }));
        pauseButton.addEventListener('click', () => { paused = !paused; render(); start(); });
        root.addEventListener('mouseenter', stop); root.addEventListener('mouseleave', start);
        root.addEventListener('focusin', stop); root.addEventListener('focusout', () => queueMicrotask(start));
        root.addEventListener('keydown', e => { if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') { e.preventDefault(); move(e.key === 'ArrowRight' ? 1 : -1); } });
        document.addEventListener('visibilitychange', start);
        reducedMotion.addEventListener('change', () => { paused = reducedMotion.matches; render(); start(); });
        render(); start();
    });
})();
