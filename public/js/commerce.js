(() => {
    'use strict';
    const bankImage = document.querySelector('[data-bank-qr-image]');
    if (bankImage) bankImage.addEventListener('error', () => { bankImage.hidden = true; bankImage.nextElementSibling.hidden = false; });
    const qrTarget = document.querySelector('[data-order-qr]');
    if (qrTarget && typeof qrcode === 'function') {
        try {
            const code = qrcode(0, 'M');
            code.addData(qrTarget.dataset.qrValue, 'Byte');
            code.make();
            qrTarget.innerHTML = code.createSvgTag({cellSize: 4, margin: 16, scalable: true});
            const download = document.querySelector('[data-download-qr]');
            if (download) {
                download.disabled = false;
                download.addEventListener('click', () => {
                    const svg = qrTarget.querySelector('svg');
                    const blob = new Blob([new XMLSerializer().serializeToString(svg)], {type: 'image/svg+xml'});
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'english-center-qr.svg';
                    link.click();
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                });
            }
        } catch (_) { qrTarget.textContent = 'Không tạo được QR. Hãy mở bước xác nhận ở liên kết bên dưới.'; }
    }
    document.querySelectorAll('[data-copy-value]').forEach(button => button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyValue);
            button.textContent = 'Đã chép';
        } catch (_) { button.textContent = 'Hãy mở liên kết QR bên dưới'; }
    }));
    const status = document.querySelector('[data-order-status-url]');
    if (!status || !status.dataset.orderStatusUrl) return;
    let timer;
    async function checkStatus() {
        if (document.hidden) return;
        try {
            const response = await fetch(status.dataset.orderStatusUrl, {headers: {Accept: 'application/json'}, cache: 'no-store', credentials: 'same-origin'});
            if (!response.ok) { clearInterval(timer); return; }
            const result = await response.json();
            if (result.status === 'paid') { clearInterval(timer); location.reload(); }
        } catch (_) { /* Retry while the private order page remains open. */ }
    }
    timer = setInterval(checkStatus, 5000);
    addEventListener('pagehide', () => clearInterval(timer), {once: true});
    document.addEventListener('visibilitychange', () => { if (!document.hidden) checkStatus(); });
})();
