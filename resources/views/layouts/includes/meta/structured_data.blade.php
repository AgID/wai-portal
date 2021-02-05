<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "url": "{{ env('APP_URL') }}",
      "logo": "{{ asset(config('site.owner.logo')) }}"
    }
</script>

@if(isset($metaStructuredData['breadcrumbs']))
<script type="application/ld+json">
    @json($metaStructuredData['breadcrumbs'], JSON_PRETTY_PRINT)
</script>
@endif

@if(isset($metaStructuredData['specificStructuredData']))
<script type="application/ld+json">
    @json($metaStructuredData['specificStructuredData'], JSON_PRETTY_PRINT)
</script>
@endif
