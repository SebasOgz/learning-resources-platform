<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";

let categories = ref([]);
let title = ref("");
let description = ref("");
let link = ref("");
let category_id = ref(null);

onMounted(() => {
    axios.get("api/categories").then((response) => {
        categories.value = response.data;
    });
});

function createResource(){
    axios
        .post("api/resources", {
            title: title.value,
            description: description.value,
            link: link.value,
        })
        .then((response) => {
            console.log(response);
        });
}

</script>

<template>
<div class="m-8">
    <label class="p-4">Titulo</label>
    <input type="text" v-model="title">
    <label class="p-4">Descripción</label>
    <input type="text" v-model="description">
    <label class="p-4">Link</label>
    <input type="text" v-model="link">
    <label class="p-4">Categoría</label>
    <select v-model="category_id">
        <option v-for="category in categories" :key="category.id">
            {{ category.name }}
        </option>
    </select>
    <button @click="createResource" class="m-8 bg-gray-900 text-white">Crear Recurso</button>
</div>
</template>