import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';

export default (() => {
    const init = () => {
        const userVerificationResendButtons = [...document.querySelectorAll('a[role="button"][data-type="verificationResend"]')];

        userVerificationResendButtons.map(userVerificationResendButton => {
            const success = response => {
                Notification.showNotification(I18n.t('verifica indirizzo email'), [
                    I18n.t("Una nuova email di verifica è stata inviata all'indirizzo"),
                    '<strong>' + response.data.email + '</strong>',
                ].join(' '), 'success', 'it-check-circle',);
            }

            userVerificationResendButton.dataset.ajax && AjaxButton.init(userVerificationResendButton, 'get', null, success);
        });
    }

    return { init };
})();
