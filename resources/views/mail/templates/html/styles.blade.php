<!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
<!--[if mso]>
    <style>
        * {
            font-family: sans-serif !important;
        }
    </style>
<![endif]-->
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css?family=Lora|Titillium+Web&display=swap" rel="stylesheet">
<!--<![endif]-->
<style>
    html,
    body {
        margin: 0 auto !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        font-family: 'Titillium Web', 'Roboto', sans-serif !important;
    }
    /* Stops email clients resizing small text. */
    * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }
    /* Centers email on Android 4.4 */
    div[style*="margin: 16px 0"] {
        margin: 0 !important;
    }

    /* Forces Samsung Android mail clients to use the entire viewport */
    #MessageViewBody, #MessageWebViewDiv{
        width: 100% !important;
    }

    /* Stops Outlook from adding extra spacing to tables. */
    table,
    td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
    }

    /* Fixes webkit padding issue. */
    table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
    }

    /* Uses a better rendering method when resizing images in IE. */
    img {
        -ms-interpolation-mode:bicubic;
    }

    /* Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
    a {
        text-decoration: none;
    }

    /* A work-around for email clients meddling in triggered links. */
    a[x-apple-data-detectors],  /* iOS */
    .unstyle-auto-detected-links a,
    .aBn {
        border-bottom: 0 !important;
        cursor: default !important;
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* Prevents Gmail from displaying a download button on large, non-linked images. */
    .a6S {
        display: none !important;
        opacity: 0.01 !important;
    }

    /* Prevents Gmail from changing the text color in conversation threads. */
    .im {
        color: inherit !important;
    }

    /* If the above doesn't work, add a .g-img class to any image in question. */
    img.g-img + div {
        display: none !important;
    }

    /* Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */

    /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
        u ~ div .email-container {
            min-width: 320px !important;
        }
    }
    /* iPhone 6, 6S, 7, 8, and X */
    @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
        u ~ div .email-container {
            min-width: 375px !important;
        }
    }
    /* iPhone 6+, 7+, and 8+ */
    @media only screen and (min-device-width: 414px) {
        u ~ div .email-container {
            min-width: 414px !important;
        }
    }
    /* Adjust typography on small screens to improve readability */
    @media screen and (max-width: 600px) {
        .email-container p {
            font-size: 17px !important;
        }
    }
    /* Content styles */
    .mail-highlight-bar {
        background-color: #75ebe7;
        padding-top: 16px !important;
        padding-bottom: 16px !important;
    }
    .mail-highlight-bar-content {
        margin-left: 24px !important;
        margin-right: 24px !important;
        text-align: center;
        color: #17324d;
    }
    .mail-highlight-bar-content p {
        font-family: 'Titillium Web', 'Roboto', sans-serif !important;
        margin: 0;
    }
    .mail-main-header, .mail-slim-header {
        padding-right: 1em;
        padding-left: 1em;
    }
    .mail-slim-header {
        background-color: #0059b3;
    }
    .mail-main-header a img {
        max-height: 2em;
        max-width: 2em;
    }
    .mail-main-header a span {
        font-size: 1.5rem;
        margin-left: 12px;
        font-weight: 400;
    }
    h1 {
        font-weight: 400 !important;
        margin-bottom: 48px !important;
    }
    p {
        font-family: Lora, Georgia, serif !important;
    }
    .mail-page-container {
        margin: 0 auto;
        padding: 1em 2em;
        background-color: #ffffff;
    }
    .mail-footer {
        display: block;
    }
    .mail-footer a {
        text-decoration: underline;
    }
    .mail-footer p {
        font-size: 14px;
    }
    @media (min-width: 576px) {
        .mail-main-header, .mail-slim-header {
            padding-right: 2em !important;
            padding-left: 2em !important;
        }
        .mail-main-header a img {
            max-height: 3em !important;
            max-width: 3em !important;
        }
        .mail-main-header a span {
            font-size: 2rem !important;
        }
    }
    @media (min-width: 768px) {
        .mail-main-header, .mail-slim-header {
            padding-right: 0;
            padding-left: 0;
        }
        .container {
            max-width: 720px;
        }
        .mail-page-container {
            border-radius: 1em;
            box-shadow: 0px 10px 25px 0px rgba(0, 0, 0, 0.25);
            padding: 2em 3em !important;
            margin: 24px auto !important;
        }
        .mail-footer {
            margin-top: 0 !important;
        }
    }
</style>
<!-- Makes background images in 72ppi Outlook render at correct size. -->
<!--[if gte mso 9]>
<xml>
    <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
</xml>
<![endif]-->
