export default (() => {
    const selectPublicAdministration = publicAdministrationFormSelector => {
        publicAdministrationFormSelector.submit();
    };

    const init = () => {
        const publicAdministrationSelector = document.querySelector('.pa-selector select[name="public-administration"]');
        const publicAdministrationFormSelector = document.querySelector('form.pa-selector ');

        publicAdministrationSelector && publicAdministrationSelector.addEventListener('change', () => {
            const publicAdministrationSelectorButton = document.querySelector('.pa-selector select[name="public-administration"] ~ .btn.dropdown-toggle');

            publicAdministrationSelectorButton.disabled = true;
            publicAdministrationSelectorButton.classList.add('loading');
            publicAdministrationSelectorButton.setAttribute('aria-disabled', true);

            selectPublicAdministration(publicAdministrationFormSelector);
        })
    };

    return { init };
})();
