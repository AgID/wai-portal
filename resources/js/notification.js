import I18n from './i18n';

export default (() => {
    const showNotification = (title, message, status, icon, dismissable = true, position = 'top-fix') => {
        const notificationId = (new Date().getTime()).toString(36);
        const notificationFragment = document.createRange().createContextualFragment(`
        <div class="notification ${dismissable ? 'dismissable' : ''} ${position} with-icon ${status}" role="alert" aria-labelledby="${notificationId}-title" id="${notificationId}">
            <h5 id="${notificationId}-title"><svg class="icon"><use xlink:href="/svg/sprite.svg#${icon}"></use></svg>${title}</h5>
            <p>${message}</p>
            ${dismissable ? `
            <button type="button" class="btn notification-close">
                <svg class="icon"><use xlink:href="/svg/sprite.svg#it-close"></use></svg>
                <span class="sr-only">close</span>
            </button>` : ''}
        </div>
        `);
        document.body.appendChild(notificationFragment);

        const notificationElement = document.getElementById(notificationId);
        setTimeout(() => {
            notificationElement.classList.add('show');
        });

        dismissable && notificationElement.querySelector('.notification-close').addEventListener('click', () => {
            setTimeout(() => {
                document.body.removeChild(notificationElement);
            }, 100);
        });
    };

    const showServerErrorNotification = () => {
        showNotification(I18n.t('errore'), [
            I18n.t('Si è verificato un errore relativamente alla tua richiesta.'),
            I18n.t('Puoi riprovare più tardi'),
            I18n.t('o'),
            '<a href="/contacts">' + I18n.t('contattare il supporto tecnico') + '</a>.',
        ].join(' '), 'error', 'it-close-circle');
    };

    const init = () => {
        const notificationInPage = document.querySelector('.notification-in-page');

        notificationInPage && showNotification(
            notificationInPage.dataset.title,
            notificationInPage.dataset.message,
            notificationInPage.dataset.status,
            notificationInPage.dataset.icon,
            notificationInPage.dataset.dismissable,
            notificationInPage.dataset.position,
        );
    };

    return { init, showNotification, showServerErrorNotification };
})();
