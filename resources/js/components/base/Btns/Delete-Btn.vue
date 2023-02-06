<template>
    <v-tooltip color="red" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="red"
                v-on="on"
                @click="onFocus"
            >
                mdi-delete
            </v-icon>
        </template>
        <span>Eliminar</span>
    </v-tooltip>
</template>

<script>
    export default {
        name: 'DeleteBtn',

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>
