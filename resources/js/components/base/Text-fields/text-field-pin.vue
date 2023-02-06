<template>
    <v-text-field
        type="password"
        prepend-icon="mdi-lastpass"
        counter="4"
        class="input-group--focused"
        placeholder="1234"
        maxlength="4"
        required
        v-bind="$attrs"
        v-on="$listeners"
        :rules="[rulesPIN.required, rulesPIN.isNumeric, rulesPIN.spaces, rulesPIN.regex]"
    >
        <slot/>
    </v-text-field>
</template>

<script>
    export default {
        name: "text-field-pin",

        data: () => ({
            rulesPIN: {
                required: v => !!v || 'Obligatorio.',
                isNumeric: v => !isNaN(parseInt(v)) || 'Solo se permiten dígitos',
                spaces: v => (v || '').indexOf(' ') < 0 || 'No se permiten espacios',
                /*min: v => !!v && (v.length === 4) || 'PIN debe ser de 4 dígitos',*/
                regex: v => /^(?=(?:.*\d){4})\S{4,}$/.test(v) || 'PIN debe ser de 4 dígitos'
            },
        }),
    }
</script>

<style scoped>

</style>
