import I18n from './i18n';

const iframeResizer = require('iframe-resizer').iframeResize;

export default (() => {
    const analyticsWidgets = [...document.querySelectorAll('iframe.auto-resizeable')];

    const areWidgetsAvailable = () => {
        const apiPublicDomain = window.api_public_domain;
        const apiPublicPath = window.api_public_path;
        return axios.get(`${apiPublicDomain}${apiPublicPath}/index.php?module=API&method=API.getMatomoVersion&timestamp=${new Date().getTime()}`)
            .then(() => true)
            .catch(() => false);
    }

    const onFrameLoaded = iframe => {
        document.getElementById('spinner-' + iframe.id).remove()
        iframe.classList.remove('invisible');
        iframeResizer({ heightCalculationMethod: 'grow' }, iframe);
    }

    const init = async () => {
        await areWidgetsAvailable()
            ? analyticsWidgets.map(widget => {
                widget.addEventListener('load', event => {
                    onFrameLoaded(event.currentTarget)
                });
                widget.src = widget.dataset.src;
            })
            : analyticsWidgets.map(widget => {
                document.getElementById('spinner-' + widget.id).remove()
                widget.replaceWith(I18n.t('Widget non disponibile, riprovare più tardi.'));
            });
    }

    return { init };
})();
