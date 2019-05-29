<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('mail.templates.html.styles')
</head>
<body>
    <table class="wrapper" style="box-sizing: content-box" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    @include('mail.templates.html.header')
                    <tr class="lightgrey-bg-a2">
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <div class="container p-0">
                                            <div class="mail-page-container">
                                                {{ Illuminate\Mail\Markdown::parse($slot) }}
                                                @include('mail.templates.html.separator')
                                                @include('mail.templates.html.greetings')
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {{-- @include('mail.templates.html.disclaimer') --}}
                    @include('mail.templates.html.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
