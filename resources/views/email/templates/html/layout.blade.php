<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <script type="text/javascript">
        WebFontConfig = {
            google: {
                families: ['Titillium+Web:300,400,600,700,400italic:latin']
            }
        };
        (function () {
            var wf = document.createElement('script');
            wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>
</head>
<body class="t-Pac">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body, .footer {
                width: 100% !important;
            }
        }

        @font-face {
            font-family: "ita";
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('toolkit/assets/5d3ff71c.eot') }}");
            src: url("{{ asset('toolkit/assets/5d3ff71c.eot') }}") format("embedded-opentype"),
            url("{{ asset('toolkit/assets/602e9d03.ttf') }}") format("truetype"),
            url("{{ asset('toolkit/assets/80f3eca6.woff') }}") format("woff"),
            url("{{ asset('toolkit/assets/cf91165a.svg') }}") format("svg");
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
        @include('email.templates.html.font-icon')
    </style>
    <table class="wrapper" style="box-sizing: content-box" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    @include('email.templates.html.header')
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
                                @include('email.templates.html.greetings')
                            </table>
                        </td>
                    </tr>
                    @include('email.templates.html.disclaimer')
                    @include('email.templates.html.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
