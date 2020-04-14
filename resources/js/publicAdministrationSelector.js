export default (() => {

    const selectPublicAdministration = (publicAdministrationSelector) => {
        window.location = publicAdministrationSelector.value;
    };

    const init = () => {
        const publicAdministrationSelector = document.querySelector('.selector-pa select[name="public-administration-nav"]');

        publicAdministrationSelector && publicAdministrationSelector.addEventListener('change', () => {
            const publicAdministrationSelectorButton = document.querySelector('.selector-pa select[name="public-administration-nav"] ~ .btn.dropdown-toggle');

            publicAdministrationSelectorButton.disabled = true;
            publicAdministrationSelectorButton.classList.add('loading');
            publicAdministrationSelectorButton.setAttribute('aria-disabled', true);

            selectPublicAdministration(publicAdministrationSelector);
        })
    };

    return { init };
})();
