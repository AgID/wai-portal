import I18n from './i18n';

const iframeResizer = require('iframe-resizer').iframeResize;

export default (() => {
    const analyticsWidgets = [...document.querySelectorAll('iframe.auto-resizeable')];
    const apiPublicDomain = window.api_public_domain;
    const apiPublicPath = window.api_public_path;

    const areWidgetsAvailable = () => {
        return axios.get(`${apiPublicDomain}${apiPublicPath}/index.php?module=API&method=API.getMatomoVersion&timestamp=${new Date().getTime()}`)
            .then(() => true)
            .catch(() => false);
    }

    const areReportsArchived = () => {
        const dashboardId = window.dashboard_id;
        return axios.get(`${apiPublicDomain}${apiPublicPath}/index.php?module=API&method=VisitsSummary.getVisits&idSite=${dashboardId}&period=range&date=previous30&format=JSON&timestamp=${new Date().getTime()}`)
            .then((response) => 0 ==! response.data.value)
            .catch(() => false);
    }

    const onFrameLoaded = iframe => {
        document.getElementById('spinner-' + iframe.id).remove()
        iframe.classList.remove('invisible');
        iframeResizer({ heightCalculationMethod: 'grow' }, iframe);
    }

    const getLastPrevious30Range = () => {
        const dayBeforeYesterday = new Date();
        dayBeforeYesterday.setDate((new Date()).getDate() - 2);
        const thirtyDaysBeforeYesterday = new Date();
        thirtyDaysBeforeYesterday.setDate(dayBeforeYesterday.getDate() - 29);
        return `${thirtyDaysBeforeYesterday.toISOString().split('T')[0]},${dayBeforeYesterday.toISOString().split('T')[0]}`;
    }

    const init = async () => {
        await areWidgetsAvailable()
            ? analyticsWidgets.map(async widget => {
                widget.addEventListener('load', event => {
                    onFrameLoaded(event.currentTarget)
                });
                widget.src = await areReportsArchived()
                    ? widget.dataset.src
                    : widget.dataset.src.replace('previous30', getLastPrevious30Range);
            })
            : analyticsWidgets.map(widget => {
                document.getElementById('spinner-' + widget.id).remove()
                widget.replaceWith(I18n.t('Widget non disponibile, riprovare più tardi.'));
            });
    }

    return { init };
})();
