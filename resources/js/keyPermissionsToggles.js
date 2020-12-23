export default (() => {
    const initTogglesKeys = () => {
        const adminSelect = document.getElementById('type');
        const permissionsToggles = [...document.querySelectorAll(`input[type="checkbox"][name^="permissions"]:not([disabled])`)];

        permissionsToggles.map(toggle => {
            toggle.addEventListener('change', event => {
                if (!event.currentTarget.checked) {
                    adminSelect && (adminSelect.value = "analytics");
                }
                if ('R' === event.currentTarget.value && !event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-W`).checked = false;
                }
                if ('W' === event.currentTarget.value && event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-R`).checked = true;
                }
            })
        });

        permissionsToggles && (permissionsToggles.disabled = false);
    };

    const init = () => {
        initTogglesKeys();
    };

    return { init };
})();
