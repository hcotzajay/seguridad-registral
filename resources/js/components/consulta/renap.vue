<template>
  <v-container>
    <v-card
        elevation="12"
        class="mx-auto my-12"
        max-width="1500"
    >
      <v-container>
        <v-card-actions class="pt-0">
          <align-actions>
            <v-img
                src="../storage/assets/img/logoRenap.png"
                max-height="100"
                max-width="300"
            ></v-img>
          </align-actions>
        </v-card-actions>
      </v-container>
      <div v-if="seleccionConsultas">
        <h3 style="text-align: center">Seleccione una opción</h3>
        <v-container>
          <v-row>
            <v-col
                cols="12"
                md="6"
            >
              <v-alert
                  color="cyan"
                  border="left"
                  elevation="2"
                  colored-border
                  icon="mdi-clipboard-list-outline"
                  @click="consultaCui"
                  class="espaciadoVoBo"
                  style="cursor:pointer;"
              >
                <strong>Consulta CUI</strong>
              </v-alert>
            </v-col>
            <v-col
                cols="12"
                md="6"
            >
              <v-alert
                  color="cyan"
                  border="left"
                  elevation="2"
                  colored-border
                  icon="mdi-clipboard-list-outline"
                  class="espaciadoVoBo"
                  style="cursor:pointer;"
                  @click="consultaNombres"
              >
                <strong>Consulta por nombres y apellidos</strong>
              </v-alert>
            </v-col>
          </v-row>
        </v-container>
      </div>
      <div>
        <consulta-cui v-if="seleccionConsultaCui" @regresar="regresar"></consulta-cui>
        <consulta-nombres v-if="seleccionConsultaNombres" @regresarNombres="regresarNombres"></consulta-nombres>
      </div>

    </v-card>
  </v-container>
</template>

<script>
import ConsultaCui from "./consultaCui";
import consultaNombres from "./consultaNombres";

export default {
  name: "renap",
  components: {ConsultaCui, consultaNombres},
  data: () => ({
    seleccionConsultas: true,
    seleccionConsultaCui: false,
    seleccionConsultaNombres: false,
  }),
  methods: {
    consultaCui() {
      this.seleccionConsultas = false
      this.seleccionConsultaCui = true
      this.seleccionConsultaNombres = false

    },
    consultaNombres() {
      this.seleccionConsultas = false
      this.seleccionConsultaNombres = true
      this.seleccionConsultaCui = false
    },
    regresar() {
      this.seleccionConsultas = true
      this.seleccionConsultaCui = false
      this.seleccionConsultaNombres = false
    },
    regresarNombres() {
      this.seleccionConsultas = true
      this.seleccionConsultaCui = false
      this.seleccionConsultaNombres = false
    },

  }
}
</script>

<style scoped>

</style>
