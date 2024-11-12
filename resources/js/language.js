import { createI18n } from 'vue-i18n';

function getCookie(name) {
	const value = `; ${document.cookie}`;
	const parts = value.split(`; ${name}=`);
	if (parts.length === 2) return parts.pop().split(';').shift();
	return null;
}

const locale = getCookie('pll_language') || 'ru';

const messages = {
	ru: {
	    readmore: 'Подробнее',
	    filter: 'Фильтры',
	},
	ua: {
	    readmore: 'Детальніше',
	    filter: 'Фільтри',
	}
};

const i18n = createI18n({
	locale: locale,
	messages,
});

export default i18n;