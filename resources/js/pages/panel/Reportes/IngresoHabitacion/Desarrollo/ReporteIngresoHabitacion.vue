<template>
    <div class="">
        <!-- TÍTULO Y FILTROS -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold m-0">Ingresos de Habitaciones</h2>
                <p class="text-sm mt-1">Reporte mensual de ingresos generados</p>
            </div>
            <div class="flex gap-2">
                <Calendar 
                    v-model="filtroMes" 
                    view="month" 
                    dateFormat="mm/yy" 
                    placeholder="Seleccionar mes"
                    @date-select="cargarDatos"
                    fluid
                />
            </div>
        </div>

        <!-- TARJETAS RESUMEN - 4 EN FILA -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 mb-4">
            <Message severity="info" class="m-0">
                <template #icon>
                    <i class="pi pi-wallet text-xl"></i>
                </template>
                <div class="ml-2">
                    <div class="text-gray-500 font-medium text-xs">Total Ingresos</div>
                    <div class="text-lg font-bold">S/ {{ formatoMoneda(resumen.total) }}</div>
                </div>
            </Message>

            <Message severity="success" class="m-0">
                <template #icon>
                    <i class="pi pi-home text-xl"></i>
                </template>
                <div class="ml-2">
                    <div class="text-gray-500 font-medium text-xs">Total Reservas</div>
                    <div class="text-lg font-bold">{{ resumen.total_reservas }}</div>
                </div>
            </Message>

            <Message severity="warn" class="m-0">
                <template #icon>
                    <i class="pi pi-clock text-xl"></i>
                </template>
                <div class="ml-2">
                    <div class="text-gray-500 font-medium text-xs">Horas Vendidas</div>
                    <div class="text-lg font-bold">{{ resumen.total_horas }}</div>
                </div>
            </Message>

            <Message severity="secondary" class="m-0">
                <template #icon>
                    <i class="pi pi-chart-line text-xl"></i>
                </template>
                <div class="ml-2">
                    <div class="text-gray-500 font-medium text-xs">Promedio/Reserva</div>
                    <div class="text-lg font-bold">S/ {{ formatoMoneda(resumen.promedio_reserva) }}</div>
                </div>
            </Message>
        </div>

        <!-- GRÁFICAS - 2 EN FILA -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-4">
            <div class="lg:col-span-7">
                <div class="card p-3">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="pi pi-chart-bar text-primary"></i>
                        <span class="font-semibold text-sm">Ingresos por Día</span>
                    </div>
                    <Chart 
                        type="bar" 
                        :data="graficaIngresosDia" 
                        :options="opcionesGraficaBarras"
                        class="h-64"
                    />
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="card p-3">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="pi pi-chart-pie text-primary"></i>
                        <span class="font-semibold text-sm">Distribución por Método</span>
                    </div>
                    <Chart 
                        type="pie" 
                        :data="graficaDistribucion" 
                        :options="opcionesPie"
                        class="h-64"
                    />
                </div>
            </div>
        </div>

        <!-- TABLA DETALLADA -->
        <div class="">
            <div class="flex items-center gap-2 mb-3">
                <i class="pi pi-table text-primary"></i>
                <span class="font-semibold text-sm">Detalle de Ventas</span>
            </div>
            <DataTable 
                :value="pagos" 
                :loading="cargando"
                stripedRows
                paginator 
                :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                responsiveLayout="scroll"
                size="small"
            >
                <Column field="payment_code" header="Código Pago" sortable></Column>
                <Column field="booking.booking_code" header="Código Reserva" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.booking?.booking_code || 'N/A' }}
                    </template>
                </Column>
                <Column field="booking.room_number" header="Habitación" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.booking?.room_number || 'N/A' }}
                    </template>
                </Column>
                <Column field="payment_date" header="Fecha" sortable>
                    <template #body="slotProps">
                        {{ formatoFecha(slotProps.data.payment_date) }}
                    </template>
                </Column>
                <Column field="amount" header="Monto" sortable>
                    <template #body="slotProps">
                        <span class="font-semibold">S/ {{ formatoMoneda(slotProps.data.amount) }}</span>
                    </template>
                </Column>
                <Column field="payment_method" header="Método" sortable>
                    <template #body="slotProps">
                        <Tag 
                            :value="getMetodoPagoLabel(slotProps.data.payment_method)" 
                            :severity="getSeverityMetodo(slotProps.data.payment_method)" 
                        />
                    </template>
                </Column>
                <Column field="status" header="Estado" sortable>
                    <template #body="slotProps">
                        <Tag 
                            :value="getEstadoLabel(slotProps.data.status)" 
                            :severity="getSeverityEstado(slotProps.data.status)" 
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Chart from 'primevue/chart';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Message from 'primevue/message';

