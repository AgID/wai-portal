export default (() => {
    const initToggles = () => {
        const adminToggle = document.getElementById('is_admin');
        const permissionsToggles = [...document.querySelectorAll(`input[type="checkbox"][name^="permissions"]:not([disabled])`)];

        permissionsToggles.map(toggle => {
            toggle.addEventListener('change', event => {
                if (!event.currentTarget.checked) {
                    adminToggle && (adminToggle.checked = false);
                }
                if ('read-analytics' === event.currentTarget.value && !event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-manage-analytics`).checked = false;
                }
                if ('manage-analytics' === event.currentTarget.value && event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-read-analytics`).checked = true;
                }
            })
        });

        adminToggle && adminToggle.addEventListener('change', event => {
            permissionsToggles.map(toggle => {
                toggle.checked = event.currentTarget.checked;
            })
        });

        adminToggle && (adminToggle.disabled = false);
    };

    const init = () => {
        initToggles();
    };

    return { init };
})();
