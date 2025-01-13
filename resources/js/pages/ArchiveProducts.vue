<script setup>
import { ref, watch, defineProps } from 'vue';
import Filters from '../products/Filters.vue';
import Products from '../products/Products.vue';

const filters = ref([]);

const props = defineProps({
	catId: {
		type: String,
		default: 0
	}
});

function onFilters(event) {
	filters.value = event;
}
</script>

<template>
	<div class="grid-products">
		<Filters @filters="onFilters" :cat-id="catId"/>
		<Products :filters="filters" :cat-id="catId"/>
	</div>
</template>