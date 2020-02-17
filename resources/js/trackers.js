export default (() => {
    const initTrackers = () => {
        if (window.trackers && Array.isArray(window.trackers)) {
            window.trackers.forEach(tracker => {
                ('function' === typeof tracker) && tracker();
            });
        }
    }

    const init = () => {
        $(document).on(
            'click.bs.cookiebar.data-api',
            '[data-accept="cookiebar"]',
            initTrackers
        )
    };

    return { init };
})();
