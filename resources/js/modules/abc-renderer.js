import abcjs from 'abcjs';

function renderAbcNotation() {
    document.querySelectorAll('.abc-notation[data-abc]').forEach(function(el) {
        if (el.dataset.rendered) return;
        el.dataset.rendered = 'true';
        abcjs.renderAbc(el, el.dataset.abc, {
            responsive: 'resize',
        });
    });
}

renderAbcNotation();
document.addEventListener('turbo:load', renderAbcNotation);
