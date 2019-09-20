import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const websiteDeleteRestoreButtons = [...document.querySelectorAll('a[role="button"][data-type="websiteDeleteRestore"]')];

        websiteDeleteRestoreButtons.map(websiteDeleteRestoreButton => {
            const isAjax = 'ajax' in websiteDeleteRestoreButton.dataset;
            const isTrashed = websiteDeleteRestoreButton.dataset.trashed;
            const currentAction = isTrashed
                ? I18n.t('Ripristino')
                : I18n.t('Eliminazione');
            const confirmation = {
                title: [
                    currentAction,
                    I18n.t('del sito web'),
                ].join(' '),
                body: [
                    '<p>',
                    isTrashed ? I18n.t('Stai ripristinando il sito web') : I18n.t('Stai eliminando il sito web'),
                    '<strong>' + websiteDeleteRestoreButton.dataset.websiteName + '</strong>',
                    '</p>',
                    '<p>' + I18n.t('Sei sicuro?') +'<p>',
                ].join(' '),
                image: '/images/website-archive.svg',
            };
            const success = response => {
                Notification.showNotification(I18n.t('sito web modificato'), response.data.trashed
                    ? [
                        I18n.t('Il sito web'),
                        '<strong>' + response.data.website_name + '</strong>',
                        I18n.t('è stato eliminato.')
                    ].join(' ')
                    : [
                        I18n.t('Il sito web'),
                        '<strong>' + response.data.website_name + '</strong>',
                        I18n.t('è stato modificato correttamente.'),
                        '<br>',
                        I18n.t('Stato del sito web:'),
                        '<span class="badge website-status ' + response.data.status.toLowerCase() + '">',
                        response.data.status_description.toUpperCase(),
                        '</span>',
                    ].join(' '), 'success', 'it-check-circle');

                Datatable.reload();
            }

            isAjax && AjaxButton.init(websiteDeleteRestoreButton, 'patch', confirmation, success);
            isAjax || FormButton.init(websiteDeleteRestoreButton, 'patch', confirmation);
        });
    }

    return { init };
})();
