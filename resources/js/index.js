import '../css/index.css';
import { header, popup, formAjax } from './helpers';
import { createApp } from 'vue/dist/vue.esm-bundler';
import i18n from './language.js';
import ArchiveProducts from './pages/ArchiveProducts.vue';
import SimpleLightbox from 'simple-lightbox';
import 'simple-lightbox/dist/simpleLightbox.min.css';

new SimpleLightbox({elements: '.product-gallery a'});

window.onload = () => {
	header();
	if (document.querySelector('img.lazy') != null) lazyLoad();
	if (document.querySelector('.btn-popup') != null) popup();
	if (document.querySelector('.ajax-form') != null) formAjax();
}

const app = createApp({});
app.component("archive-products", ArchiveProducts);
app.use(i18n);
const mountedApp = app.mount("#archive-products");
