(() => {
    'use strict';

    if (!window.fetch || !window.AbortController) {
        return;
    }

    document.querySelectorAll('[data-live-search]').forEach((section) => {
        const form = section.querySelector('[data-search-form]');
        const results = section.querySelector('[data-search-results]');
        const status = section.querySelector('[data-search-status]');
        const loading = section.querySelector('[data-search-loading]');
        const errorBox = section.querySelector('[data-search-error]');
        const errorMessage = section.querySelector('[data-search-error-message]');
        const endpoint = new URL(form.action, window.location.href);
        let timer;
        let controller;
        let revision = 0;
        let requestedUrl = new URL(window.location.href);

        const invalidate = () => {
            window.clearTimeout(timer);
            controller?.abort();
            controller = null;
            revision += 1;
        };

        const busy = (active) => {
            results.setAttribute('aria-busy', String(active));
            results.classList.toggle('opacity-50', active);
            loading.hidden = !active;
        };

        const formUrl = (page = null) => {
            const url = new URL(endpoint);
            for (const [key, value] of new FormData(form)) {
                const text = String(value).trim();
                if (text !== '') {
                    url.searchParams.set(key, text);
                }
            }
            if (page && page !== '1') {
                url.searchParams.set('page', page);
            }
            return url;
        };

        const syncForm = (url) => {
            for (const control of form.elements) {
                if (control.name) {
                    control.value = url.searchParams.get(control.name) ?? '';
                }
            }
        };

        const search = async (url, historyMode = 'replace', focusResults = false) => {
            if (url.origin !== endpoint.origin || url.pathname !== endpoint.pathname) {
                return;
            }

            invalidate();
            const currentRevision = revision;
            controller = new AbortController();
            requestedUrl = url;
            errorBox.hidden = true;
            busy(true);
            status.textContent = 'Đang tìm kiếm…';

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    signal: controller.signal,
                });
                if (currentRevision !== revision) {
                    return;
                }
                if (response.status === 401) {
                    window.location.assign(section.dataset.loginUrl);
                    return;
                }
                if (response.status === 403) {
                    throw new Error('Bạn không có quyền xem danh sách này.');
                }

                const payload = await response.json();
                if (currentRevision !== revision) {
                    return;
                }
                if (!response.ok) {
                    const errors = Object.values(payload.errors ?? {}).flat();
                    throw new Error(errors[0] ?? 'Không thể tìm kiếm lúc này. Vui lòng thử lại.');
                }
                if (typeof payload.html !== 'string' || !Number.isInteger(payload.total)) {
                    throw new Error('Không tải được danh sách. Vui lòng thử lại.');
                }

                results.innerHTML = payload.html;
                status.textContent = `Tìm thấy ${payload.total} ${section.dataset.itemLabel}.`;
                if (historyMode && url.href !== window.location.href) {
                    const method = historyMode === 'push' ? 'pushState' : 'replaceState';
                    window.history[method](null, '', url);
                }
                if (focusResults) {
                    results.focus({ preventScroll: true });
                }
            } catch (error) {
                if (error.name === 'AbortError' || currentRevision !== revision) {
                    return;
                }
                errorMessage.textContent = error instanceof TypeError
                    ? 'Không kết nối được máy chủ. Hãy kiểm tra kết nối và thử lại.'
                    : error.message;
                errorBox.hidden = false;
                status.textContent = 'Chưa cập nhật được kết quả. Danh sách trước đó vẫn được hiển thị.';
            } finally {
                if (currentRevision === revision) {
                    busy(false);
                    controller = null;
                }
            }
        };

        const schedule = () => {
            invalidate();
            errorBox.hidden = true;
            busy(true);
            status.textContent = 'Đang tìm kiếm…';
            timer = window.setTimeout(() => search(formUrl()), 300);
        };

        form.addEventListener('input', (event) => {
            if (event.target.name === 'keyword' && !event.isComposing) {
                schedule();
            }
        });
        form.addEventListener('compositionstart', () => {
            invalidate();
            busy(false);
        });
        form.addEventListener('compositionend', schedule);
        form.addEventListener('change', (event) => {
            if (event.target instanceof HTMLSelectElement) {
                search(formUrl());
            }
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            search(formUrl());
        });

        section.querySelector('[data-search-reset]').addEventListener('click', (event) => {
            event.preventDefault();
            syncForm(endpoint);
            search(new URL(endpoint), 'push');
        });
        section.querySelector('[data-search-retry]').addEventListener('click', () => {
            search(requestedUrl);
        });
        results.addEventListener('click', (event) => {
            const link = event.target.closest('.pagination a[href]');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }
            event.preventDefault();
            search(formUrl(new URL(link.href).searchParams.get('page')), 'push', true);
        });
        window.addEventListener('popstate', () => {
            const url = new URL(window.location.href);
            if (url.pathname === endpoint.pathname) {
                syncForm(url);
                search(url, null);
            }
        });
    });
})();
