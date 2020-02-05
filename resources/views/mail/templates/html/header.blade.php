<tr>
    <td class="mail-header">
        <div class="mail-slim-header">
            <div class="container">
                <a class="d-inline-block text-white py-2" href="{{ config('site.owner.link') }}">{{ config('site.owner.name') }}</a>
            </div>
        </div>
        <div class="mail-main-header primary-bg">
            <div class="container py-4">
                <a href="{{ url('/') }}">
                    <img class="align-middle" src="{{ asset(config('site.logo_raster')) }}" alt="{{ config('app.name') }}">
                    <span class="text-white align-middle">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>
        </div>
    </td>
</tr>
