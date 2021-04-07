import { throttle } from "throttle-debounce";

export default (() => {
    const stickyFixedSwitch = (highlightBar, sticky) => {
        highlightBar.classList.toggle('fixed-top', !sticky);
        highlightBar.classList.toggle('sticky-top', sticky);
        sticky || window.scrollTo(0, window.pageYOffset - highlightBar.offsetHeight);
        sticky && window.scrollTo(0, window.pageYOffset + highlightBar.offsetHeight);
    }

    const init = () => {
        const highlightBar = document.getElementById('highlight-bar');

        highlightBar && window.addEventListener('scroll', throttle(20, () => {
            const parentElement = highlightBar.parentElement;
            const sticky = highlightBar.offsetHeight - parentElement.offsetHeight < parentElement.getBoundingClientRect().top;
            const isSticky = highlightBar.classList.contains('sticky-top');
            (isSticky !== sticky) && stickyFixedSwitch(highlightBar, sticky);
        }));
    };

    return { init };
})();
