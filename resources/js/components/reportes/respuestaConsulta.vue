<template>
  <v-container>
    <template>
      <v-row justify="center">
        <v-dialog
            v-model="dialog"
            persistent
            max-width="1200"
        >
          <v-card>
            <v-card-title>
              <span class="text-h6">Resultados de la búsqueda</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.CUI"
                        label="Cui"
                        class="event-none"
                        readonly
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.PRIMER_NOMBRE"
                        label="Primer Nombre"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.SEGUNDO_NOMBRE === null ? 'NULL' : enviarRespuestaConsulta.SEGUNDO_NOMBRE"
                        label="Segundo Nombre"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.TERCER_NOMBRE === null ? 'NULL' : enviarRespuestaConsulta.TERCER_NOMBRE"
                        label="Tercer Nombre"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.PRIMER_APELLIDO"
                        label="Primer Apellido"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.SEGUNDO_APELLIDO === null ? 'NULL' : enviarRespuestaConsulta.SEGUNDO_APELLIDO"
                        label="Segundo apellido"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.FECHA_NACIMIENTO"
                        label="Fecha de nacimiento"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.GENERO"
                        label="Genero"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.ESTADO_CIVIL"
                        label="Estado Civil"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.NACIONALIDAD"
                        label="Nacionalidad"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.FECHA_DEFUNCION === null ? 'NULL' : enviarRespuestaConsulta.FECHA_DEFUNCION"
                        label="Fecha defunción"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.OCUPACION"
                        label="Ocupación"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      class="not-allowed"
                  >
                    <v-text-field
                        v-model="enviarRespuestaConsulta.VECINDAD"
                        label="Vecindad"
                        readonly
                        class="event-none"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <align-actions>
                <cancel-btn
                    @click="cerrar"
                > Cerrar
                  <br>
                  <v-icon
                      right
                      dark
                  >
                    mdi-cancel
                  </v-icon>
                </cancel-btn>
                <v-btn
                    @click="generarReporte"
                    rounded

                > Generar Reporte
                  <br>
                  <v-icon
                      right
                      dark
                      color="red"
                  >
                    mdi-file-pdf-box-outline
                  </v-icon>
                </v-btn>
              </align-actions>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </template>
  </v-container>
</template>

<script>
export default {
  name: "respuestaConsulta",
  data: () => ({
    dialog: true,
    datosDetalleCorrecciones: [],
    datosDetalle: []
  }),
  methods: {
    cerrar() {
      this.$emit('cerrar')
    },
    generarReporte() {
      if (this.enviarRespuestaConsulta.CUI != null) {
        let datos = {
          datosBusqueda: this.enviarRespuestaConsulta,
          fecha: this.enviarFecha,
          hora: this.enviarHora
        }
        let myJson = JSON.stringify(datos)
        window.open('/generarReporteResultadoBusqueda?json=' + myJson)
      } else {
        this.$iziToast.warning('Alerta', 'No existen datos para generar el reporte.')
        return false
      }
    }
  },
  props: {
    enviarRespuestaConsulta: [],
    enviarFecha: [],
    enviarHora: []
  },
}
</script>

<style scoped>
.not-allowed {
  cursor: not-allowed;
}

.event-none {
  pointer-events: none;
}
</style>