<script setup>
import { defineProps, ref, onMounted, watch } from 'vue';
import { fetchProducts, getCookie } from '../helpers.js';
import Product from './Product.vue';

const lang = getCookie('pll_language');
const perPage = import.meta.env.VITE_API_PAR_PAGE;
const flagLoading = ref(true);
const products = ref([]);
const loadingProducts = ref([]);
const isLoading = ref(false);
const imagesLoaded = ref(0);

const props = defineProps({
	filters: {
		type: Object,
		default: {}
	},
	catId: {
		type: String,
		default: 0
	}
});

const query = ref({
	lang: lang,
	per_page: perPage,
	page: 1,
	orderby: 'title',
	order: 'asc',
	category: props.catId
});

async function fetchProductsData(query) {
	if (!flagLoading.value) return false;
	isLoading.value = true;

	const result = await fetchProducts(query);

	if (!result.length) flagLoading.value = false;
	
	products.value = [...products.value, ...result];

	isLoading.value = false;
} 

async function onScroll() {
	const documentHeight = document.documentElement.scrollHeight;

	if (scrollY + window.innerHeight >= documentHeight - 1000 && !isLoading.value) {
        query.value.page++;

		await fetchProductsData(query.value);
    }
}

onMounted(() => {
	fetchProductsData(query.value);
	window.addEventListener('scroll', onScroll);
});

watch(() => ({ ...props.filters }), (newFilters, oldFilters) => {
	query.value = newFilters;

	query.value.page = 1;
    flagLoading.value = true;
    products.value = [];

    fetchProductsData(query.value);
}, { deep: true });
</script>

<template>
	<div 
		class="all-products" 
		:class="{'is-loading': isLoading}"
		v-if="products.length"
	>
		<div class="box-product" v-for="(product, index) in products" :key="index">
			<Product :product="product"/>
		</div>
	</div>
</template>