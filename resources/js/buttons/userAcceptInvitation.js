import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userAcceptInvitationButtons = [...document.querySelectorAll('a[role="button"][data-type="acceptInvitation"]')];

        userAcceptInvitationButtons.map(userAcceptInvitationButton => {
            const isAjax = 'ajax' in userAcceptInvitationButton.dataset;
            const success = response => {
                Notification.showNotification(I18n.t('invito accettato'), [
                    I18n.t('Da adesso puoi accedere a'),
                    '<strong>' + response.data.name + '</strong>'
                ].join(' '), 'success', 'it-check-circle');

                Datatable.reload();
            }

            isAjax && AjaxButton.init(userAcceptInvitationButton, 'post', null, success);
            isAjax || FormButton.init(userAcceptInvitationButton, 'post');
        });
    }

    return { init };
})();
