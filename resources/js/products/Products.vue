<script setup>
import { defineProps, ref, onMounted, watch } from 'vue';
import { fetchProducts } from '../helpers.js';
import Product from './Product.vue';

const perPage = import.meta.env.VITE_API_PAR_PAGE;
const products = ref([]);
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
	per_page: perPage,
	page: 1,
	category: props.catId,
});

async function fetchProductsData(query) {
	isLoading.value = true;
	const result = await fetchProducts(query);
	products.value = result;
	isLoading.value = false;
} 

function onImageLoad() {
    imagesLoaded.value++;

    if (products.value && imagesLoaded.value === products.value.length) {
        isLoading.value = false;
    }
}

async function onScroll() {
	const documentHeight = document.documentElement.scrollHeight;

	if (scrollY + window.innerHeight >= documentHeight - 500 && !isLoading.value) {
        query.value.page++;

        if (query.value.offset != undefined) {
        	query.value.offset = (Number(query.value.offset) + Number(perPage));
        } else {
        	query.value.offset = Number(perPage);
        }

        isLoading.value = true;
		const result = await fetchProducts(query.value);
		products.value = [...products.value, ...result];
		isLoading.value = false;
    }
}

onMounted(() => {
	fetchProductsData(query.value);
	window.addEventListener('scroll', onScroll);
});

watch(() => ({ ...props.filters }), (newFilters, oldFilters) => {
	query.value = newFilters;
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

	<div class="all-products is-loading" v-else>
		<div class="product" v-for="item in 6">
			<a class="image">
				<img src="#" alt="">
			</a>

			<div class="foot">
				<div class="flex">
					<h2>null</h2>
					<div class="price">null</div>
				</div>

				<a class="btn-more">
					<span>null</span>
					<svg width="10" height="10"><use xlink:href="#arr"></use></svg>
				</a>
			</div>
		</div>
	</div>
</template>