import './bootstrap';
import * as Turbo from '@hotwired/turbo';
import abcjs from 'abcjs';
window.ABCJS = abcjs;

import './modules/tunes/index';
import './modules/settings/show';
import './modules/abc-renderer';
import './modules/tune_form';
import './modules/favorites/toggle';
import './modules/favorites/remove';

// Turbo page transition fade — adds turbo-loading class to body
// during navigation, CSS handles the opacity transition
document.addEventListener('turbo:before-visit', () => {
    document.body.classList.add('turbo-loading');
});
document.addEventListener('turbo:load', () => {
    document.body.classList.remove('turbo-loading');
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
