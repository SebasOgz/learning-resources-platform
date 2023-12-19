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
            category_id: category_id.value, 
        })
        .then((response) => {
            window.location.href = "/"; 
        })
        .catch((error) => {
            alert(error.message);
        });
}

</script>

<template>
<div class="m-8">
    <label class="p-4 font-semibold">Título</label>
    <input type="text" placeholder="Título del recurso" v-model="title">
    <label class="p-4 font-semibold">Descripción</label>
    <input type="text" placeholder="Descripción del recurso" v-model="description">
    <label class="p-4 font-semibold">Link</label>
    <input type="text" placeholder="Link del recurso" v-model="link">
    <label class="p-4 font-semibold">Categoría</label>
    <select v-model="category_id">
        <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
        </option>
    </select>
    <button @click="createResource" class="m-8 py-2 px-4 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">Crear Recurso</button>
</div>
</template>