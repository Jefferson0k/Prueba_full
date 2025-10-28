<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppMenuItem from './AppMenuItem.vue';

const page = usePage();
const permissions = computed(() => page.props.auth.user?.permissions ?? []);
const hasPermission = (perm) => permissions.value.includes(perm);

const model = computed(() => [
    {
        label: 'Home',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }
        ]
    },
    {
        label: 'Gestión de Productos',
        items: [
            hasPermission('view products') && {
                label: 'Productos',
                icon: 'pi pi-fw pi-tags',
                to: '/panel/productos'
            },
            hasPermission('ver categorias') && {
                label: 'Categorías',
                icon: 'pi pi-fw pi-list',
                to: '/panel/categorias'
            },
        ].filter(Boolean),
    },
].filter(section => section.items.length > 0));
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="i">
            <app-menu-item :item="item" :index="i" />
        </template>
    </ul>
</template>