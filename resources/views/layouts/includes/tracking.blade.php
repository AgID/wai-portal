@if (env('TRACKING_MATOMO_ID'))
<!-- Matomo -->
<script type="text/javascript">
    var _paq = _paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['enableLinkTracking']);
    _paq.push(['trackPageView']);
    var loadMatomo = function() {
        (function() {
            var u="https://ingestion.webanalytics.italia.it/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '{{ env('TRACKING_MATOMO_ID') }}']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    }

    // Cookie consent not needed if no personal data is collected
    // if (document.cookie.indexOf('cookies_consent=true') !== -1) {
    //     loadMatomo();
    // }
    loadMatomo();

    // (window.trackers = window.trackers || []).push(loadMatomo);
</script>
<!-- End Matomo -->
@endif
@if (env('TRACKING_GA_ID'))
<!-- Google Analytics -->
<script>
    var loadGoogleAnalytics = function() {
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '{{ env('TRACKING_GA_ID') }}', 'auto');
        ga('send', 'pageview');
    }

    // Cookie consent not needed if no personal data is collected
    // if (document.cookie.indexOf('cookies_consent=true') !== -1) {
    //     loadGoogleAnalytics();
    // }
    loadGoogleAnalytics();

    // (window.trackers = window.trackers || []).push(loadGoogleAnalytics);
</script>
<!-- End Google Analytics -->
@endif
@if (env('TRACKING_HOTJAR_ID'))
<!-- Hotjar Tracking Code -->
<script>
    var loadHotjar = function() {
            (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:{{ env('TRACKING_HOTJAR_ID') }},hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    }

    // Cookie consent not needed if no personal data is collected
    // if (document.cookie.indexOf('cookies_consent=true') !== -1) {
    //     loadHotjar();
    // }
    loadHotjar();

    // (window.trackers = window.trackers || []).push(loadHotjar);
</script>
@endif
