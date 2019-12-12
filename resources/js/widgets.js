const iframeResizer = require('iframe-resizer').iframeResize;

export default (() => {
    const analyticsWidgets = [...document.querySelectorAll('iframe.auto-resizeable')];

    const onFrameLoaded = () => {
        iframeResizer({ heightCalculationMethod: 'lowestElement' });
    }

    const init = () => {
        analyticsWidgets.map(widget => {
            widget.addEventListener("load", onFrameLoaded);
        });
    }

    return { init };
})();
