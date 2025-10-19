<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <div>
                <h2 class="text-xl font-bold">Ingreso Bruto</h2>
                <p class="text-xs mt-1">Análisis completo de ingresos por habitaciones y productos</p>
            </div>
            <div class="flex gap-2">
                <Calendar 
                    v-model="filtroMes" 
                    view="month" 
                    dateFormat="mm/yy" 
                    placeholder="Seleccionar mes"
                    @date-select="cargarDatos"
                    fluid
                    class="w-48"
                />
            </div>
        </div>

        <!-- Total Principal -->
        <Message severity="info" :closable="false" class="mb-0">
            <div class="flex justify-between items-center w-full">
                <div>
                    <div class="text-xs font-medium mb-1 flex items-center gap-2">
                        <i class="pi pi-dollar text-sm"></i>
                        Ingreso Bruto Total
                    </div>
                    <div class="text-3xl font-bold mb-1">S/ {{ formatoMoneda(ingresoBruto.total) }}</div>
                    <div class="text-xs opacity-80">
                        <i class="pi pi-calendar mr-1"></i>
                        {{ formatoFecha(filtroMes) }}
                    </div>
                </div>
                <div class="opacity-20">
                    <i class="pi pi-chart-line text-4xl"></i>
                </div>
            </div>
        </Message>

        <!-- Cards de Ingresos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Habitaciones -->
            <Message severity="success" :closable="false" class="mb-0">
                <div class="flex items-center justify-between w-full">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="pi pi-home text-2xl opacity-80"></i>
                            <div>
                                <span class="text-xs font-medium uppercase tracking-wide opacity-75">Ingresos por</span>
                                <h3 class="text-sm font-bold">Habitaciones</h3>
                            </div>
                        </div>
                        <div class="text-2xl font-bold mb-2">
                            S/ {{ formatoMoneda(parseFloat(ingresoBruto.ingresos_habitaciones.toString())) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="bg-white bg-opacity-40 rounded-full px-3 py-0.5">
                                <span class="text-xs font-bold">{{ porcentajeHabitaciones }}%</span>
                            </div>
                            <span class="text-xs opacity-80">del ingreso total</span>
                        </div>
                    </div>
                </div>
            </Message>

            <!-- Productos -->
            <Message severity="success" :closable="false" class="mb-0">
                <div class="flex items-center justify-between w-full">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="pi pi-shopping-cart text-2xl opacity-80"></i>
                            <div>
                                <span class="text-xs font-medium uppercase tracking-wide opacity-75">Ingresos por</span>
                                <h3 class="text-sm font-bold">Productos</h3>
                            </div>
                        </div>
                        <div class="text-2xl font-bold mb-2">
                            S/ {{ formatoMoneda(ingresoBruto.ingresos_productos) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="bg-white bg-opacity-40 rounded-full px-3 py-0.5">
                                <span class="text-xs font-bold">{{ porcentajeProductos }}%</span>
                            </div>
                            <span class="text-xs opacity-80">del ingreso total</span>
                        </div>
                    </div>
                </div>
            </Message>
        </div>

        <!-- Gráficas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Distribución -->
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <div class="bg-blue-100 rounded-lg p-1.5">
                            <i class="pi pi-chart-pie text-blue-600 text-sm"></i>
                        </div>
                        Distribución de Ingresos
                    </h3>
                </div>
                <Chart 
                    type="doughnut" 
                    :data="graficaDistribucion" 
                    :options="opcionesDoughnut"
                    class="w-full"
                    style="height: 220px"
                />
            </div>

            <!-- Tendencia -->
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <div class="bg-blue-100 rounded-lg p-1.5">
                            <i class="pi pi-chart-bar text-blue-600 text-sm"></i>
                        </div>
                        Tendencia de Últimos Meses
                    </h3>
                </div>
                <Chart 
                    type="bar" 
                    :data="graficaComparativa" 
                    :options="opcionesComparativa"
                    class="w-full"
                    style="height: 220px"
                />
            </div>
        </div>

        <!-- Métricas Detalladas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Estadísticas Diarias -->
            <Message severity="warn" :closable="false" class="mb-0">
                <div class="w-full">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="pi pi-calendar-clock text-lg opacity-80"></i>
                        <h3 class="font-bold text-xs">Estadísticas Diarias</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Promedio Diario</span>
                            <span class="font-bold text-sm">S/ {{ formatoMoneda(promedioDiario) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Mejor Día (estimado)</span>
                            <span class="font-bold text-sm">S/ {{ formatoMoneda(diaPico) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs opacity-80">Días del Mes</span>
                            <span class="font-bold text-sm">{{ diasDelMes }}</span>
                        </div>
                    </div>
                </div>
            </Message>

            <!-- Crecimiento -->
            <Message :severity="crecimientoMensual >= 0 ? 'success' : 'error'" :closable="false" class="mb-0">
                <div class="w-full">
                    <div class="flex items-center gap-2 mb-3">
                        <i :class="['text-lg opacity-80', crecimientoMensual >= 0 ? 'pi pi-arrow-up' : 'pi pi-arrow-down']"></i>
                        <h3 class="font-bold text-xs">Análisis de Crecimiento</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Crecimiento Mensual</span>
                            <span class="font-bold text-sm">
                                {{ crecimientoMensual >= 0 ? '+' : '' }}{{ crecimientoMensual.toFixed(1) }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Mes Anterior</span>
                            <span class="font-bold text-sm">S/ {{ formatoMoneda(ingresoMesAnterior) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs opacity-80">Variación</span>
                            <span class="font-bold text-sm">
                                {{ variacionAbsoluta >= 0 ? '+' : '' }}S/ {{ formatoMoneda(Math.abs(variacionAbsoluta)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </Message>

            <!-- Métricas de Operación -->
            <Message severity="info" :closable="false" class="mb-0">
                <div class="w-full">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="pi pi-users text-lg opacity-80"></i>
                        <h3 class="font-bold text-xs">Métricas de Operación</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Total Reservas</span>
                            <span class="font-bold text-sm">{{ metricas.totalReservas }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-current border-opacity-20">
                            <span class="text-xs opacity-80">Total Consumos</span>
                            <span class="font-bold text-sm">{{ metricas.totalConsumos }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs opacity-80">Ticket Promedio</span>
                            <span class="font-bold text-sm">S/ {{ formatoMoneda(metricas.ticketPromedio) }}</span>
                        </div>
                    </div>
                </div>
            </Message>
        </div>

        <!-- Gráfica de Línea de Tendencia -->
        <div class="">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <div class="rounded-lg p-1.5">
                        <i class="pi pi-chart-line text-blue-600 text-sm"></i>
                    </div>
                    Evolución de Ingresos por Categoría
                </h3>
            </div>
            <Chart 
                type="line" 
                :data="graficaLinea" 
                :options="opcionesLinea"
                class="w-full"
                style="height: 220px"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Chart from 'primevue/chart';
import Calendar from 'primevue/calendar';
import Message from 'primevue/message';

interface IngresoBruto {
    total: number;
    ingresos_habitaciones: string | number;
    ingresos_productos: number;
}

interface DatosComparativa {
    mes: string;
    mes_nombre: string;
    ingresos_habitaciones: string | number;
    ingresos_productos: number;
    total: number;
}

const cargando = ref(false);
const filtroMes = ref(new Date());
const ingresoBruto = ref<IngresoBruto>({ total: 0, ingresos_habitaciones: 0, ingresos_productos: 0 });
const datosComparativa = ref<DatosComparativa[]>([]);

// Computados para porcentajes
const porcentajeHabitaciones = computed(() => {
    const habitaciones = parseFloat(ingresoBruto.value.ingresos_habitaciones.toString());
    return ingresoBruto.value.total === 0 ? 0 : ((habitaciones / ingresoBruto.value.total) * 100).toFixed(1);
});

const porcentajeProductos = computed(() => 
    ingresoBruto.value.total === 0 ? 0 : ((ingresoBruto.value.ingresos_productos / ingresoBruto.value.total) * 100).toFixed(1)
);

// Computados para métricas
const diasDelMes = computed(() => 
    new Date(filtroMes.value.getFullYear(), filtroMes.value.getMonth() + 1, 0).getDate()
);

const promedioDiario = computed(() => 
    diasDelMes.value > 0 ? ingresoBruto.value.total / diasDelMes.value : 0
);

const diaPico = computed(() => 
    ingresoBruto.value.total * 0.15
);

const ingresoMesAnterior = computed(() => {
    if (datosComparativa.value.length < 2) return 0;
    return datosComparativa.value[datosComparativa.value.length - 2]?.total || 0;
});

const variacionAbsoluta = computed(() => 
    ingresoBruto.value.total - ingresoMesAnterior.value
);

const crecimientoMensual = computed(() => {
    if (datosComparativa.value.length < 2) return 0;
    const mesActualIdx = datosComparativa.value.length - 1;
    const mesAnteriorIdx = mesActualIdx - 1;
    const actual = datosComparativa.value[mesActualIdx]?.total || 0;
    const anterior = datosComparativa.value[mesAnteriorIdx]?.total || 0;
    return anterior === 0 ? 0 : ((actual - anterior) / anterior) * 100;
});

const metricas = computed(() => ({
    totalReservas: 45,
    totalConsumos: 128,
    ticketPromedio: ingresoBruto.value.total > 0 ? ingresoBruto.value.total / 45 : 0
}));

// Funciones de formato
const formatoMoneda = (valor: number) => 
    new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(valor);

const formatoFecha = (fecha: Date) => {
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    return `${meses[fecha.getMonth()]} ${fecha.getFullYear()}`;
};

const obtenerParametrosMes = () => ({
    month: filtroMes.value.getMonth() + 1,
    year: filtroMes.value.getFullYear()
});

// Funciones de carga de datos
const cargarDatos = async () => {
    cargando.value = true;
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingreso-bruto', { params });
        ingresoBruto.value = response.data;
        await cargarDatosComparativa();
    } catch (error) {
        console.error('Error cargando datos:', error);
    } finally {
        cargando.value = false;
    }
};

const cargarDatosComparativa = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingreso-bruto-comparativa', { params });
        datosComparativa.value = response.data;
    } catch (error) {
        console.error('Error cargando comparativa:', error);
        datosComparativa.value = [];
    }
};

// Datos de gráficas
const graficaDistribucion = computed(() => {
    const habitaciones = parseFloat(ingresoBruto.value.ingresos_habitaciones.toString());
    return {
        labels: ['Habitaciones', 'Productos'],
        datasets: [{
            data: [habitaciones, ingresoBruto.value.ingresos_productos],
            backgroundColor: ['#10B981', '#3B82F6'],
            hoverBackgroundColor: ['#059669', '#2563EB'],
            borderWidth: 3,
            borderColor: '#ffffff'
        }]
    };
});

const graficaComparativa = computed(() => {
    const labels = datosComparativa.value.map(d => {
        const mes = d.mes_nombre.charAt(0).toUpperCase() + d.mes_nombre.slice(1);
        return mes.length > 3 ? mes.substring(0, 3) : mes;
    });
    const datos = datosComparativa.value.map(d => d.total);

    return {
        labels,
        datasets: [{
            label: 'Ingreso Total',
            data: datos,
            backgroundColor: '#3B82F6',
            borderColor: '#2563EB',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: '#2563EB'
        }]
    };
});

const graficaLinea = computed(() => {
    const labels = datosComparativa.value.map(d => {
        const mes = d.mes_nombre.charAt(0).toUpperCase() + d.mes_nombre.slice(1);
        return mes.length > 3 ? mes.substring(0, 3) : mes;
    });
    
    return {
        labels,
        datasets: [
            {
                label: 'Habitaciones',
                data: datosComparativa.value.map(d => parseFloat(d.ingresos_habitaciones.toString())),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10B981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            },
            {
                label: 'Productos',
                data: datosComparativa.value.map(d => d.ingresos_productos),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }
        ]
    };
});

// Opciones de gráficas
const opcionesDoughnut = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { 
            position: 'bottom',
            labels: { 
                font: { size: 11, weight: 600 },
                color: '#374151',
                padding: 12,
                usePointStyle: true,
                pointStyle: 'circle'
            } 
        },
        tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#F9FAFB',
            bodyColor: '#F9FAFB',
            borderColor: '#374151',
            borderWidth: 1,
            padding: 10,
            displayColors: true,
            callbacks: {
                label: (ctx: any) => {
                    const total = ctx.dataset.data.reduce((a: number, b: number) => a + b, 0);
                    const pct = Math.round((ctx.parsed / total) * 100);
                    return `${ctx.label}: S/ ${formatoMoneda(ctx.parsed)} (${pct}%)`;
                }
            }
        }
    }
};

const opcionesComparativa = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { 
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#F9FAFB',
            bodyColor: '#F9FAFB',
            borderColor: '#374151',
            borderWidth: 1,
            padding: 10,
            callbacks: {
                label: (ctx: any) => `Ingreso: S/ ${formatoMoneda(ctx.parsed.y)}`
            }
        }
    },
    scales: {
        x: {
            ticks: { 
                color: '#6B7280',
                font: { size: 10, weight: 600 }
            },
            grid: { 
                display: false
            }
        },
        y: {
            ticks: { 
                color: '#6B7280',
                font: { size: 10 },
                callback: (v: any) => 'S/ ' + new Intl.NumberFormat('es-PE').format(v)
            },
            grid: { 
                color: '#E5E7EB'
            }
        }
    }
};

const opcionesLinea = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index',
        intersect: false
    },
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                font: { size: 11, weight: 600 },
                color: '#374151',
                padding: 12,
                usePointStyle: true,
                pointStyle: 'circle'
            }
        },
        tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#F9FAFB',
            bodyColor: '#F9FAFB',
            borderColor: '#374151',
            borderWidth: 1,
            padding: 10,
            callbacks: {
                label: (ctx: any) => `${ctx.dataset.label}: S/ ${formatoMoneda(ctx.parsed.y)}`
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 10, weight: 600 }
            },
            grid: {
                display: false
            }
        },
        y: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 },
                callback: (v: any) => 'S/ ' + new Intl.NumberFormat('es-PE').format(v)
            },
            grid: {
                color: '#E5E7EB'
            }
        }
    }
};

onMounted(cargarDatos);
</script>