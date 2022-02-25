export default (() => {
    const credentialTypes = window.credentialTypes;
    const credentialPermissions = window.credentialPermissions;

    const initTogglesCredentials = () => {
        const adminSelect = document.getElementById('type');
        const permissionsToggles = [
            ...document.querySelectorAll(
                `input[type="checkbox"][name^="permissions"]:not([disabled])`
            )
        ];

        permissionsToggles.map(toggle => {
            toggle.addEventListener('change', event => {
                if (credentialPermissions.read === event.currentTarget.value && !event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-${credentialPermissions.write}`).checked = false;
                }
                if (credentialPermissions.write === event.currentTarget.value && event.currentTarget.checked) {
                    document.getElementById(`permissions[${event.currentTarget.dataset.entity}][]-${credentialPermissions.read}`).checked = true;
                }
            });
        });

        adminSelect && adminSelect.addEventListener('change', event => {
            permissionsToggles.map(toggle => {
                const isAdminCredentials = (event.target.value === credentialTypes.admin);
                toggle.disabled = isAdminCredentials;
                isAdminCredentials
                    ? toggle.setAttribute('checked', true)
                    : toggle.removeAttribute('checked');
            });
        });
    };

    const init = () => {
        initTogglesCredentials();
    };

    return { init };
})();
