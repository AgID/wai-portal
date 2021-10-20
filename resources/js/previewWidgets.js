export default (() => {
    const init = () => {
        const previewBlock = document.getElementById('widget-preview');
        const snippetBlock = document.getElementById('widget-code');
        const box = document.getElementById('widgets-preview-box');
        const widgetList = [
            ...document.querySelectorAll('[data-type=widget-select]')
        ];

        widgetList.map(widget => {
            widget.addEventListener('click', e => {
                const widgetData = e.currentTarget.dataset;
                const widgetMetadata = widgetData.widgetMetadata;
                const widgetOptions = JSON.parse(widgetData.widgetOptions);
                const idSite = widgetData.idSite;
                const baseUrl = box.dataset.url;
                const iframeUrl = generateWidgetUrl(
                    widgetMetadata,
                    widgetOptions,
                    idSite,
                    baseUrl
                );
                const frame = document.createElement('iframe');

                frame.setAttribute('width', '100%');
                frame.setAttribute('height', '350');
                frame.setAttribute('src', decodeURIComponent(iframeUrl));
                frame.setAttribute('scrolling', 'yes');
                frame.setAttribute('frameborder', '0');
                frame.setAttribute('marginheight', '0');
                frame.setAttribute('marginwidth', '0');

                previewBlock.innerHTML = '';
                previewBlock.appendChild(frame);

                snippetBlock.textContent = frame.outerHTML.replace(
                    /&amp;/g,
                    '&'
                );

                box.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                    inline: 'nearest'
                });
            });
        });
    };

    const generateWidgetUrl = (widgetMetadata, widgetOptions, idSite, baseUrl) => {
        const widgetTitles = [...document.querySelectorAll('.widget-title')];
        let data = {};

        try {
            data = JSON.parse(widgetMetadata);
            widgetTitles.map(widgetTitle => {
                widgetTitle.textContent = data.name;
            });
        } catch (error) {
            console.log('Error in parsing JSON', error);
        }

        let url = new URLSearchParams(baseUrl + '/index.php?module=Widgetize');

        url.append('action', 'iframe');
        if (data.parameters?.containerId)
            url.append('containerId', data.parameters.containerId);
        url.append('disableLink', '0');
        url.append('widget', '1');
        url.append('moduleToWidgetize', data.module);
        url.append('actionToWidgetize', data.action);
        url.append('idSite', idSite);
        url.append('period', 'day');
        url.append('date', 'yesterday');
        url.append('disableLink', '1');

        Object.keys(widgetOptions).map((name) => {
            url.append(name, widgetOptions[name]);
        });

        return url;
    };

    return { init };
})();
