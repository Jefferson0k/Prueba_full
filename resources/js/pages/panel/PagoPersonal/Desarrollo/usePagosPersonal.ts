import { ref } from 'vue';
import axios from 'axios';

export interface PagoPersonal {
  id: number;
  empleado: string;
  empleado_id: number;
  sucursal: string;
  sucursal_id: string;
  monto: number;
  monto_formateado: string;
  fecha_pago: string;
  fecha_pago_formateada: string;
  periodo: string;
  tipo_pago: string;
  metodo_pago: string;
  concepto: string | null;
  estado: 'pendiente' | 'pagado' | 'anulado';
  comprobante: string | null;
  comprobante_url: string | null;
  tiene_comprobante: boolean;
  tipo_comprobante: 'pdf' | 'imagen' | 'otro' | null;
  registrado_por: string;
  registrado_por_id: number;
  creado_en: string;
  actualizado_en: string;
}

// Estado global compartido
const pagos = ref<PagoPersonal[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

export function usePagosPersonal() {
  const fetchPagos = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.get('/pagos');
      pagos.value = response.data.data || [];
      return pagos.value;
    } catch (err: any) {
      error.value = err.message || 'Error al cargar los pagos';
      console.error('Error fetchPagos:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createPago = async (formData: FormData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.post('/pagos', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      await fetchPagos();
      return response.data;
    } catch (err: any) {
      error.value = err.message || 'Error al crear el pago';
      console.error('Error createPago:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deletePago = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      await axios.delete(`/pagos/${id}`);
      await fetchPagos();
    } catch (err: any) {
      error.value = err.message || 'Error al eliminar el pago';
      console.error('Error deletePago:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    pagos,
    loading,
    error,
    fetchPagos,
    createPago,
    deletePago
  };
}