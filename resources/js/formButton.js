import Confirmation from './confirmation';

export default (() => {
    const buttonSubmission = (button, method) => {
        const formId = (new Date().getTime()).toString(36);
        const token = document.head.querySelector('meta[name="csrf-token"]');
        const formFragment = document.createRange().createContextualFragment(`
        <form id="${formId}" action="${button.getAttribute('href')}" method="post">
            <input type="hidden" name="_method" value="${method}">
            <input type="hidden" name="_token" value="${token.content}">
        </form>
        `);
        document.body.appendChild(formFragment);
        document.getElementById(formId).submit();
    };

    const init = (button, method, confirmation) => {
        // Remove all previously attached event listeners
        const buttonClone = button.cloneNode(true);
        button.parentNode.replaceChild(buttonClone, button);
        button = buttonClone;

        button.addEventListener('click', event => {
            event.preventDefault();

            confirmation && Confirmation.showConfirmation(confirmation.title, confirmation.body, confirmation.image).then(() => {
                buttonSubmission(button, method);
            }).catch(() => {});

            confirmation || buttonSubmission(button, method);
        });

        button.disabled = false;
        button.classList.remove('disabled');
        button.setAttribute('aria-disabled', false);
    };

    return { init };
})();

