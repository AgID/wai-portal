<div class="pb-2">
    <p>
        {{ __("Lo stato della tua utenza è:") }}
        <strong class="p-1 text-white bg-secondary">{{ auth()->user()->status->description }}</strong>
    </p>
    @if (auth()->user()->isAn(UserRole::ADMIN))
    <p>
        {{ __("Il ruolo della tua utenza è:") }}
        {{ auth()->user()->all_role_names }}
    </p>
    @endif
</div>
