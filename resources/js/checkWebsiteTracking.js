const axios = require('axios');

export function check_tracking (link) {
  axios.get(link)
    .then(function (response) {
      //TODO: usare alert bootstrap
      alert('Stato sito: ' + response.data.status)
    })
    .catch(function () {
      //TODO: usare alert bootstrap
      alert('Richiesta stato website fallita')
    });
}

export function initWebsiteCheckButton (element) {
  let href = element.getAttribute('href');
  element.removeAttribute('href');
  element.onclick = function() { check_tracking(href); };
}
