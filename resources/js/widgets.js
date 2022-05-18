import I18n from './i18n';

const iframeResizer = require('iframe-resizer').iframeResize;

export default (() => {
    const analyticsWidgets = [...document.querySelectorAll('iframe.auto-resizeable')];
    const apiPublicDomain = window.api_public_domain;
    const widgetsBaseUrl = window.widgets_base_url;

    const areWidgetsAvailable = () => {
        return axios.get(`${apiPublicDomain}${widgetsBaseUrl}/index.php?module=API&method=API.getMatomoVersion&timestamp=${new Date().getTime()}`)
            .then(() => true)
            .catch(() => false);
    }

    const areReportsArchived = () => {
        const dashboardId = window.dashboard_id;
        return axios.get(`${apiPublicDomain}${widgetsBaseUrl}/index.php?module=API&method=VisitsSummary.getVisits&idSite=${dashboardId}&period=month&date=-1month&format=JSON&timestamp=${new Date().getTime()}`)
            .then((response) => 0 ==! response.data.value)
            .catch(() => false);
    }

    const onFrameLoaded = iframe => {
        document.getElementById('spinner-' + iframe.id).remove()
        iframe.classList.remove('invisible');
        iframeResizer({ heightCalculationMethod: 'grow' }, iframe);
    }

    const init = async () => {
        await areWidgetsAvailable()
            ? analyticsWidgets.map(async widget => {
                widget.addEventListener('load', event => {
                    onFrameLoaded(event.currentTarget)
                });
                widget.src = await areReportsArchived()
                    ? widget.dataset.src
                    : widget.dataset.src.replace('-1month', '-2month');
            })
            : analyticsWidgets.map(widget => {
                document.getElementById('spinner-' + widget.id).remove()
                widget.replaceWith(I18n.t('Widget non disponibile, riprovare più tardi.'));
            });
    }

    return { init };
})();
