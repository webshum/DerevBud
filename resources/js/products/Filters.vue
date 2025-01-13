<script setup>
import { ref, onMounted, defineProps, onUnmounted } from 'vue';
import { fetchAttributes } from '../helpers.js';

const attributes = ref(null);
const refFilters = ref(null);
const showFilters = ref(false);
const showButton = ref(false);
const emit = defineEmits(['filters']);
const perPage = import.meta.env.VITE_API_PAR_PAGE;
const props = defineProps({
	catId: {
		type: String,
		default: 0
	}
});

async function fetchAttributesData() {
	attributes.value = await fetchAttributes();
}

onMounted(() => {
	fetchAttributesData();
	handleResize();
	window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
	window.removeEventListener('resize', handleResize);
});

const onChange = async (e) => {
	let data = {};
	const form = document.forms.filters;
	const inputHidden = form.querySelectorAll('.input-hidden');
	const checkboxses = form.querySelectorAll('[type="checkbox"]:checked');

	for (const input of inputHidden) {
		data[input.name] = input.value;
	}

	for (const checkbox of checkboxses) {
		const inputHidden = checkbox.closest('.group').querySelectorAll('[type="hidden"]');

		data[checkbox.name] = checkbox.value;

		for (const input of inputHidden) {
			data[input.name] = input.value;
		}
	}

	emit('filters', data);
}

const handleResize = () => {
	showButton.value = window.innerWidth < 991;
	showFilters.value = window.innerWidth < 991;
};

function onBtnFilters(e) {
	e.preventDefault();
	showFilters.value = true;
	refFilters.value.classList.toggle('active');
}

function onBtnFiltersClose(e) {
	e.preventDefault();
	showFilters.value = false;
	refFilters.value.classList.remove('active');
}
</script>

<template>
	<button class="btn-filters btn" v-if="showButton" @click.prevent="onBtnFilters">
		{{ $t('filter') }}
	</button>

	<form 
		action="#" 
		name="filters" 
		v-show="!showFiters" 
		class="form-filters" 
		ref="refFilters"
	>
		<input class="input-hidden" type="hidden" name="orderby" value="date">
		<input class="input-hidden" type="hidden" name="order" value="desc">
		<input class="input-hidden" type="hidden" name="catalog_visibility" value="catalog">
		<input class="input-hidden" type="hidden" name="per_page" :value="perPage">
		<input class="input-hidden" type="hidden" name="page" value="1">
		<input v-if="Number(catId)" class="input-hidden" type="hidden" name="category" :value="catId">

		<button class="btn-close" v-if="showButton" @click="onBtnFiltersClose">
			<svg><use xlink:href="#close"></use></svg>
		</button>

		<h3>{{ $t('filter') }}</h3>

		<div class="group" v-for="(attribute, key, count) in attributes" :key="count">
			<label class="label" v-for="(term, index) in attribute.terms" :key="index">
				<input 
					type="checkbox" 
					:name="`attributes[${count}][slug][${index}]`" 
					:value="term.slug"
					@change="onChange"
				>
				<div>
					<span>{{term.name}}</span>
					<svg class="ic-arr"><use xlink:href="#arr"></use></svg>
					<svg class="ic-close"><use xlink:href="#close"></use></svg>
				</div>
			</label>

			<input type="hidden" :name="`attributes[${count}][attribute]`" :value="`pa_${attribute.attribute_name}`">
			<input type="hidden" :name="`attributes[${count}][operator]`" value="in">
		</div>
	</form>
</template>