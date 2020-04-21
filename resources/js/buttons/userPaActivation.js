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
                // const publicAdministrationTenantElement = document.querySelector('.it-nav-wrapper .it-tenant');
                // const headerSocialsElement = document.querySelector('.it-nav-wrapper .it-socials');

                Notification.showNotification(I18n.t('pubblica amministrazione confermata'), [
                    I18n.t('Ora puoi accedere a'),
                    '<strong>' + response.data.name + '</strong>',
                    '<a class="btn btn-primary" role="button" href="/analytics">Procedi</a>'
                ].join(' '), 'success', 'it-check-circle');

                showWhenActiveElements.map(showWhenActiveElement => {
                    showWhenActiveElement.classList.remove('d-none');
                });
                // headerSocialsElement && headerSocialsElement.classList.remove('d-md-flex');
                // publicAdministrationTenantElement && publicAdministrationTenantElement.classList.add('d-md-block');

                Datatable.reload();
            }

            const notModified = () => {

            }

            isAjax && AjaxButton.init(userPaActivationButton, 'get', null, success, notModified);
            isAjax || FormButton.init(userPaActivationButton, 'get');
        });
    }

    return { init };
})();
