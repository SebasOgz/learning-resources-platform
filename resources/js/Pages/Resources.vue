<script setup>
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, defineProps, ref, watch } from 'vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    resources: {
        type: Array,
    },
    categories: {
        type: Array,
    },
    voterId: {
        type: String,
    },
});

let search = ref("");
let filteredResources = ref([])
let filteredCategory = ref(null)

watch(search, (value) => {
    // console.log(value);
    axios.get('/api/resources?search=' + value)
        .then((response) => {
            console.log(response.data, 'resultados de busqueda')
            filteredResources.value = response.data;
        });
});

watch(filteredCategory, (value) => {
    // console.log(value);
    axios.get('/api/resources?category=' + value + '&search=' + search.value)
        .then((response) => {
            console.log(response.data, 'resultados de busqueda')
            filteredResources.value = response.data;
        });
});

onMounted(() => {
    filteredResources.value = props.resources;
}); 

function vote(resourceId) {
    axios.get('/api/vote/' + resourceId).then((response) => {
        filteredResources.value = filteredResources.value.map((resource) => {
            if (resource.id === resourceId) {
                return response.data;
            }

            return resource;
        })
    });
}

function youHaveVoter(resource){
    return resource.votes.find((vote) => vote.code === props.voterId);
}
</script>

<template>
    <Head title="Votación de Recursos" />

    <div
        class="relative min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
            Dashboard</Link>

            <template v-else>
                <Link :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Log in</Link>

                <Link v-if="canRegister" :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Register</Link>
            </template>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-51.5 0 136 23" fill="currentColor" data-slot="icon" class="w-41 h-30">
                    <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                    <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                    <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
                </svg>

            </div>

            <div class="relative overflow-x-auto p-4">
                <div>
                    <input type="text" placeholder="Buscar..." v-model="search">
                    <select v-model="filteredCategory">
                        <option value="">Todas las categorías</option>
                        <option v-for="category in categories" 
                            :key="category.id" 
                            :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <table class="w-full text-sm text-left text-gray-500 border-collapse border border-gray-300">
                    <thead class="text-lg text-white uppercase bg-gray-500">
                        <tr>
                            <th scope="col" class="p-4 border p-2">Votos</th>
                            <th scope="col" class="p-4 border p-2">Recurso</th>
                            <th scope="col" class="p-4 border p-2">Link</th>
                            <th scope="col" class="p-4 border p-2">Categoria</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white ">
                        <tr v-for="resource in filteredResources" :key="resource.id">
                            <th scope="row" class="p-4 text-left border p-2">
                                <div class="flex ">
                                    <span>
                                    {{ resource.votes.length }}
                                    </span>
                                    <button @click="vote(resource.id)">
                                        <svg 
                                            v-if="youHaveVoter(resource)"
                                            xmlns="http://www.w3.org/2000/svg" 
                                            fill="none" 
                                            viewBox="0 0 24 24" 
                                            stroke-width="1.5"
                                            stroke="currentColor" 
                                            data-slot="icon" 
                                            class="w-6 h-6 text-green-500"
                                        >
                                            <path 
                                                stroke-linecap="round" 
                                                stroke-linejoin="round"
                                                d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                        </svg>
                                        <svg 
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg" 
                                            fill="none" 
                                            viewBox="0 0 24 24" 
                                            stroke-width="1.5"
                                            stroke="currentColor" 
                                            data-slot="icon" 
                                            class="w-6 h-6 text-red-500"
                                        >
                                            <path 
                                                stroke-linecap="round" 
                                                stroke-linejoin="round"
                                                d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                                        </svg> 
                                    </button>
                                </div>
                            </th>
                            <th scope="row" class="p-4 text-left border p-2">{{ resource.title }}</th>
                            <th scope="row" class="p-4 border p-2">
                                <a target="_blank" :href="resource.link">Ver recurso</a>
                            </th>
                            <th scope="row" class="p-4 border p-2">{{ resource.category.name }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
