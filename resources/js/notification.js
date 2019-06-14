export default (() => {
    const showNotification = (title, message, status, icon, dismissable, position) => {
        const notificationId = (new Date().getTime()).toString(36);
        const notificationFragment = document.createRange().createContextualFragment(`
        <div class="notification ${dismissable ? 'dismissable' : ''} ${position || ''} with-icon ${status}" role="alert" aria-labelledby="${notificationId}-title" id="${notificationId}">
            <h5 id="${notificationId}-title"><svg class="icon"><use xlink:href="/svg/sprite.svg#it-${icon}"></use></svg>${title}</h5>
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
    }

    return { showNotification };
})();
