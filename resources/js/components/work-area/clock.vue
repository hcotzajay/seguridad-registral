<template>
    <h4
        class="text-center font-weight-black"
    >
        {{ hora }}
        <br>
        <span class="font-italic font-weight-bold hidden-md-and-down">
            {{ fecha }}
        </span>
    </h4>
</template>

<script>
    export default {
        name: "clock",

        data: () => ({
            hora: null,
            fecha: null,
        }),

        created() {
            this.hora = this.updateTime()
            this.fecha = this.$moment.now().format('dddd D MMM');
        },

        methods: {
            updateTime() {
                this.hora = this.$moment.now().format('LTS a');
                let t = setTimeout(this.updateTime, 500);
            },
        }
    }
</script>

<style scoped>

</style>
