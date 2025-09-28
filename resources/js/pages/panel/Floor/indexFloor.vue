<template>
  <Head title="Pisos" />
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
          <addFloor
            :subBranch="subBranch"
            @agregado="refrescarListado"
          />
          <!-- forzamos remount cuando cambia subBranch -->
          <listFloor
            :key="subBranch.id"
            :subBranch="subBranch"
            :refresh="refreshKey"
          />
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, toRef, computed } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import listFloor from './Desarrollo/listFloor.vue';
import addFloor from './Desarrollo/addFloor.vue';
import Breadcrumb from 'primevue/breadcrumb';

const props = defineProps<{
  subBranch: Record<string, any>,
  floors: Array<any>
}>();

// usar toRef para que subBranch reaccione cuando cambie la prop
const subBranch = toRef(props, 'subBranch');

const isLoading = ref(true);
const refreshKey = ref(0);

const home = ref({ 
    icon: 'pi pi-home', 
    command: () => router.visit('/dashboard')
});

// convertir items a computed para que se actualice si cambia subBranch
const items = computed(() => [
    { 
        label: 'Sucursales', 
        command: () => router.visit('/panel/sucursales')
    },
    { 
        label: subBranch.value?.branch?.name ?? '',
        command: () => router.visit(`/panel/branches/${subBranch.value?.branch?.id}/sub-branches`)
    },
    { 
        label: subBranch.value?.name ?? '',
        command: () => router.visit(`/panel/sub-branches/${subBranch.value?.id}/floors`)
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
