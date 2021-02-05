import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const websiteCheckTrackingButtons = [...document.querySelectorAll('a[role="button"][data-type="checkTracking"]')];

        websiteCheckTrackingButtons.map(websiteCheckTrackingButton => {
            const isAjax = 'ajax' in websiteCheckTrackingButton.dataset;
            const success = response => {
                const showWhenActiveElements = [...document.querySelectorAll('.show-when-active')];
                const hideWhenActiveElements = [...document.querySelectorAll('.hide-when-active')];
                const publicAdministrationTenantElement = document.querySelector('.it-nav-wrapper .it-tenant');
                const headerSocialsElement = document.querySelector('.it-nav-wrapper .it-socials');

                Notification.showNotification(I18n.t('sito attivato'), [
                    I18n.t('Il sito'),
                    '<strong>' + response.data.website_name + '</strong>',
                    I18n.t('ha iniziato a tracciare il traffico.'),
                ].join(' '), 'success', 'it-check-circle');

                showWhenActiveElements.map(showWhenActiveElement => {
                    showWhenActiveElement.classList.remove('d-none');
                });

                hideWhenActiveElements.map(hideWhenActiveElement => {
                    hideWhenActiveElement.classList.add('d-none');
                });

                headerSocialsElement && headerSocialsElement.classList.remove('d-md-flex');
                publicAdministrationTenantElement && publicAdministrationTenantElement.classList.add('d-md-block');

                Datatable.reload();
            }

            const notModified = () => {
                Notification.showNotification(I18n.t('sito non attivo'), [
                    I18n.t('Il sito'),
                    '<strong>' + websiteCheckTrackingButton.dataset.websiteName + '</strong>',
                    I18n.t('non ha ancora iniziato a tracciare il traffico.'),
                ].join(' '), 'info', 'it-info-circle');
            }

            isAjax && AjaxButton.init(websiteCheckTrackingButton, 'get', null, success, notModified);
            isAjax || FormButton.init(websiteCheckTrackingButton, 'get');
        });
    }

    return { init };
})();
