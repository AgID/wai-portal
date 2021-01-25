import i18n from './i18n';

export default (() => {
    let cancelRequest;
    const init = (searchInput, resultSchema, options) => {
        const autocompleteList = searchInput.parentElement.querySelector('ul.autocomplete-list');
        options = options || {};
        searchInput.addEventListener('keyup', event => {
            performSearch(event, searchInput, autocompleteList, options)
                .then(response => {
                    handleResult(response, searchInput, autocompleteList, resultSchema, options);
                    searchInput.classList.remove('searching');
                }).catch(() => {});
        });
    };

    const performSearch = (event, searchInput, autocompleteList, options) => {
        let query = searchInput.value;

        if (event.isComposing || event.keyCode === 229 || event.keyCode === 13) {
            event.preventDefault();
            return Promise.reject(new Error('composing'));
        }

        if (searchInput.dataset.lastQuery === query) {
            return Promise.reject(new Error('same query'));
        }

        options.onSearch && options.onSearch();

        if (query.length < 3) {
            searchInput.classList.remove('searching');
            autocompleteList.style.display = 'none';
            return Promise.reject(new Error('query too short'));
        }

        (typeof(cancelRequest) === 'function') && cancelRequest();
        searchInput.dataset.lastQuery = query;
        autocompleteList.style.display = 'none';
        searchInput.classList.add('searching');

        return axios.get(searchInput.dataset.source, {
            params: {
                ...{ q: query },
                ...options.queryParameters,
            },
            cancelToken: new axios.CancelToken(cancel => cancelRequest = cancel)
        }).then(response => response).catch(error => {
            if (axios.isCancel(error)) {
                return Promise.reject(new Error('request deduped'));
            } else {
                console.error(error); // eslint-disable-line no-console
            }
        });
    };

    const handleResult = (response, searchInput, autocompleteList, resultSchema, options) => {
        if (!response) {
            return;
        }

        while (autocompleteList.firstChild) {
            autocompleteList.firstChild.remove();
        }

        if (response.data.length === 0) {
            response.data = [
                {
                    [resultSchema.title]: i18n.t('nessun risultato'),
                    [resultSchema.subTitle]: '',
                }
            ];
            response.empty = true;
        }

        response.data.map(result => {
            const markText = new RegExp('(' + searchInput.value + ')', 'gi');
            const resultName = Array.isArray(resultSchema.title) && !response.empty
                ? resultSchema.title.reduce((title, titleElement) => {
                    return [title, result[titleElement]].join(' ');
                }, '')
                : result[resultSchema.title];
            const resultFragment = document.createRange().createContextualFragment([
                '<li>',
                response.empty ? '<a>' : '<a href="#">',
                '<span class="autocomplete-list-text">',
                `<span>${resultName.replace(markText, '<mark>$1</mark>')}</span>`,
                resultSchema.subTitle && `<em>${result[resultSchema.subTitle]}</em>`,
                '</span>',
                '</a>',
                '</li>',
            ].join(''));
            const resultElement = resultFragment.firstChild;

            if (!response.empty) {
                resultElement.dataset.item = JSON.stringify(result);
                resultElement.addEventListener('click', event => {
                    const selectedResult = JSON.parse(event.currentTarget.dataset.item);

                    event.preventDefault();
                    event.stopPropagation();

                    autocompleteList.style.display = 'none';
                    searchInput.value = resultName;

                    options.handleSelectedResult && options.handleSelectedResult(selectedResult);
                });
            }

            return resultElement;
        }).map(resultListItem => autocompleteList.appendChild(resultListItem));

        autocompleteList.style.display = 'block';
    };

    return { init };
})();
