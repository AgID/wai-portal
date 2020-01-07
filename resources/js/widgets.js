const iframeResizer = require('iframe-resizer').iframeResize;

export default (() => {
    const analyticsWidgets = [...document.querySelectorAll('iframe.auto-resizeable')];

    const onFrameLoaded = iframe => {
        iframeResizer({ heightCalculationMethod: 'lowestElement' }, iframe);
    }

    const init = () => {
        analyticsWidgets.map(widget => {
            widget.addEventListener('load', event => {
                onFrameLoaded(event.currentTarget)
            });
            widget.src = widget.dataset.src;
        });
    }

    return { init };
})();
