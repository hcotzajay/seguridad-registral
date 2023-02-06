//https://momentjs.com/docs/
import moment from 'moment'
//import momentDuration from 'moment-duration-format'

moment.locale('es')
/*moment.locale('es', {
    calendar: {
        lastDay: '[Ayer a las] LT',
        sameDay: '[Hoy a las] LT',
        nextDay: '[Mañana a las] LT',
        lastWeek: '[la semana pasada] dddd [a las] LT',
        nextWeek: 'dddd [a las] LT',
        sameElse: 'L'
    }
});*/

const Moment = {
    install(Vue) {
        Vue.prototype.$moment = {
            now() {
                return moment();
            },
            get(date) {
                return moment(date);
            },
            since(date) {
                return moment(date).fromNow();
            },
            diffInMinutes(start, end) {
                /*let a = moment('2018-05-30 11:30:00', 'YYYY-MM-DD, h:mm:ss');*/
                let a = moment(start, 'YYYY-MM-DD, h:mm:ss');
                let b = moment(end, 'YYYY-MM-DD, h:mm:ss');
                //return b.diff(a, 'hours', true);
                return this.duration(Math.round(b.diff(a, 'minutes', true) * 100) / 100);
            },
            diffInDays(start, end) {
                let a = moment(start, 'YYYY-MM-DD');
                let b = moment(end, 'YYYY-MM-DD');
                return b.diff(a, 'days');
            },
            addTime(start, value, key) {
                return moment(start, 'YYYY-MM-DD').add(value, key);
            },
            duration(value) {
                return moment.duration(value, "minutes").format("d [días] h [hrs] m [min] s [seg]");
            }
        }
    }
}

export default Moment;
