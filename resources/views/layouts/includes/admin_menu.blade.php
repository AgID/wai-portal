<li data-megamenu-class="Megamenu-item Megamenu-item--admin u-flexExpandLeft"><a href="#">Area amministrativa</a>
    <ul>
        <li>
            <a href="{{ route('admin-dashboard', [], false) }}">
                Dashboard amministrativa
                {{--TODO: put string in lang file--}}
            </a>
        </li>
        <li>
            <a href="{{ route('admin-add-user', [], false) }}">
                Aggiungi utente amministratore
                {{--TODO: put string in lang file--}}
            </a>
        </li>
    </ul>
</li>
