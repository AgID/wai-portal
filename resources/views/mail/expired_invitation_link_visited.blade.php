@component('mail::message')
    # {{ __('mail.user.expired_invitation_link_visited.user.title') }}

    @include('mail.partials.' . $locale . '.user.expired_invitation_link_visited_message', ['fullName' => $fullName, 'invitedFullName' => $invitedFullName, 'profileUrl' => $profileUrl])

@endcomponent
