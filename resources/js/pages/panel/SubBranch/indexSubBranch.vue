<template>
    <Head title="Sub Sucursales" />
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
                    <addSubBranch 
                        :branch="branch" 
                        @agregado="refrescarListado"
                    />
                    <listSubBranch 
                        :branch="branch"
                        :refresh="refreshKey"
                        :subBranches="subBranches"
                    />
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import addSubBranch from './Desarrollo/addSubBranch.vue';
import listSubBranch from './Desarrollo/listSubBranch.vue';
import Breadcrumb from 'primevue/breadcrumb';

const props = defineProps<{
    branch: Record<string, any>,
    subBranches: Array<any>
}>();

const branch = props.branch;
const subBranches = props.subBranches;

const isLoading = ref(true);
const refreshKey = ref(0);


const home = ref({ 
    icon: 'pi pi-home', 
    command: () => router.visit('/dashboard')
});

const items = ref([
    { 
        label: 'Sucursales', 
        command: () => router.visit('/panel/sucursales')
    },
    { 
        label: branch.name, 
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
