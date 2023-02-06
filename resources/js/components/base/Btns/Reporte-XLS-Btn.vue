<template>
    <v-tooltip color="green darken-4" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="green"
                v-on="on"
                @click="onFocus"
            >
                mdi-file-excel-outline
            </v-icon>
        </template>
        <span>Formato excel</span>
    </v-tooltip>
</template>

<script>
    export default {
        name: "Reporte-XLS-Btn",

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>

<style scoped>

</style>
