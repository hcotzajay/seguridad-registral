<template>
    <v-container>
        <v-card class="mx-auto" max-width="auto">
        <v-card-actions>
                <v-btn @click="genToken">
                    <v-icon left color="red">mdi-file-pdf-outline</v-icon>
                    Consultar
                </v-btn>
        </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>

import consulta_nombres from "./consulta_nombres";
export default {
    name: "consulta",
    realizarConsulta() {
      /*  this.bloqueo = true
        this.generarToken()
        axios.post('/realizarConsulta')
            .then(res => {
                this.$iziToast.msg(res, this.$Progress)
            }).catch(err => {
            this.$iziToast.fail(err, this.$Progress)
        }).finally(() => {
            window.open('/pdf', '_blank')
            this.bloqueo = false
            this.cargando = false
            this.close()
        })*/
    },
    genToken() {
        console.log("Hola Mundo")
       /* this.token = []
        this.loading = true;
        axios.get('tokens/gToken')
            .then(res => {
                this.token = res.data;
         //       console.log("Prueba: "+res.data);
            })
            .catch(err => {
                this.$iziToast.fail(err, this.$Progress)
            })
            .finally(() => {
                this.loading = false
            })
            */
    },
}
</script>

<style scoped>

</style>
