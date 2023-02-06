<template>
    <div class="text-center">
        <v-dialog
            v-model="dialog"
            persistent
            width="500"
        >
            <v-card>
                <v-card-title class="">
                    Porfavor espera un momento...
                </v-card-title>

                <v-card-text>
                    <v-progress-linear
                        color="primary accent-4"
                        indeterminate
                        rounded
                        height="6"
                    ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import {mapMutations} from 'vuex'

    export default {
        name: "dialog-full-loading",

        data: () => ({}),

        computed: {
            dialog: {
                get() {
                    return this.$store.state.dialog_full_loading
                },
                set(val) {
                    this.setDialog_full_loading(val)
                }
            },
        },

        watch: {
            dialog(val) {
                val || this.close()
            },
        },

        methods: {
            ...mapMutations(['setDialog_full_loading']),

            close() {
                this.dialog = false
            }
        }
    }
</script>

<style scoped>

</style>
