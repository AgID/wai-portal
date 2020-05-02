import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userPaActivationButtons = [...document.querySelectorAll('a[role="button"][data-type="paActivation"]')];

        userPaActivationButtons.map(userPaActivationButton => {
            const isAjax = 'ajax' in userPaActivationButton.dataset;
            const success = response => {
                const showWhenActiveElements = [...document.querySelectorAll('.show-when-active')];

                Notification.showNotification(I18n.t('pubblica amministrazione confermata'), [
                    I18n.t('Adesso puoi accedere a'),
                    '<strong>' + response.data.name + '</strong>'
                ].join(' '), 'success', 'it-check-circle');

                showWhenActiveElements.map(showWhenActiveElement => {
                    showWhenActiveElement.classList.remove('d-none');
                });
                Datatable.reload();
            }
            const notModified = () => {}

            isAjax && AjaxButton.init(userPaActivationButton, 'post', null, success, notModified);
            isAjax || FormButton.init(userPaActivationButton, 'get');
        });
    }

    return { init };
})();
