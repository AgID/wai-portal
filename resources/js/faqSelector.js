export default (() => {
    const faqSelectors = [...document.querySelectorAll('.faq-selector')];
    const faqSearchField = document.getElementById('faq-search');

    const renderFaqs = () => {
        const selectedFaqs = selectFaqTheme();
        const renderedFaqs = searchFaqs(selectedFaqs);
        const noFaqsFound = document.getElementById('no-faqs-found');

        renderedFaqs.hide.map(faqToHide => faqToHide.style.display = 'none');
        renderedFaqs.show.map(faqToShow => faqToShow.style.display = 'block');

        if (renderedFaqs.show.length > 0) {
            noFaqsFound.classList.add('d-none');
        } else {
            noFaqsFound.classList.remove('d-none');
        }
    }

    const searchFaqs = faqsToSearch => {
        const searchTerm = faqSearchField.value.toLowerCase().replace(/\s+/g, ' ').trim();

        if (0 === searchTerm.length) {
            return faqsToSearch;
        }

        const faqsToHide = faqsToSearch.hide;
        const faqsToShow = faqsToSearch.show.filter(faq => {
            if (-1 !== faq.textContent.toLowerCase().replace(/\s+/g, ' ').trim().indexOf(searchTerm)) {
                return faq;
            } else {
                faqsToHide.push(faq);
            }
        });

        return {
            hide: faqsToHide,
            show: faqsToShow,
        };
    }

    const selectFaqTheme = () => {
        const faqTheme = document.querySelector('.faq-selector.selected').dataset.theme;
        const faqThemeQuerySelector = faqTheme === 'all' ? `data-themes` : `data-themes~=${faqTheme}`;
        const faqsToHide = [...document.querySelectorAll(`.faq:not([${faqThemeQuerySelector}])`)];
        const faqsToShow = [...document.querySelectorAll(`.faq[${faqThemeQuerySelector}]`)];

        return {
            hide: faqsToHide,
            show: faqsToShow,
        };
    };

    const setSearchFieldCancelIcon = (searchFieldIcon, present) => {
        const searchFieldIconSvgUse = searchFieldIcon.querySelector('.icon > use');
        const iconPath = searchFieldIconSvgUse.getAttribute('xlink:href');

        searchFieldIconSvgUse.setAttribute('xlink:href', iconPath.replace(/#it-\S+/, present ? '#it-close' : '#it-search'));
    }

    const init = () => {
        faqSelectors.map(faqSelector => {
            faqSelector.addEventListener('click', event => {
                event.preventDefault();
                faqSelectors.map(faqSelector => faqSelector.classList.remove('selected'));
                faqSelector.classList.add('selected');
                renderFaqs();
            })
        });

        faqSearchField && faqSearchField.addEventListener('keyup', event => {
            if (event.isComposing || event.keyCode === 229 || event.keyCode === 13) {
                return;
            }

            const searchFieldIcon = faqSearchField.parentNode.querySelector('.input-group-text');

            if (faqSearchField.value.trim().length > 0) {
                setSearchFieldCancelIcon(searchFieldIcon, true);

                searchFieldIcon.addEventListener('click', () => {
                    faqSearchField.value = '';
                    setSearchFieldCancelIcon(searchFieldIcon, false);
                    renderFaqs();
                })
            } else {
                setSearchFieldCancelIcon(searchFieldIcon, false);

                // Remove all previously attached event listeners
                const searchFieldIconClone = searchFieldIcon.cloneNode(true);
                searchFieldIcon.parentNode.replaceChild(searchFieldIconClone, searchFieldIcon);
            }

            renderFaqs();
        });

        const isFaqSelectedInUrl = window.location.hash && window.location.hash.startsWith('#faq-');
        isFaqSelectedInUrl && $(`${window.location.hash}-body`).collapse('show');
    };

    return { init };
})();
