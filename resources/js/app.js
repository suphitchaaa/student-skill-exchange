import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-transition-form]');

    if (!form) {
        return;
    }

    const confirmation = form.dataset.confirm;
    if (confirmation && !window.confirm(confirmation)) {
        event.preventDefault();
        return;
    }

    const button = form.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.textContent = 'กำลังบันทึก...';
    }
});
