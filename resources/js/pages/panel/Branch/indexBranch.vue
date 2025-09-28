<template>
    <Head title="Sucursales" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera />
            </template>
            <template v-else>
                <div class="mb-4">
                    <Breadcrumb :home="home" :model="items" />
                </div>

                <div class="card">
                    <addBranch @agregado="refrescarListado"/>
                    <listBranch :refresh="refreshKey"/>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layout/AppLayout.vue';
import Espera from '@/components/Espera.vue';
import listBranch from './Desarrollo/listBranch.vue';
import addBranch from './Desarrollo/addBranch.vue';
import Breadcrumb from 'primevue/breadcrumb';

const isLoading = ref(true);
const refreshKey = ref(0);

const home = ref({ 
    icon: 'pi pi-home', 
    command: () => router.visit('/dashboard')
});

const items = ref([
    { 
        label: 'Sucursales', 
    }
]);

function refrescarListado() {
    refreshKey.value++;
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});
</script>