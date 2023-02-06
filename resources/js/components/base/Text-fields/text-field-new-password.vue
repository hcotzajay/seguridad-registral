<template>
    <v-text-field
        type="password"
        prepend-icon="mdi-form-textbox-password"
        counter
        class="input-group--focused"
        minlength="8"
        required
        v-bind="$attrs"
        v-on="$listeners"
        :rules="[rulesPass.required, rulesPass.spaces, rulesPass.min, rulesPass.regex]"
    >
        <slot/>
    </v-text-field>
</template>

<script>
    export default {
        name: "text-field-new-password",

        data: () => ({
            rulesPass: {
                required: v => !!v || 'Obligatorio.',
                spaces: v => (v || '').indexOf(' ') < 0 || 'No se permiten espacios',
                min: v => !!v && (v.length >= 8) || 'Debe tener mínimo 8 caracteres',
                regex: v => /^(?=(?:.*\d){2})(?=(?:.*[A-Z]){2})(?=(?:.*[a-z]){2})\S{8,}$/.test(v) || 'No cumple con las políticas de seguridad'
            },
        }),
    }
</script>

<style scoped>

</style>
