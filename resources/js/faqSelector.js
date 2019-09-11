export default (() => {
    const selectFaqTheme = faqTheme => {
        const faqThemeQuerySelector = faqTheme === 'all' ? `data-theme` : `data-theme=${faqTheme}`;
        const faqsToHide = [...document.querySelectorAll(`.faq:not([${faqThemeQuerySelector}])`)];
        const faqsToShow = [...document.querySelectorAll(`.faq[${faqThemeQuerySelector}]`)];

        faqsToHide.map(faqToHide => faqToHide.style.display = 'none');
        faqsToShow.map(faqToShow => faqToShow.style.display = 'block');
    };

    const init = () => {
        const faqSelectors = [...document.querySelectorAll('.faq-selector')];

        faqSelectors.map(faqSelector => {
            const faqTheme = faqSelector.dataset.theme;
            faqSelector.addEventListener('click', event => {
                event.preventDefault();
                faqSelectors.map(faqSelector => faqSelector.classList.remove('selected'));
                selectFaqTheme(faqTheme);
                faqSelector.classList.add('selected');
            })
        });

        const isFaqSelectedInUrl = window.location.hash && window.location.hash.startsWith('#faq-');
        isFaqSelectedInUrl && $(`${window.location.hash}-body`).collapse('show');
    };

    return { init };
})();
