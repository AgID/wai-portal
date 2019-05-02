export default (() => {
    const checkTracking = (link) => {
        axios.get(link)
            .then((response) => {
                //TODO: usare alert bootstrap
                //TODO: refresh page/datatable after success
                alert('Stato sito: ' + response.data.status);
            })
            .catch((error) => {
                //TODO: usare alert bootstrap
                if (error.response && error.response.status === 304) {
                    alert('Stato non cambiato');
                } else {
                    alert('Richiesta stato website fallita');
                }
            });
    }

    const initWebsiteCheckButton = () => {
        let checkButtons = [...document.querySelectorAll('a[role="button"][data-type="checkTracking"]')];

        checkButtons.map((checkButton) => {
            let href = checkButton.getAttribute('href');

            checkButton.removeAttribute('href');
            checkButton.onclick = () => checkTracking(href);
        });
    }

    return { initWebsiteCheckButton };
})();
