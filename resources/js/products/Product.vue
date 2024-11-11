<script setup>
import { defineProps, onMounted, ref } from 'vue';
import { fetchPrice } from '../helpers.js';

const price = ref(null);
const props = defineProps({
	product: {
		type: Object,
		default: {},
		required: true
	}
});

async function fetchPriceData(id) {
	const result = await fetchPrice(id);
	price.value = result;
}

onMounted(() => {
	fetchPriceData(props.product.id);
});
</script>

<template>
	<a 
		:href="product.permalink" 
		target="_blank" 
		class="image" 
		v-if="product.images[0] !== null"
	>	
		<img :src="`${product.images[0].thumbnail}`" loading="lazy" alt="">
	</a>

	<div class="foot">
		<div class="flex">
			<h2>{{ product.name }}</h2>

			<div style="overflow: hidden;" v-html="price"></div>
		</div>

		<a :href="product.permalink" target="_blank" class="btn-more">
			<span>{{ $t('readmore') }}</span>
			<svg width="10" height="10"><use xlink:href="#arr"></use></svg>
		</a>
	</div>
</template>