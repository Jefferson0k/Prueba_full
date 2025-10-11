import { defineStore } from 'pinia';
import axios from 'axios';

export const useKardexGeneralStore = defineStore('kardexGeneral', {
  state: () => ({
    kardexData: [],
    loading: false,
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0,
      from: 0,
      to: 0
    }
  }),
  
  actions: {
    async fetchKardexGeneral(params = {}, page = 1) {
      this.loading = true;
      try {
        const requestParams = {
          ...params,
          page: page,
          per_page: this.pagination.perPage
        };

        const response = await axios.get('/kardex/general', { params: requestParams });
        
        this.kardexData = response.data.data;
        
        // Guardar información de paginación
        if (response.data.meta) {
          this.pagination = {
            currentPage: response.data.meta.current_page,
            lastPage: response.data.meta.last_page,
            perPage: response.data.meta.per_page,
            total: response.data.meta.total,
            from: response.data.meta.from || 0,
            to: response.data.meta.to || 0
          };
        }
        
        console.log('Kardex general cargado:', this.kardexData.length, 'registros de', this.pagination.total);
      } catch (error) {
        console.error('Error al cargar kardex general:', error);
        this.kardexData = [];
      } finally {
        this.loading = false;
      }
    },
    
    clearKardexGeneral() {
      this.kardexData = [];
      this.pagination = {
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0,
        from: 0,
        to: 0
      };
    }
  }
});