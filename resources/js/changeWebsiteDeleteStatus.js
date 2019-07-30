export default (() => {
    const changeDeleteStatus = (link) => {
        axios.patch(link)
            .then(() => {
                //TODO: usare alert bootstrap
                //TODO: better refresh page/datatable after success:
                location.reload();
            })
            .catch((error) => {
                //TODO: usare alert bootstrap
                if (error.response && error.response.status === 304) {
                    alert('Sito web non modificato');
                } else {
                    alert('Richiesta eliminazione/ripristino sito web fallita');
                }
            });
    }
    
    const initDeleteStatusButton = () => {
        let deleteButtons = [...document.querySelectorAll('a[role="button"][data-type="deleteWebsiteStatus"]')];
        
        deleteButtons.map((deleteButton) => {
            let href = deleteButton.getAttribute('href');
            
            deleteButton.removeAttribute('href');
            deleteButton.onclick = () => changeDeleteStatus(href);
        });
    }
    
    return { initDeleteStatusButton };
})();
