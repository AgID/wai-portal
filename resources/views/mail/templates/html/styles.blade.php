<link href="https://fonts.googleapis.com/css?family=Lora|Titillium+Web&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        font-family: 'Titillium Web', Geneva, Tahoma, sans-serif !important;
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
        font-weight: 600;
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
    @media (min-width: 992px) {
        .mail-main-header a img {
            max-height: 4em;
            max-width: 4em;
        }
        .mail-main-header a span {
            font-size: 2.5rem !important;
            margin-left: 36px !important;
            font-weight: 700;
        }
        .container {
            max-width: 960px;
        }
        .mail-page-container {
            padding: 3em 4em !important;
            margin: 48px auto !important;
        }
    }
    @media (min-width: 1200px) {
        .mail-page-container {
            max-width: 960px;
        }
    }
</style>
