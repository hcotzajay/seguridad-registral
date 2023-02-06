<template>
    <v-container>
        <h1>Cambio de contraseña</h1>

        <v-card
            class="mx-auto"
            max-width="700"
            outlined
            color=""
        >
            <v-alert
                icon="mdi-shield-lock-outline"
                dense
                outlined
                prominent
                text
                type="warning"
            >
                <ul>
                    <li class="pb-3">
                        Por las políticas de seguridad, se requiere que la contraseña cumpla con:
                        <ul>
                            <li>Tener como mínimo <strong>8</strong> caracteres.</li>
                            <li>Tener al menos <strong>2</strong> mayúsculas, <strong>2</strong> minúsculas y <strong>2</strong> números.</li>
                        </ul>
                    </li>
                    <li>
                        Ejemplo <span class="font-italic font-weight-thin">(no use el ejemplo para establecer su contraseña)</span>: miPAss11
                    </li>
                </ul>
            </v-alert>
            <v-card-title>
                <span class="">Por favor ingrese los siguientes datos:</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col cols="12">
                                <text-field-password
                                    label="Contraseña actual"
                                    hint="La mísma que utilizas para ingresar al sistema."
                                    persistent-hint
                                    v-model="form.password"
                                    @keyup.enter="setPassword"
                                ></text-field-password>
                            </v-col>
                            <v-col cols="6">
                                <text-field-new-password
                                    label="Nueva contraseña"
                                    v-model="form.new_password"
                                    @keyup.enter="setPassword"
                                ></text-field-new-password>
                            </v-col>
                            <v-col cols="6">
                                <text-field-new-password
                                    label="Confirme su nueva contraseña"
                                    v-model="form.confirm_new_password"
                                    @keyup.enter="setPassword"
                                ></text-field-new-password>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-container>
                <small>*Todos los campos son obligatorios</small>
            </v-card-text>
            <align-actions
                class="mb-3"
            >
                <action-btn
                    :loading="loading"
                    @click="setPassword"
                >
                    Cambiar
                </action-btn>
            </align-actions>
        </v-card>
    </v-container>
</template>

<script>
    import md5 from "crypto-js/md5"
    import TextFieldPassword from "../base/Text-fields/text-field-password";
    import TextFieldNewPassword from "../base/Text-fields/text-field-new-password";

    export default {
        name: "cambiapass",

        components: {TextFieldNewPassword, TextFieldPassword},

        data: () => ({
            valid: false,
            form: {password: '', new_password: '', confirm_new_password: ''},
            defaultForm: {password: '', new_password: '', confirm_new_password: ''},
            loading: false,
        }),

        methods: {
            setPassword() {
                if (!this.$refs.form.validate()) {
                    return
                }

                if (this.form.new_password !== this.form.confirm_new_password) {
                    this.$iziToast.error({title: 'Error en contraseña', message: 'La nueva contraseña y su confirmación no coinciden.'})
                    return
                }
                this.$Progress.start();
                this.loading = true

                axios.post('/personal/cambiaPassword', {
                    password: window.btoa(md5(this.form.password)),
                    new_password: window.btoa(md5(this.form.new_password))
                })
                    .then(res => {
                        this.$iziToast.msg(res, this.$Progress);
                        this.form = Object.assign({}, this.defaultForm)
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
                    .finally(() => {
                        this.loading = false
                        this.$refs.form.resetValidation()
                    })
            },
        },
    }
</script>

<style scoped>

</style>
