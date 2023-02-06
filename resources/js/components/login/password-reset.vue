<template>
    <v-expand-transition>
        <v-card
            v-if="reveal"
            class="transition-fast-out-slow-in v-card--reveal"
            style="height: 100%;"
        >
            <v-container fill-height fluid class="px-0 pt-0">
                <v-toolbar class="elevation-0" style="width: auto;">
                    <v-flex class="text-center">
                        <v-toolbar-title>Recuperar contraseña</v-toolbar-title>
                    </v-flex>
                </v-toolbar>
                <v-card-text class="pt-0">
                    <v-form ref="form" v-model="valid" class="mx-9" @submit.prevent="recuperar_password">
                        <div>
                            <v-alert dense outlined class="py-1 mb-2"
                                     type="info" icon="mdi-email-send-outline"
                            >
                                <small>Te enviaremos un correo electrónico con tu nueva contraseña.</small>
                            </v-alert>
                            <v-alert dense outlined class="py-1 border"
                                     type="warning" icon="mdi-alert-outline"
                            >
                                <small>Al ingresar al sistema deberás cambiarla por una de tu confianza.</small>
                            </v-alert>
                        </div>
                        <v-text-field prepend-icon="mdi-account" name="usuario" label="Usuario" v-model="form.usuario"
                                      :rules="[rules.required, rules.spaces]"
                                      hint="El mismo que utilizas para ingresar al sistema."
                                      class="pb-3" persistent-hint clearable
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <align-actions>
                    <cancel-btn @click="cancel">Cancelar</cancel-btn>
                    <action-btn :loading="loading" @click="recuperar_password">Recuperar</action-btn>
                </align-actions>
            </v-container>
        </v-card>
    </v-expand-transition>
</template>

<script>
    import md5 from "crypto-js/md5";

    export default {
        name: "password-reset",

        props: {
            reveal: {
                type: Boolean,
                default: false,
                required: true,
            }
        },

        data: () => ({
            valid: false,
            loading: false,
            form: {usuario: ''},
            disable: false,
            rules: {
                required: v => !!v || 'Requerido.',
                spaces: v => (v || '').indexOf(' ') < 0 || 'No se permiten espacios',
            },
        }),

        watch: {
            reveal() {
                this.form.usuario = ''
            }
        },

        methods: {
            recuperar_password() {
                if (!this.$refs.form.validate()) {
                    return
                }

                this.$Progress.start();
                this.loading = true

                axios.post('/login/password-reset', this.form)
                    .then(res => {
                        this.$iziToast.msg(res, this.$Progress)
                        this.$refs.form.resetValidation()
                        this.cancel()
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
                    .finally(() => {
                        this.form.usuario = ''
                        this.loading = false
                    })
            },

            cancel() {
                this.$emit('cancel_reset')
            },
        },
    }
</script>

<style scoped>
    .v-card--reveal {
        bottom: 0;
        opacity: 1 !important;
        position: absolute;
        width: 100%;
    }
</style>
