import { createI18n } from 'vue-i18n';

// Функція для отримання значення куки за назвою
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
	},
	ua: {
	    readmore: 'Детальніше',
	}
};

console.log(locale);

const i18n = createI18n({
	locale: locale,
	messages,
});

export default i18n;