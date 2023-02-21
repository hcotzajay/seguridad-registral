<template>
  <v-container>
    <v-card
        elevation="12"
        class="mx-auto my-12"
        max-width="1500"
    >
      <h2 class="tituloReporte">Reporte de tokens utilizados</h2>
      <v-form ref="consultaTokens" v-model="buscarToken" v-on:submit.prevent="buscarToken">
        <v-container>
          <v-row>
            <v-col
                cols="12"
                sm="6"
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
                sm="6"
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

      <div v-if="existeInformacion">

        <v-data-table
            :headers="headers"
            :items="desserts"
            :page.sync="page"
            :items-per-page="10"
            hide-default-footer
            class="elevation-1"
            @page-count="pageCount = $event"
        ></v-data-table>
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
  name: "tokens",
  data: vm => ({
    buscarToken: false,
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

    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    headers: [
      {text: 'Fecha del Token', value: 'fecha_token'},
      {text: 'Estado Token', value: 'estado'}
    ],
    desserts: [],


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
        this.buscarTokens()
      }
    },
  },
  methods: {
    buscarTokens() {
      // let fechaHoy = new Date().toLocaleDateString('es-ES')
      // let fechaHoy = new Date()
      // let dia = fechaHoy.getDate()
      // let mes = fechaHoy.getMonth() + 1
      // let anio = fechaHoy.getFullYear()
      // if (dia < 10) {
      //   dia = '0' + dia
      // }
      // if (mes < 10) {
      //   mes = '0' + mes
      // }
      // let fechaFormato = dia + '/' + mes + '/' + anio;

      axios.get('/consultarTokensUtilizados', {
        params: {
          fechaInicio: this.date,
          fechaFin: this.date2
        }
      })
          .then(res => {
            this.loading = false
            this.desserts = res.data.resultados
            console.log(JSON.stringify(this.desserts, null, 2))
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
  },
}
</script>

<style scoped>
.tituloReporte {
  text-align: center;
}
</style>