<tr>
    <td class="mail-footer primary-bg-a11 py-5 px-4">
        <div class="container text-center">
            <ul class="list-inline d-inline-block text-left m-0">
                <li class="list-inline-item ml-1">
                    <a class="text-white" href="">{{ __("Cos'è") }} {{ config('app.name') }}?</a>
                </li>
                <li class="list-inline-item ml-1">
                    <a class="text-white" href="{{ url(route('faq')) }}">{{ __('FAQ') }}</a>
                </li>
                <li class="list-inline-item ml-1">
                    <a class="text-white" href="{{ __(config('site.kb.link')) }}">{{ __(config('site.kb.name')) }}</a>
                </li>
            </ul>
        </div>
        <div class="container text-center text-white mt-3">
            <p class="m-0 text-sans-serif">{{ __('Ti abbiamo inviato questa mail perché sei iscritto a') }} <a class="text-white" href="{{ url('/') }}">{{ config('app.name') }}</a>.</p>
            <p class="m-0 text-sans-serif">{{ __('Non rispondere a questo messaggio perché è stato inviato automaticamente da un indirizzo non programmato per la ricezione.') }}</p>
        </div>
    </td>
</tr>
