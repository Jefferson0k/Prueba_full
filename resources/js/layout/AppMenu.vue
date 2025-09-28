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
        label: 'Configuración',
        items: [
            hasPermission('view branches') && { label: 'Sucursales', icon: 'pi pi-fw pi-building', to: '/panel/sucursales' },
            hasPermission('ver pisos') && { label: 'Pisos', icon: 'pi pi-fw pi-sitemap', to: '/panel/pisos' },
            hasPermission('ver tipos de habitación') && { label: 'Tipos de Habitación', icon: 'pi pi-fw pi-th-large', to: '/panel/tipos-habitacion' },
        ].filter(Boolean),
    },
    {
        label: 'Habitaciones',
        items: [
            hasPermission('view rooms') && { label: 'Gestión de Habitaciones', icon: 'pi pi-fw pi-home', to: '/panel/habitaciones' },
        ].filter(Boolean),
    },
    {
        label: 'Reservas',
        items: [
            hasPermission('view bookings') && { label: 'Gestión de Reservas', icon: 'pi pi-fw pi-calendar', to: '/panel/reservas' },
            hasPermission('create bookings') && { label: 'Nueva Reserva', icon: 'pi pi-fw pi-plus-circle', to: '/panel/reservas/nueva' },
        ].filter(Boolean),
    },
    {
        label: 'Pagos',
        items: [
            hasPermission('view payments') && { label: 'Gestión de Pagos', icon: 'pi pi-fw pi-credit-card', to: '/panel/pagos' },
            hasPermission('refund payments') && { label: 'Reembolsos', icon: 'pi pi-fw pi-refresh', to: '/panel/pagos/reembolsos' },
        ].filter(Boolean),
    },
    {
        label: 'Inventario',
        items: [
            hasPermission('view inventory') && { label: 'Gestión de Inventario', icon: 'pi pi-fw pi-box', to: '/panel/inventario' },
            hasPermission('adjust inventory stock') && { label: 'Ajustes de Stock', icon: 'pi pi-fw pi-sort-amount-up', to: '/panel/inventario/ajustes' },
            hasPermission('transfer inventory') && { label: 'Transferencias', icon: 'pi pi-fw pi-exchange', to: '/panel/inventario/transferencias' },
        ].filter(Boolean),
    },
    {
        label: 'Proveedores',
        items: [
            hasPermission('view suppliers') && { label: 'Gestión de Proveedores', icon: 'pi pi-fw pi-truck', to: '/panel/proveedores' },
            hasPermission('create suppliers') && { label: 'Nuevo Proveedor', icon: 'pi pi-fw pi-plus', to: '/panel/proveedores/nuevo' },
        ].filter(Boolean),
    },
    {
        label: 'Clientes',
        items: [
            hasPermission('view clients') && { label: 'Gestión de Clientes', icon: 'pi pi-fw pi-user', to: '/panel/clientes' },
            hasPermission('create clients') && { label: 'Nuevo Cliente', icon: 'pi pi-fw pi-user-plus', to: '/panel/clientes/nuevo' },
        ].filter(Boolean),
    },
    {
        label: 'Kardex',
        items: [
            hasPermission('view kardex') && { label: 'Kardex General', icon: 'pi pi-fw pi-file-o', to: '/panel/kardex' },
            hasPermission('view kardex') && { label: 'Kardex por Producto', icon: 'pi pi-fw pi-list', to: '/panel/kardex/producto' },
            hasPermission('view kardex') && { label: 'Kardex Valorizado', icon: 'pi pi-fw pi-dollar', to: '/panel/kardex/valorizado' },
        ].filter(Boolean),
    },
    {
        label: 'Movimientos',
        items: [
            hasPermission('view movements') && { label: 'Todos los Movimientos', icon: 'pi pi-fw pi-history', to: '/panel/movimientos' },
            hasPermission('view movements') && { label: 'Entradas', icon: 'pi pi-fw pi-arrow-down', to: '/panel/movimientos/entradas' },
            hasPermission('view movements') && { label: 'Salidas', icon: 'pi pi-fw pi-arrow-up', to: '/panel/movimientos/salidas' },
            hasPermission('create movements') && { label: 'Nuevo Movimiento', icon: 'pi pi-fw pi-plus-circle', to: '/panel/movimientos/nuevo' },
        ].filter(Boolean),
    },
    {
        label: 'Reportes',
        items: [
            hasPermission('ver reportes') && { label: 'Reporte de Ocupación', icon: 'pi pi-fw pi-chart-bar', to: '/panel/reportes/ocupacion' },
            hasPermission('ver reportes') && { label: 'Reporte de Ingresos', icon: 'pi pi-fw pi-chart-line', to: '/panel/reportes/ingresos' },
            hasPermission('ver reportes') && { label: 'Reporte de Inventario', icon: 'pi pi-fw pi-chart-pie', to: '/panel/reportes/inventario' },
        ].filter(Boolean),
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
    {
        label: 'Usuarios',
        items: [
            hasPermission('ver usuarios') && { label: 'Gestión de Usuarios', icon: 'pi pi-fw pi-users', to: '/panel/usuario' },
            hasPermission('ver roles') && { label: 'Roles', icon: 'pi pi-fw pi-id-card', to: '/panel/roles' },
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