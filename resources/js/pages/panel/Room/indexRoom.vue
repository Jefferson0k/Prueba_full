<template>
  <Head title="Habitaciones" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>
      <template v-else>
        <!-- Breadcrumb -->
        <div class="mb-4">
          <Breadcrumb :home="home" :model="items" />
        </div>

        <div class="card">
          <addRoom
            :floor="floor"
            @agregado="refrescarListado"
          />
          <listRoom
            :floor="floor"
            :refresh="refreshKey"
            :rooms="rooms"
          />
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Breadcrumb from 'primevue/breadcrumb';
import Espera from '@/components/Espera.vue';
import addRoom from './Desarrollo/addRoom.vue';
import listRoom from './Desarrollo/listRoom.vue';

const props = defineProps<{
  floor: Record<string, any>,
  rooms: Array<any>
}>();

const floor = props.floor;
const rooms = props.rooms;
const isLoading = ref(true);
const refreshKey = ref(0);

// Home del breadcrumb
const home = ref({
  icon: 'pi pi-home',
  command: () => router.visit('/dashboard')
});

// Items del breadcrumb
const items = computed(() => [
  {
    label: 'Sucursales',
    command: () => router.visit(`/panel/sucursales`)
  },
  {
    label: floor.sub_branch.branch.name,
    command: () => router.visit(`/panel/branches/${floor.sub_branch.branch.id}/sub-branches`)
  },
  {
    label: floor.sub_branch.name,
    command: () => router.visit(`/panel/sub-branches/${floor.sub_branch.id}/floors`)
  },
  {
    label: floor.name,
    command: () => router.visit(`/panel/floors/${floor.id}/rooms`)
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
