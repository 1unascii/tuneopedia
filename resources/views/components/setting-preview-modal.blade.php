{{--
    Setting Preview Modal
    =====================
    A single shared modal for rendering ABC notation previews.
    Included once in the layout so it works across all pages
    (tunes index, collections index, collection show, etc.)
    without duplicate IDs.

    When a .setting-preview-btn is clicked anywhere on the page,
    JS in modules/settings/show.js opens this modal and renders
    the ABC notation via abcjs into the .setting-preview-render div.
--}}
<div id="setting-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" id="setting-modal-backdrop"></div>
    <div class="card bg-base-200 shadow-lg max-w-2xl w-full mx-4 relative z-10">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold setting-preview-title"></h3>
                <button class="btn btn-ghost btn-xs" id="setting-modal-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="setting-preview-render mt-2"></div>
        </div>
    </div>
</div>
