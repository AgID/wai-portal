export default (() => {
    // const initTrackers = () => {
    //     if (window.trackers && Array.isArray(window.trackers)) {
    //         window.trackers.forEach(tracker => {
    //             ('function' === typeof tracker) && tracker();
    //         });
    //     }
    // }

    const init = () => {
        // Cookie consent not needed if no personal data is collected
        // $(document).on(
        //     'click.bs.cookiebar.data-api',
        //     '[data-accept="cookiebar"]',
        //     initTrackers
        // )
    };

    return { init };
})();
