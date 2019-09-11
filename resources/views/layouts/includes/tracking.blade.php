@if (config('site.trackers.matomo'))
<!-- Matomo -->
<script type="text/javascript">
    var _paq = _paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['requireConsent']);
    _paq.push(['enableLinkTracking']);
    _paq.push(['trackPageView']);
    (function() {
        var u="{!! config('analytics-service.public_url') !!}/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '{{ config('site.trackers.matomo') }}']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
    })();
</script>
<!-- End Matomo -->
@endif
@if (config('site.trackers.ga'))
<!-- Google Analytics -->
<script>
    if (document.cookie.indexOf('cookies_consent=true') === -1) {
        window['ga-disable-{{ config('site.trackers.ga') }}'] = true;
    }

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '{{ config('site.trackers.ga') }}', 'auto');
    ga('send', 'pageview');
</script>
<!-- End Google Analytics -->
@endif
