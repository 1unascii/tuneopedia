function initSettingModal() {
    var modal = document.getElementById('setting-modal');
    if (!modal) return;

    var modalTitle = modal.querySelector('.setting-preview-title');
    var modalRender = modal.querySelector('.setting-preview-render');
    var closeBtn = document.getElementById('setting-modal-close');
    var backdrop = document.getElementById('setting-modal-backdrop');

    function openModal(name, abc) {
        modalTitle.textContent = name;
        modalRender.innerHTML = '';
        modal.classList.remove('hidden');
        window.ABCJS.renderAbc(modalRender, abc, { responsive: 'resize', staffwidth: 500 });
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.querySelectorAll('.setting-preview-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openModal(btn.dataset.settingName, btn.dataset.abc);
        });
    });
}

initSettingModal();
document.addEventListener('turbo:load', initSettingModal);
