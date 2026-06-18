<footer class="w-full mt-20 px-5 pt-16 pb-8 bg-base-300 text-base-content border-t-2 border-primary backdrop-blur-[15px] font-[Fondamento]">
    <div class="max-w-[1100px] mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
        <div>
            <h3 class="text-secondary text-xl mb-5 tracking-widest">Tuneopedia</h3>
            <p class="text-sm text-base-content/60 leading-relaxed">Preserving the spirit of traditional music through a digital repository of tunes that have been passed down through history.</p>
        </div>
        <div>
            <h3 class="text-secondary text-xl mb-5 tracking-widest">Connect</h3>
            <a href="https://github.com/1unascii/tuneopedia" target="_blank" class="text-sm text-base-content/60 hover:text-primary block leading-relaxed">GitHub Repo</a>
            <a href="#" class="text-sm text-base-content/60 hover:text-primary block leading-relaxed">Contact Us</a>
        </div>
    </div>
    <div class="text-center mt-12 pt-5 border-t border-base-content/10 text-xs opacity-60">
        &copy; {{ date('Y') }} Tuneopedia. All rights reserved. |
        <a href="#" class="text-base-content/60 hover:text-primary">Privacy Policy</a> |
        <a href="#" class="text-base-content/60 hover:text-primary">Terms of Use</a>
    </div>
</footer>
<script>
    function applyTheme() {
        var current = localStorage.getItem('tuneopedia-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', current === 'light' ? 'light' : 'dark');
        var icon = document.getElementById('theme-icon');
        if (icon) {
            icon.className = current === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
    }

    function initToggle() {
        var toggle = document.getElementById('theme-toggle');
        if (toggle && !toggle.dataset.initialized) {
            toggle.dataset.initialized = 'true';
            toggle.addEventListener('click', function() {
                var current = localStorage.getItem('tuneopedia-theme') || 'dark';
                current = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('tuneopedia-theme', current);
                applyTheme();
            });
        }
        applyTheme();
    }

    initToggle();
    document.addEventListener('turbo:load', initToggle);
</script>
