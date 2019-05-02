export default (() => {
    const initPermissionInputs = () => {
        initWebsitesCheckboxes();
        initUsersCheckboxes();
        initAdminUserCheckbox();
    }

    const initWebsitesCheckboxes = () => {
        let websitesEnabledCheckboxes = [...document.querySelectorAll('input[type="checkbox"][name^="websitesEnabled"]:not([disabled])')];

        websitesEnabledCheckboxes.map((checkbox) => {
            setDisabledRadiosForWebsiteId(checkbox.dataset.website, !checkbox.checked);

            checkbox.onchange = () => {
                setDisabledRadiosForWebsiteId(checkbox.dataset.website, !checkbox.checked);
            }
        });
    }

    const initAdminUserCheckbox = () => {
        let adminUserCheckbox = document.getElementById('isAdmin');

        adminUserCheckbox && (adminUserCheckbox.onchange = () => {
            setDisabledWebsitesInputs(adminUserCheckbox.checked);
        });
    }

    const initUsersCheckboxes = () => {
        let usersEnabledCheckboxes = [...document.querySelectorAll('input[type="checkbox"][name^="usersEnabled"]:not([disabled])')];

        usersEnabledCheckboxes.map((checkbox) => {
            setDisabledRadiosForUserId(checkbox.dataset.user, !checkbox.checked);

            checkbox.onchange = () => {
                setDisabledRadiosForUserId(checkbox.dataset.user, !checkbox.checked);
            }
        });
    }

    const setDisabledWebsitesInputs = (disabled) => {
        [...document.querySelectorAll(`input[type="checkbox"][name^="websitesEnabled"]`)].map((checkbox) => {
            checkbox.disabled = disabled;
            !disabled && initWebsitesCheckboxes();
            disabled && setDisabledRadiosForWebsiteId(checkbox.dataset.website, disabled);
        });
    }

    const setDisabledRadiosForWebsiteId = (websiteId, disabled) => {
        [...document.querySelectorAll(`input[type="radio"][name="websitesPermissions[${websiteId}]"]`)].map((radio) => {
            radio.disabled = disabled;
        });
    }

    const setDisabledRadiosForUserId = (userId, disabled) => {
        [...document.querySelectorAll(`input[type="radio"][name="usersPermissions[${userId}]"]`)].map((radio) => {
            radio.disabled = disabled;
        });
    }

    return { initPermissionInputs };
})();
