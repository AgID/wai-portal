export default (() => {
    const changeArchiveStatus = (link) => {
        axios.patch(link)
            .then((response) => {
                //TODO: usare alert bootstrap
                //TODO: better refresh page/datatable after success:
                alert('Nuovo stato sito: ' + response.data.status)
                location.reload();
            })
            .catch((error) => {
                //TODO: usare alert bootstrap
                if (error.response && error.response.status === 304) {
                    alert('Stato archiviazione non modificato');
                } else {
                    alert('Richiesta cambio stato archiviazione fallita');
                }
            });
    }
    
    const initArchiveStatusButton = () => {
        let archiveButtons = [...document.querySelectorAll('a[role="button"][data-type="archiveStatus"]')];
    
        archiveButtons.map((archiveButton) => {
            let href = archiveButton.getAttribute('href');
    
            archiveButton.removeAttribute('href');
            archiveButton.onclick = () => changeArchiveStatus(href);
        });
    }
    
    return { initArchiveStatusButton };
})();
