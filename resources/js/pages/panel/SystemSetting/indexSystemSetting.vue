<template>
    <Head title="Configuraciones" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera />
            </template>
            <template v-else>
                <SystemSettingList 
                    :settings="settings" 
                    @update-setting="fetchSettings"
                />
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import SystemSettingList from './Desarrollo/SystemSettingList.vue';
import axios from 'axios';

const isLoading = ref(true);
const settings = ref([]);

const fetchSettings = async () => {
    try {
        const response = await axios.get('/system-settings');
        settings.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
};

onMounted(async () => {
    await fetchSettings();
    isLoading.value = false;
});
</script>
