import AjaxButton from '../ajaxButton';
import FormButton from '../formButton';

export default (() => {
    const init = () => {
        const userPaSelectTenantButtons = [...document.querySelectorAll('a[role="button"][data-type="paSelectTenant"]')];

        userPaSelectTenantButtons.map(userPaSelectTenantButton => {
            const isAjax = 'ajax' in userPaSelectTenantButton.dataset;
            const success = () => {
                window.location = "/analytics";
            }
            const notModified = () => {}

            isAjax && AjaxButton.init(userPaSelectTenantButton, 'post', null, success, notModified);
            isAjax || FormButton.init(userPaSelectTenantButton, 'get');
        });
    }

    return { init };
})();
