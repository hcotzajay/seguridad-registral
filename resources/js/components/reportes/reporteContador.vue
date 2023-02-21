<template>
  <v-container>
    <v-card
        elevation="12"
        class="mx-auto my-12"
        max-width="1500"
    >
      <h2 class="tituloReporte">Reporte contador de consultas realizadas por rango de fecha</h2>
      <v-form ref="consultaRangoFecha" v-model="buscarRangoFecha" v-on:submit.prevent="buscarRangoFecha">
        <v-container>
          <v-row>
            <v-col
                cols="12"
                sm="5"
            >
              <v-menu
                  v-model="menu2"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      label="Fecha de inicio"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-model="dateFormatted"
                      @blur="date = parseDate(dateFormatted)"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="date"
                    no-title
                    @input="menu2 = false"
                    :max="date2"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
                cols="12"
                sm="5"
            >
              <v-menu
                  v-model="menu3"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      label="Fecha de fin"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-model="dateFormattedFechaFinal"
                      @blur="date2 = parseDateFinal(dateFormattedFechaFinal)"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="date2"
                    no-title
                    @input="menu3 = false"
                    :min="date"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
                cols="12"
                sm="2"
            >
              <v-icon
                  color="red"
                  class="ma-2 pa-2"
                  x-large
                  @click="generarReporte"
              >
                mdi-file-pdf-box-outline
              </v-icon>
            </v-col>
            <v-spacer></v-spacer>
          </v-row>
        </v-container>

        <v-card-actions class="pt-0">
          <align-actions>
            <cancel-btn
                @click="limpiar"
            >
              Limpiar
              <v-icon
                  right
                  dark
              >
                mdi-broom
              </v-icon>
            </cancel-btn>

            <v-btn
                rounded
                color="primary"
                :loading="loading"
                :disabled="loading"
                @click="loader = 'loading'"
            >
              Buscar
              <v-icon
                  right
                  dark
              >
                mdi-account-search
              </v-icon>
            </v-btn>
          </align-actions>
        </v-card-actions>
      </v-form>


      <v-card
          class="d-flex flex-row-reverse"
          flat
          tile
          elevation="0"
      >
        <!--        <v-card-->
        <!--            class="pa-2"-->
        <!--            tile-->
        <!--            elevation="0"-->
        <!--        >-->
        <!--          <v-icon-->
        <!--              color="red"-->
        <!--              class="ma-2 pa-2"-->
        <!--              x-large-->
        <!--              @click="generarReporte"-->
        <!--          >-->
        <!--            mdi-file-pdf-box-outline-->
        <!--          </v-icon>-->
        <!--        </v-card>-->

        <v-card
            class="pa-2"
            tile
            elevation="0"
            v-if="existeContador"
        >
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-avatar
                  color="info"
                  size="62"
                  v-bind="attrs"
                  v-on="on"
              >
                <span class="white--text text-h5">{{ contador }}</span>
              </v-avatar>
            </template>
            <span>Consultas restantes del mes actual</span>
          </v-tooltip>
        </v-card>
      </v-card>

      <div v-if="existeInformacion">
        <v-data-table
            :headers="headers"
            :items="desserts"
            :page.sync="page"
            :items-per-page="10"
            hide-default-footer
            class="elevation-1"
            @page-count="pageCount = $event"
        >
        </v-data-table>
        <div class="text-center pt-2">
          <v-pagination
              v-model="page"
              :length="pageCount"
          ></v-pagination>
        </div>
      </div>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: "contador",
  data: vm => ({
    buscarRangoFecha: false,
    loader: null,
    loading: false,
    date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
    date2: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
    dateFormatted: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
    dateFormattedFechaFinal: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
    modal: false,
    menu2: false,
    menu3: false,
    existeInformacion: false,
    existeRespuestaConsulta: false,
    existeContador: false,
    contador: '',
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    headers: [
      {text: 'Contador', value: 'contador'},
      {text: 'Fecha del contador', value: 'fecha_contador'},
      {text: 'Nombre del colaborador', value: 'nombre_colaborador'},
      {text: 'Tipo de búsqueda', value: 'tipoBusqueda'},
      {text: 'Cui de búsqueda', value: 'cuiBuscado'},
      {text: 'Nombre de búsqueda', value: 'nombreBusqueda'},
    ],
    desserts: [],
    enviarRespuestaConsulta: [],
  }),
  computed: {
    computedDateFormattedInicial() {
      return this.formatDate(this.date)
    },
    computedDateFormattedFinal() {
      return this.formatDateFinal(this.date2)
    },
  },
  watch: {
    date(val) {
      this.dateFormatted = this.formatDate(this.date)
    },
    date2(val) {
      this.dateFormattedFechaFinal = this.formatDate(this.date2)
    },
    loader() {
      const l = this.loader
      this[l] = !this[l]
      if (this.loader) {
        this.buscarFecha()
      }
    },
  },
  methods: {
    buscarFecha() {
      axios.get('/consultarContadorRangoFechas', {
        params: {
          fechaInicio: this.date,
          fechaFin: this.date2
        }
      })
          .then(res => {
            this.loading = false
            this.desserts = res.data.resultados
            this.contador = res.data.contador
            this.existeContador = true
            this.existeInformacion = true
          })
          .catch(err => {
            this.$iziToast.fail(err, this.$Progress)
            this.loading = false
          })
          .finally(() => {
            this.loading = false
            this.cancelarLoading()
          })
    },
    cancelarLoading() {
      const l = this.loader
      this[l] = false
      this.loader = null
    },
    formatDate(date) {
      if (!date) return null
      const [year, month, day] = date.split('-')
      return `${day}/${month}/${year}`
    },
    formatDateFinal(date2) {
      if (!date2) return null
      const [year, month, day] = date2.split('-')
      return `${day}/${month}/${year}`
    },
    parseDate(date) {
      if (!date) return null
      const [day, month, year] = date.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    parseDateFinal(date2) {
      if (!date2) return null
      const [day, month, year] = date2.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    limpiar() {
      this.date = ''
      this.date2 = ''
      this.cancelarLoading()
    },
    generarReporte() {
      if (this.desserts.length > 0) {
        let datos = {
          datosObtenidos: this.desserts,
          fechaInicial: this.date,
          fechaFinal: this.date2
        }

        let myJson = JSON.stringify(datos)
        window.open('/generarReporteRangoFechasContador?json=' + myJson)
      } else {
        this.$iziToast.warning('Alerta', 'No existen datos para generar el reporte.')
        return false
      }
    },
  },
}
</script>

<style scoped>
.tituloReporte {
  text-align: center;
}
</style>