<template>
  <Head title="Detalle Movimiento" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>
      <template v-else>
        <div class="card">
          <addDetailisMovement 
            :movement="movement" 
            @reload-details="reloadDetailsList"
          />
          <listDetailisMovement 
            ref="listDetailisMovementRef"
            :movement="movement" 
          />
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import addDetailisMovement from './Desarrollo/addDetailisMovement.vue';
import listDetailisMovement from './Desarrollo/listDetailisMovement.vue';

// Obtener props de Inertia
const { props } = usePage();
const movement = props.movement;
const isLoading = ref(true);
const listDetailisMovementRef = ref(null);

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});

const reloadDetailsList = () => {
  if (listDetailisMovementRef.value) {
    listDetailisMovementRef.value.reloadDetails();
  }
};
</script>