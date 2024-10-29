<script setup>
import { ref, watch } from 'vue';
import Filters from '../products/Filters.vue';
import Products from '../products/Products.vue';

const filters = ref([]);

function onFilters(event) {
	filters.value = event;
}
</script>

<template>
	<div class="grid-products">
		<Filters @filters="onFilters"/>
		<Products :filters="filters"/>
	</div>
</template>