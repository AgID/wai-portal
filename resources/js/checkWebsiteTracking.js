const axios = require('axios');

const check_tracking = (link) => {
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

const initWebsiteCheckButton = (element) => {
  let href = element.getAttribute('href');
  element.removeAttribute('href');
  element.onclick = () => check_tracking(href);
}

export default { initWebsiteCheckButton };
