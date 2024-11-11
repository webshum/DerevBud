const URL = import.meta.env.VITE_API_URL;
const API_URL_GET_PRODUCTS = URL + '/wp-json/wc/store/v1/products';
const API_URL_GET_ATTRIBUTES = URL + '/wp-json/derevbud/v1/attributes';
const API_URL_GET_PRICE = URL + '/wp-json/derevbud/v1/price';

const header = () => {
	const header = document.getElementById('header');
	const wrapper = document.querySelector('.wrapper');
	const btnPhone = document.querySelector('.head-phones > a');
	const nav = document.querySelector('.nav');
	const menu = nav.querySelector('.menu');
	const burger = header.querySelector('.btn-nav');

	wrapper.style.paddingTop = `${header.offsetHeight}px`;

	window.addEventListener('resize', e => {
		wrapper.style.paddingTop = `${header.offsetHeight}px`;
	});

	btnPhone.addEventListener('click', e => {
		e.preventDefault();

		const drop = e.target.closest('.head-phones');
		const close = drop.querySelector('.btn-back');

		drop.classList.toggle('open');
		close.onclick = e => drop.classList.remove('open');
	});

	burger.addEventListener('click', e => {
		document.body.classList.toggle('nav-active');
	});

	if (window.innerWidth < 991) {
		const parentLi = nav.querySelectorAll('.menu-item-has-children');

		for (const li of parentLi) {
			li.querySelector('a').addEventListener('click', e => {
				e.preventDefault();

				li.classList.add('open');
				menu.classList.add('active');
			});

			li.querySelector('.sub-menu li:first-child .prev').addEventListener('click', e => {
				e.preventDefault();
				console.log(li.closest('.menu'));

				li.classList.remove('open');
				menu.classList.remove('active');
			});
		}
	}
}

const popup = () => {
    let btn = document.querySelectorAll('.btn-popup');
    let overlay = document.querySelector('.popup-overlay');
    let popup = null;
    let close = null;
    let _this = this;

    for (let i = 0; i < btn.length; i++) {
        btn[i].addEventListener('click', function(e) {
            e.preventDefault();

            popup = document.querySelector('.popup-' + this.getAttribute('data-popup'));
            close = popup.querySelector('.popup-close');

            let top  = window.pageYOffset || document.documentElement.scrollTop,
            left = window.pageXOffset || document.documentElement.scrollLeft;
            
            overlay.classList.add('active');
            popup.classList.add('active');
            popup.style.top = (top + 100) + 'px';

            close.addEventListener('click', closePopup);
            overlay.addEventListener('click', closePopup);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.keyCode == 27) closePopup(e);
    });

    function closePopup(e) {
        e.preventDefault();
        overlay.classList.remove('active');
        popup.classList.remove('active');
    }
}

const formAjax = () => {
    const forms = document.querySelectorAll('.ajax-form');
    const popupSuccess = document.querySelector('.popup-success');
    const popupOverlay = document.querySelector('.popup-overlay');
    const dur = 300;

    forms.forEach(form => {
    	form.addEventListener('submit', e => {
    		e.preventDefault();

	        const title = e.target.title ? e.target.title.value : '';
	        const project = e.target.project ? e.target.project.value : '';
	        const phone = e.target.phone ? e.target.phone.value : '';
	        const email = e.target.email ? e.target.email.value : '';
	        const message = e.target.message ? e.target.message.value : '';
	        const complectation = e.target.complectation ? e.target.complectation.value : '';
	        const link = window.location.href;
	        const slug = window.location.pathname;
	        const data = `title=${title}&project=${project}&phone=${phone}&email=${email}&message=${message}&complectation=${complectation}&slug=${slug}&link=${link}&action=send`;
	        
	        submitForm(e.target, data);
    	});
    });
}

function submitForm(form, data) {
    form.classList.add('preload');
    const submitName = form.querySelector('[type="submit"]').value;
    const popupOverlay = document.querySelector('.popup-overlay');
    const popupSuccess = document.querySelector('.popup-success');

    form.querySelector('[type="submit"]').value = '...';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', ajax_url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(data);

    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            form.classList.remove('preload');
            form.reset();
            form.querySelector('[type="submit"]').value = submitName;

            if (document.querySelector('.popup.active') != null) {
                form.closest('.popup').classList.remove();
            }

            popupOverlay.classList.add('active');
            popupSuccess.classList.add('active');
            
            popupSuccess.querySelector('.popup-close').addEventListener('click', e => {
            	e.preventDefault();

            	popupOverlay.classList.remove('active');
            	popupSuccess.classList.remove('active');
            });
        }
    }

    xhr.onerror = () => {
        console.log('Network error!');
    }
}

const lazyLoad = () => {
	const lazyImages = document.querySelectorAll('img.lazy');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(image => {
            imageObserver.observe(image);
        });
    } else {
        lazyImages.forEach(img => {
            img.src = img.getAttribute('data-src');
        });
    }
}

const fetchProducts = async (data) => {
	try {
		const params = new URLSearchParams(data).toString();
		const url = `${API_URL_GET_PRODUCTS}?${params}`;

		const response = await fetch(url);

		if (response.ok) {
			const json = await response.json();
			return json;
		}
	} catch (error) {
		console.log('Fetch error get_products: ', error);
	}
}

const fetchAttributes = async () => {
	try {
		const response = await fetch(API_URL_GET_ATTRIBUTES);

		if (response.ok) {
			const json = await response.json();
			return json;
		}
	} catch (error) {
		console.error('Fetch error get_attributes: ', error);
	}
}

const fetchPrice = async (id) => {
	try {
		const response = await fetch(API_URL_GET_PRICE + `?id=${id}`);

		if (response.ok) {
			const json = await response.json();
			return json;
		}
	} catch (error) {
		console.error('Fetch error get_price: ', error);
	}
}

export {
	header,
	popup,
	formAjax,
	lazyLoad,
	fetchProducts,
	fetchAttributes,
	fetchPrice
};