interface Resumen {
    total: number;
    total_reservas: number;
    promedio_reserva: number;
    total_horas: number;
}

interface Pago {
    id: string;
    payment_code: string;
    amount: number;
    payment_method: string;
    payment_date: string;
    status: string;
    booking?: {
        id: string;
        booking_code: string;
        room_number: string;
    };
}

interface DatosGrafica {
    dia: string;
    ingresos: string;
}

const cargando = ref(false);
const filtroMes = ref(new Date());
const resumen = ref<Resumen>({
    total: 0,
    total_reservas: 0,
    promedio_reserva: 0,
    total_horas: 0
});
const pagos = ref<Pago[]>([]);
const datosGrafica = ref<DatosGrafica[]>([]);

const formatoMoneda = (valor: number) => {
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor);
};

const formatoFecha = (fecha: string) => {
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const obtenerParametrosMes = () => {
    const fecha = filtroMes.value;
    return {
        month: fecha.getMonth() + 1,
        year: fecha.getFullYear()
    };
};

const cargarDatos = async () => {
    cargando.value = true;
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingresos-habitaciones', { params });
        resumen.value = response.data;
        await cargarPagosDetallados();
        await cargarDatosGrafica();
    } catch (error) {
        console.error('Error cargando datos:', error);
    } finally {
        cargando.value = false;
    }
};

const cargarPagosDetallados = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/bookings', { 
            params: { ...params, per_page: 100 }
        });
        pagos.value = response.data.data || [];
    } catch (error) {
        console.error('Error cargando pagos:', error);
        pagos.value = [];
    }
};

const cargarDatosGrafica = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingresos-habitaciones-grafica', { params });
        datosGrafica.value = response.data;
    } catch (error) {
        console.error('Error cargando gráfica:', error);
        datosGrafica.value = [];
    }
};

const graficaIngresosDia = computed(() => {
    if (!datosGrafica.value.length) {
        return {
            labels: ['Sin datos'],
            datasets: [{
                label: 'Ingresos',
                data: [0],
                backgroundColor: '#42A5F5'
            }]
        };
    }

    return {
        labels: datosGrafica.value.map(item => {
            const fecha = new Date(item.dia);
            return `${fecha.getDate()} ${fecha.toLocaleDateString('es-PE', { month: 'short' })}`;
        }),
        datasets: [{
            label: 'Ingresos',
            data: datosGrafica.value.map(item => parseFloat(item.ingresos)),
            backgroundColor: '#42A5F5',
            borderColor: '#1E88E5',
            borderWidth: 1
        }]
    };
});

const graficaDistribucion = computed(() => {
    const metodosPago = pagos.value.reduce((acc: any, pago) => {
        const metodo = pago.payment_method;
        acc[metodo] = (acc[metodo] || 0) + pago.amount;
        return acc;
    }, {});

    const labels = Object.keys(metodosPago).map(key => getMetodoPagoLabel(key));
    const data = Object.values(metodosPago);

    if (!data.length) {
        return {
            labels: ['Sin datos'],
            datasets: [{ data: [1], backgroundColor: ['#e0e0e0'] }]
        };
    }

    return {
        labels,
        datasets: [{
            data,
            backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#26C6DA', '#7E57C2', '#EC407A']
        }]
    };
});

const opcionesGraficaBarras = {
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context: any) => 'S/ ' + formatoMoneda(context.parsed.y)
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: any) => 'S/ ' + value.toLocaleString('es-PE')
            }
        }
    }
};

const opcionesPie = {
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { padding: 8, font: { size: 11 } }
        },
        tooltip: {
            callbacks: {
                label: (context: any) => context.label + ': S/ ' + formatoMoneda(context.parsed)
            }
        }
    }
};

const getMetodoPagoLabel = (metodo: string) => {
    const metodos: { [key: string]: string } = {
        'cash': 'Efectivo',
        'card': 'Tarjeta',
        'transfer': 'Transferencia'
    };
    return metodos[metodo] || metodo;
};

const getEstadoLabel = (estado: string) => {
    const estados: { [key: string]: string } = {
        'completed': 'Completado',
        'pending': 'Pendiente',
        'failed': 'Fallido'
    };
    return estados[estado] || estado;
};

const getSeverityMetodo = (metodo: string) => {
    switch (metodo) {
        case 'cash': return 'success';
        case 'card': return 'info';
        case 'transfer': return 'warning';
        default: return 'secondary';
    }
};

const getSeverityEstado = (estado: string) => {
    switch (estado) {
        case 'completed': return 'success';
        case 'pending': return 'warning';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
};

onMounted(() => {
    cargarDatos();
});
</script>