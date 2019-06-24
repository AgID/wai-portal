export default (() => {
    const changeSuspendStatus = (link) => {
        axios.patch(link)
            .then((response) => {
                //TODO: usare alert bootstrap
                //TODO: better refresh page/datatable after success:
                alert('Nuovo stato utente: ' + response.data.status)
                location.reload();
            })
            .catch((error) => {
                //TODO: usare alert bootstrap
                if (error.response && error.response.status === 304) {
                    alert('Stato utente non modificato');
                } else {
                    alert('Richiesta cambio stato utente fallita');
                }
            });
    }
    
    const initSuspendStatusButton = () => {
        let suspendButtons = [...document.querySelectorAll('a[role="button"][data-type="suspendStatus"]')];
        
        suspendButtons.map((suspendButton) => {
            let href = suspendButton.getAttribute('href');
            
            suspendButton.removeAttribute('href');
            suspendButton.onclick = () => changeSuspendStatus(href);
        });
    }
    
    return { initSuspendStatusButton };
})();
