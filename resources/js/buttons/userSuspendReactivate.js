import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userSuspendReactivateButtons = [...document.querySelectorAll('a[role="button"][data-type="userSuspendReactivate"]')];

        userSuspendReactivateButtons.map(userSuspendReactivateButton => {
            const isAjax = 'ajax' in userSuspendReactivateButton.dataset;
            const currentStatus = userSuspendReactivateButton.dataset.currentStatus.toLowerCase();
            const currentAction = 'active' === currentStatus
                ? I18n.t('Sospensione')
                : I18n.t('Riattivazione');
            const confirmation = {
                title: [
                    currentAction,
                    I18n.t("dell'utente"),
                ].join(' '),
                body: [
                    '<p>',
                    I18n.t("Stai cambiando lo stato dell'utente"),
                    '<strong>' + userSuspendReactivateButton.dataset.userName + '</strong>',
                    '<br>',
                    I18n.t('Stato attuale:'),
                    '<span class="badge user-status ' + currentStatus + '">',
                    userSuspendReactivateButton.dataset.currentStatusDescription.toUpperCase(),
                    '</span><br>',
                    I18n.t('Il nuovo stato sarà:'),
                    '<span class="badge user-status ' + ('active' === currentStatus ? 'suspended' : 'active') + '">',
                    ('active' === currentStatus ? I18n.t('sospeso') : I18n.t('attivo')).toUpperCase(),
                    '</span><br>',
                    '</p>',
                    '<p>' + I18n.t('Sei sicuro?') +'<p>',
                ].join(' '),
                image: '/images/user-suspend.svg',
            };
            const success = response => {
                Notification.showNotification(I18n.t('utente modificato'), [
                    I18n.t("L'utente"),
                    '<strong>' + response.data.user_name + '</strong>',
                    I18n.t('è stato modificato correttamente.'),
                    '<br>',
                    I18n.t("Stato dell'utente:"),
                    '<span class="badge user-status ' + response.data.status.toLowerCase() + '">',
                    response.data.status_description.toUpperCase(),
                    '</span>',
                ].join(' '), 'success', 'it-check-circle');

                Datatable.reload();
            }

            isAjax && AjaxButton.init(userSuspendReactivateButton, 'patch', confirmation, success);
            isAjax || FormButton.init(userSuspendReactivateButton, 'patch', confirmation);
        });
    }

    return { init };
})();
