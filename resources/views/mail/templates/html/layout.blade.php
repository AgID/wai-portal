<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body, .footer {
                width: 100% !important;
            }
        }

        table {
            border-collapse: separate;
        }

        code {
            word-break: break-all;
        }

        .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {
            line-height: 100%;
        }

        .ExternalClass {
            width: 100%;
        }
    </style>
    <table class="wrapper" style="box-sizing: content-box" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    @include('mail.templates.html.header')
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="u-padding-r-all inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <div class="Prose">
                                            {{ Illuminate\Mail\Markdown::parse($slot) }}
                                        </div>
                                    </td>
                                </tr>
                                @include('mail.templates.html.greetings')
                            </table>
                        </td>
                    </tr>
                    @include('mail.templates.html.disclaimer')
                    @include('mail.templates.html.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
