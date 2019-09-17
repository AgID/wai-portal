import Datatable from '../datatables';
import Notification from '../notification';
import I18n from '../i18n';
import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const websiteArchiveUnarchiveButtons = [...document.querySelectorAll('a[role="button"][data-type="websiteArchiveUnarchive"]')];

        websiteArchiveUnarchiveButtons.map(websiteArchiveUnarchiveButton => {
            const isAjax = 'ajax' in websiteArchiveUnarchiveButton.dataset;
            const currentStatus = websiteArchiveUnarchiveButton.dataset.currentStatus.toLowerCase();
            const currentAction = 'active' === currentStatus
                ? I18n.t('Archiviazione')
                : I18n.t('Riattivazione');
            const confirmation = {
                title: [
                    currentAction,
                    I18n.t('del sito web'),
                ].join(' '),
                body: [
                    '<p>',
                    I18n.t('Stai cambiando lo stato del sito.') + '<br>',
                    I18n.t('Stato attuale:'),
                    '<span class="badge website-status ' + currentStatus + '">',
                    websiteArchiveUnarchiveButton.dataset.currentStatusDescription.toUpperCase(),
                    '</span><br>',
                    I18n.t('Il nuovo stato sarà:'),
                    '<span class="badge website-status ' + ('active' === currentStatus ? 'archived' : 'active') + '">',
                    ('active' === currentStatus ? I18n.t('archiviato') : I18n.t('attivo')).toUpperCase(),
                    '</span><br>',
                    '</p>',
                    '<p>' + I18n.t('Sei sicuro?') +'<p>',
                ].join(' '),
            };
            const success = response => {
                Notification.showNotification(I18n.t('sito web modificato'), [
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

            isAjax && AjaxButton.init(websiteArchiveUnarchiveButton, 'patch', confirmation, success);
            isAjax || FormButton.init(websiteArchiveUnarchiveButton, 'patch', confirmation);
        });
    }

    return { init };
})();
