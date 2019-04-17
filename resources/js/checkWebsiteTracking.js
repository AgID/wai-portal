export default (() => {
    const checkTracking = (link) => {
        axios.get(link)
            .then((response) => {
                //TODO: usare alert bootstrap
                //TODO: refresh page/datatable after success
                alert('Stato sito: ' + response.data.status)
            })
            .catch(() => {
                //TODO: usare alert bootstrap
                alert('Richiesta stato website fallita')
            });
    }

    const initWebsiteCheckButton = (json) => {
        json.data && json.data.map((website) => {
            website.buttons && website.buttons.map((button) => {
                if (button.type === 'check_tracking') {
                    let checkButton = document.querySelector('a[href="' + button.link + '"]');
                    let href = checkButton.getAttribute('href');

                    checkButton.removeAttribute('href');
                    checkButton.onclick = () => checkTracking(href);
                }
            });
        });
    }

    return { initWebsiteCheckButton };
})();
