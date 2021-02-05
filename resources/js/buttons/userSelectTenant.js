import FormButton from '../formButton';
import AjaxButton from '../ajaxButton';

export default (() => {
    const init = () => {
        const userSelectTenantButtons = [...document.querySelectorAll('a[role="button"][data-type="selectTenant"]')];

        userSelectTenantButtons.map(userSelectTenantButton => {
            const isAjax = 'ajax' in userSelectTenantButton.dataset;
            const success = () => {
                window.location = "/analytics";
            }

            isAjax && AjaxButton.init(userSelectTenantButton, 'post', null, success);
            isAjax || FormButton.init(userSelectTenantButton, 'post');
        });
    }

    return { init };
})();
