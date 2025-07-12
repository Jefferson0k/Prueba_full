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
        label: 'Clientes',
        items: [
            hasPermission('ver cliente') && { label: 'Listado de Clientes', icon: 'pi pi-fw pi-id-card', to: '/clientes'},
        ].filter(Boolean),
    },
    {
        label: 'Hoteles',
        items: [
            hasPermission('ver habitacion') && { label: 'Habitación', icon: 'pi pi-fw pi-building', to: '/habitaciones' },
        ].filter(Boolean),
    },
    {
        label: 'Inventario',
        items: [
            { label: 'Producto', icon: 'pi pi-fw pi-box', to: '/inventario/productos' },
        ]
    },
    {
        label: 'Reporte',
        items: [
            { label: 'Reporte general', icon: 'pi pi-fw pi-chart-line', to: '/reportes/general' },
        ]
    },
    {
        label: 'Gestión',
        items: [
            { label: 'Personal', icon: 'pi pi-fw pi-users', to: '/gestion/personal' },
            { label: 'Pagos del personal', icon: 'pi pi-fw pi-dollar', to: '/gestion/pagos-personal' },
            { label: 'Historial', icon: 'pi pi-fw pi-history', to: '/gestion/historial' },
            { label: 'Ingres/Egresos', icon: 'pi pi-fw pi-wallet', to: '/gestion/movimientos' },
        ]
    },
    {
        label: 'Usuarios',
        items: [
            hasPermission('ver usuarios') && { label: 'Gestión de Usuarios', icon: 'pi pi-fw pi-user-edit', to: '/usuario' },
            hasPermission('ver roles') && { label: 'Roles', icon: 'pi pi-fw pi-check-square', to: '/roles' },
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

<style scoped lang="scss">
/* Puedes agregar tus estilos aquí si lo deseas */
</style>
