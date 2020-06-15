import { upperCaseFirst } from 'upper-case-first';
import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userDeleteButtons = [...document.querySelectorAll('a[role="button"][data-type="userDelete"]')];

        userDeleteButtons.map(userDeleteButton => {
            const isAjax = 'ajax' in userDeleteButton.dataset;
            const confirmation = {
                title: upperCaseFirst([
                    I18n.t('eliminazione'),
                    I18n.t("dell'utente"),
                ].join(' ')),
                body: [
                    '<p>',
                    I18n.t("Stai eliminando l'utente"),
                    '<strong>' + userDeleteButton.dataset.userName + '</strong>.<br>',
                    I18n.t("L'operazione è relativa solo alla pubblica amministrazione attualmente selezionata e non è reversibile."),
                    I18n.t("Se vuoi solo sospendere temporaneamente l'utente usa il tasto 'sospendi'."),
                    '</p>',
                    '<p>' + I18n.t('Sei sicuro di volere procedere comunque?') +'<p>',
                ].join(' '),
                image: '/images/user-suspend.svg',
            };
            const success = response => {
                Notification.showNotification(I18n.t('utente eliminato'), [
                    I18n.t("L'utente"),
                    '<strong>' + response.data.user_name + '</strong>',
                    response.data.administration ? I18n.t('è stato eliminato da') : I18n.t('è stato eliminato.'),
                    response.data.administration ? '<strong>' + response.data.administration + '</strong>.' : ''
                ].join(' '));

                Datatable.reload();
            }

            isAjax && AjaxButton.init(userDeleteButton, 'patch', confirmation, success);
            isAjax || FormButton.init(userDeleteButton, 'patch', confirmation);
        });
    }

    return { init };
})();
