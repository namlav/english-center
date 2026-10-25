(() => {
    const button = document.querySelector('.nav-toggle');
    const navigation = document.getElementById('site-navigation');
    if (button && navigation) {
        const close = () => {
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Mở menu');
            navigation.classList.remove('is-open');
        };
        button.addEventListener('click', () => {
            const open = button.getAttribute('aria-expanded') !== 'true';
            button.setAttribute('aria-expanded', String(open));
            button.setAttribute('aria-label', open ? 'Đóng menu' : 'Mở menu');
            navigation.classList.toggle('is-open', open);
        });
        navigation.addEventListener('click', (event) => {
            if (event.target.closest('a')) close();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                close();
                button.focus();
            }
        });
        document.addEventListener('click', (event) => {
            if (!navigation.contains(event.target) && !button.contains(event.target)) close();
        });
    }
    const result = document.querySelector('[data-result]');
    if (result) result.focus({ preventScroll: true });
})();
