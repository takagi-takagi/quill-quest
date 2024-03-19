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
    const buttonContainer = container.parentElement.querySelector('.button-container');

    if (initialText.classList.contains('hidden')) {
        content.classList.add('hidden');
        initialText.classList.remove('hidden');
        button.classList.add('rotate-180');
        buttonContainer.classList.add('hidden');
    } else {
        initialText.classList.add('hidden');
        content.classList.remove('hidden');
        button.classList.remove('rotate-180');
        buttonContainer.classList.remove('hidden');
    }
}

document.querySelectorAll('.copyButton').forEach(button => {
    let timerId;
    button.addEventListener('click', function() {
        var parent = this.parentElement;

        var textToCopy = parent.parentElement.querySelector('.content').textContent;

        // テキストをクリップボードにコピー
        navigator.clipboard.writeText(textToCopy).then(() => {
            // コピー成功時の処理
            this.innerText = 'コピーしました！'; // ボタンのテキストを更新
        }).catch(err => {
            // コピー失敗時の処理 
            this.innerText = 'コピーに失敗しました';
        });
        clearTimeout(timerId);
        // 一定時間後にボタンのテキストを元に戻す
        timerId = setTimeout(() => {
            this.innerText ='コピーする';
        }, 2000); 
    });
});