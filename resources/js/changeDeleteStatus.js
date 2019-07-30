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
                    alert('Utente non modificato');
                } else {
                    alert('Richiesta eliminazione/ripristino utente fallita');
                }
            });
    }
    
    const initDeleteStatusButton = () => {
        let deleteButtons = [...document.querySelectorAll('a[role="button"][data-type="deleteStatus"]')];
        
        deleteButtons.map((deleteButton) => {
            let href = deleteButton.getAttribute('href');
            
            deleteButton.removeAttribute('href');
            deleteButton.onclick = () => changeDeleteStatus(href);
        });
    }
    
    return { initDeleteStatusButton };
})();
