import axios from 'axios';
import { cacheAdapterEnhancer } from 'axios-extensions';
import 'bootstrap-italia';
import Notification from './notification';
import I18n from './i18n';

/**
 * Keep jQuery in the window object for legacy scripts
 */

window.$ = window.jQuery = jQuery;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios.create({
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Cache-Control': 'no-cache',
      'Content-Type': 'application/json'
    },
    adapter: cacheAdapterEnhancer(axios.defaults.adapter)
});
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (axios.isCancel(error)) {
            // request deduped
        } else if (401 === error.response?.status) {
            Notification.showNotification(I18n.t('sessione scaduta'), [
                I18n.t('La sessione è scaduta a causa di inattività sulla pagina.'),
                ' <a href="#" onclick="location.reload(true);return false;">',
                I18n.t('Ricarica la pagina'),
                '</a> ',
                I18n.t('per continuare.'),
            ].join(''), 'error', 'it-close-circle', false);
        }

        return Promise.reject(error);
    }
);

// See https://github.com/axios/axios/issues/1445
window.axios.isCancel = axios.isCancel;
window.axios.CancelToken = axios.CancelToken;

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    // eslint-disable-next-line no-console
    console.error('CSRF token not found');
}
