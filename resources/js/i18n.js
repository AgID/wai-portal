export default (() => {
    const t = message => {
        return window.t[message] || message;
    };

    return { t };
})();
