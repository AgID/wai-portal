@isset($notification)
<template
    class="notification-in-page"
    data-title="{{ $notification['title'] }}"
    data-message="{{ nl2br($notification['message']) }}"
    data-status="{{ $notification['status'] }}"
    data-icon="{{ $notification['icon'] }}"
    data-dismissable="{{ $notification['dismissable'] ?? 'true' }}"
    data-position="{{ $notification['position'] ?? 'top-fix' }}">
</template>
@endisset
