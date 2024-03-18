import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('.toggle-container').forEach(container => {
    container.addEventListener('click', () => toggleContent(container));
});

function toggleContent(container) {
    const initialText = container.querySelector('.initial-text');
    const content = container.querySelector('.content');
    const button = container.querySelector('.toggle-button');

    if (initialText.classList.contains('hidden')) {
        content.classList.add('hidden');
        initialText.classList.remove('hidden');
        button.classList.add('rotate-180');
    } else {
        initialText.classList.add('hidden');
        content.classList.remove('hidden');
        button.classList.remove('rotate-180');
    }
}