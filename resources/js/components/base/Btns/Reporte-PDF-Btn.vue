<template>
    <v-tooltip color="red darken-4" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="red"
                v-on="on"
                @click="onFocus"
            >
                mdi-file-pdf-outline
            </v-icon>
        </template>
        <span>Formato pdf</span>
    </v-tooltip>
</template>


<script>
    export default {
        name: "Reporte-PDF-Btn",

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>

<style scoped>

</style>
