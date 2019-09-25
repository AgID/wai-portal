import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userDeleteRestoreButtons = [...document.querySelectorAll('a[role="button"][data-type="userDeleteRestore"]')];

        userDeleteRestoreButtons.map(userDeleteRestoreButton => {
            const isAjax = 'ajax' in userDeleteRestoreButton.dataset;
            const isTrashed = userDeleteRestoreButton.dataset.trashed;
            const currentAction = isTrashed
                ? I18n.t('Ripristino')
                : I18n.t('Eliminazione');
            const confirmation = {
                title: [
                    currentAction,
                    I18n.t("dell'utente"),
                ].join(' '),
                body: [
                    '<p>',
                    isTrashed ? I18n.t("Stai ripristinando l'utente") : I18n.t("Stai eliminando l'utente"),
                    '<strong>' + userDeleteRestoreButton.dataset.userName + '</strong>',
                    '</p>',
                    '<p>' + I18n.t('Sei sicuro?') +'<p>',
                ].join(' '),
                image: '/images/user-suspend.svg',
            };
            const success = response => {
                Notification.showNotification(I18n.t('utente modificato'), response.data.trashed
                    ? [
                        I18n.t("L'utente"),
                        '<strong>' + response.data.user_name + '</strong>',
                        I18n.t('è stato eliminato.')
                    ].join(' ')
                    : [
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

            isAjax && AjaxButton.init(userDeleteRestoreButton, 'patch', confirmation, success);
            isAjax || FormButton.init(userDeleteRestoreButton, 'patch', confirmation);
        });
    }

    return { init };
})();
