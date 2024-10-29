<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { fetchAttributes } from '../helpers.js';

const attributes = ref(null);
const emit = defineEmits(['filters']);
const perPage = import.meta.env.VITE_API_PAR_PAGE;

async function fetchAttributesData() {
	attributes.value = await fetchAttributes();
}

onMounted(() => {
	fetchAttributesData();
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
</script>

<template>
	<form action="#" name="filters" class="form-filters">
		<input class="input-hidden" type="hidden" name="orderby" value="date">
		<input class="input-hidden" type="hidden" name="order" value="desc">
		<input class="input-hidden" type="hidden" name="catalog_visibility" value="catalog">
		<input class="input-hidden" type="hidden" name="per_page" :value="perPage">
		<input class="input-hidden" type="hidden" name="page" value="1">

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