import i18n from './i18n';

export default (() => {
    const getJavascriptSnippet = javascriptSnippetField => {
        return axios.get(javascriptSnippetField.dataset.href)
            .then(response => {
                javascriptSnippetField.querySelector('code').textContent = response.data.javascriptSnippet;
            })
            .catch(() => {
                javascriptSnippetField.querySelector('code').textContent = i18n.t("Si è verificato un errore nel recupero del codice di tracciamento.");
            });
    };

    const init = () => {
        const javascriptSnippetContainer = document.querySelector('.javascript-snippet-container');
        const javascriptSnippetField = document.getElementById('javascript-snippet');

        if (javascriptSnippetField) {
            javascriptSnippetContainer.classList.add('loading');
            getJavascriptSnippet(javascriptSnippetField).finally(() => {
                javascriptSnippetContainer.classList.remove('loading', 'blank');
            });
        }
    };

    return { init };
})();
