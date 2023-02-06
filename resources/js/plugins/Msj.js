/*import swal from 'sweetalert'*/
import iziToast from 'izitoast/dist/js/iziToast'
import 'izitoast/dist/css/iziToast.min.css'
// Here you can include some "default" settings
iziToast.settings({
    /*close: false,
    pauseOnHover: false,
    timeout: 3000,
    progressBar: false,*/
    //titleSize: '15',
    messageSize: '15',
    layout: 2
})

const Msj = {
    install(Vue) {
        Vue.prototype.$iziToast = {
            msg(res, progressBar) {
                progressBar.finish();
                let status = res.status;
                let title = res.data.title ? res.data.title : 'Atención!';
                let message = res.data.message;

                switch (status) {
                    case 200:
                        iziToast.success({
                            title: title,
                            message: message,
                            position: 'topRight',
                            transitionIn: 'bounceInUp'
                        });
                        break
                    default :
                        iziToast.warning({
                            title: title,
                            message: message,
                            position: 'topRight',
                            transitionIn: 'bounceInUp'
                        });
                }
            },
            fail(err, progressBar) {
                progressBar.fail();
                let status = err.response.status;
                let statusText = err.response.statusText;
                let message = err.response.data.message;
                console.error('Data: ', err.response.data, 'Status: ', status, 'statusText:', statusText);

                let title = err.response.data.title !== undefined ? err.response.data.title : 'Error (' + status + ')'
                switch (status) {
                    case 401:
                        iziToast.error({
                            title: title,
                            message: message,
                            position: 'topRight',
                            transitionIn: 'bounceInUp'
                        });
                        localStorage.removeItem('user')
                        location.reload()
                        break
                    case 403:
                    case 405:
                        iziToast.error({
                            title: title,
                            message: message,
                            position: 'topRight',
                            transitionIn: 'bounceInUp'
                        });
                        break
                    case 422:
                    case 429:
                        let errores = err.response.data.errors;
                        Object.keys(errores).forEach(function (key, i) {
                            iziToast.error({
                                //title: title + '<br><i class="mdi mdi-hand-pointing-right"></i> ' + message,
                                title: title,
                                message: errores[key][i],
                                position: 'topRight',
                                transitionIn: 'bounceInUp'
                            });
                        });
                        break
                    default :
                        iziToast.error({
                            title: title,
                            message: message,
                            position: 'topRight',
                            transitionIn: 'bounceInUp'
                        });
                }

            },
            withoutPermissions(mensaje) {
                iziToast.warning({
                    title: 'Ups...',
                    message: 'Sin permisos para ' + mensaje + ' en esta opción, presiona F5 o contacta al administrador.',
                    position: 'topRight',
                    transitionIn: 'bounceInUp'
                });
            },
            success(obj) {
                iziToast.success({
                    title: obj.title,
                    message: obj.message,
                    position: 'topRight',
                    transitionIn: obj.transitionIn ? obj.transitionIn : 'bounceInUp'
                });
            },
            info(obj) {
                iziToast.info({
                    title: obj.title,
                    message: obj.message,
                    position: 'topRight',
                    transitionIn: obj.transitionIn ? obj.transitionIn : 'bounceInUp'
                });
            },
            warning(titulo, mensaje){
                iziToast.warning({
                    title: titulo,
                    message: mensaje,
                    position: 'topRight',
                    transitionIn: 'bounceInUp'
                });
            },
            error(obj) {
                iziToast.error({
                    title: obj.title,
                    message: obj.message,
                    position: obj.position ? obj.position : 'topCenter',
                    transitionIn: obj.transitionIn ? obj.transitionIn : 'bounceInUp'
                });
            },
            importComponent(err, progressBar) {
                progressBar.fail();
                iziToast.error({
                    title: 'Error (404)',
                    message: `Archivo ${err}.vue no encontrado`,
                    position: 'topRight',
                    transitionIn: 'bounceInUp'
                });
            }
        }
    }
};

export default Msj;
