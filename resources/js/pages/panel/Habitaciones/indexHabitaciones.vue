<template>
  <Head title="Gestion de habitaciones" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>
      <template v-else>
        <div class="card">
          <searcheHabitaciones/>
          <listHabitaciones />
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import listHabitaciones from './Desarrollo/listHabitaciones.vue';
import searcheHabitaciones from './Desarrollo/searcheHabitaciones.vue';
const isLoading = ref(true);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});
</script>