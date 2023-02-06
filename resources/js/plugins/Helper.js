const Helper = {
    install(Vue) {
        Vue.prototype.$helper = {
            getVariablesENV(object) {
                if (!object.appName) {
                    console.error('No se ha indicado APP_NAME en fichero .env')
                }
                if (!object.nameServerAplicaciones) {
                    console.error('No se ha indicado NAME_SERVER_APLICACIONES para reestablecer contraseña en fichero .env')
                }
            },
            getPermissions(array) {
                let data = {}
                Object.keys(array).forEach(function (permiso) {
                    let s = array[permiso].split('.', 2)[0]
                    data[s] = true
                })
                return data
            },
            hasValue(value) {
                return (value !== undefined) && (value !== null) && (value !== "")
            },
            datePickerFormatted(date) {
                if (!date)
                    return null
                const [year, month, day] = date.split('-')
                return `${day}/${month}/${year}`
            }
        }
    }
};

export default Helper;
