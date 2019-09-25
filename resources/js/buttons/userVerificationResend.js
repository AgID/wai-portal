import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userVerificationResendButtons = [...document.querySelectorAll('a[role="button"][data-type="verificationResend"]')];

        userVerificationResendButtons.map(userVerificationResendButton => {
            const isAjax = 'ajax' in userVerificationResendButton.dataset;
            const success = response => {
                Notification.showNotification(I18n.t('verifica indirizzo email'), [
                    I18n.t("Una nuova email di verifica è stata inviata all'indirizzo"),
                    '<strong>' + response.data.email + '</strong>',
                ].join(' '), 'success', 'it-check-circle',);
            }

            isAjax && AjaxButton.init(userVerificationResendButton, 'get', null, success);
            isAjax || FormButton.init(userVerificationResendButton, 'get');
        });
    }

    return { init };
})();
