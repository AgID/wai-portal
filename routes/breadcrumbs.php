<?php

// Analytics Italia
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push(__('ui.site_title'), route('home', [], false));
});

// Analytics Italia > FAQs
Breadcrumbs::register('faq', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.faq.title'), route('faq', [], false));
});

// Analytics Italia > Dashboard
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.dashboard.title'), route('dashboard', [], false));
});

// Analytics Italia > Dashboard > Websites
Breadcrumbs::register('websites-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.websites.title'), route('websites-index', [], false));
});

// Analytics Italia > Dashboard > Add primary website
Breadcrumbs::register('add-primary-website', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.add-primary-website.title'), route('add-primary-website', [], false));
});

// Analytics Italia > Dashboard > Add website
Breadcrumbs::register('websites-create', function ($breadcrumbs) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.add-website.title'), route('websites-create', [], false));
});

// Analytics Italia > Dashboard > Websites > Javascript snippet
Breadcrumbs::register('website-javascript-snippet', function ($breadcrumbs, $website) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.website-javascript-snippet.title'), route('website-javascript-snippet', $website, false));
});

// Analytics Italia > Dashboard > Users
Breadcrumbs::register('users-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.users.title'), route('users-index', [], false));
});

// Analytics Italia > Dashboard > Add user
Breadcrumbs::register('users-create', function ($breadcrumbs) {
    $breadcrumbs->parent('users-index');
    $breadcrumbs->push(__('ui.pages.add-user.title'), route('users-create', [], false));
});

// Analytics Italia > Privacy
Breadcrumbs::register('privacy', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.privacy.title'), route('privacy', [], false));
});

// Analytics Italia > Register
Breadcrumbs::register('register', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.register.title'), route('register', [], false));
});

// Analytics Italia > SPID Login
Breadcrumbs::register('spid-auth_login', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.spid-auth_login.title'), route('spid-auth_login', [], false));
});

// Analytics Italia > SPID Login
Breadcrumbs::register('auth-verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Analytics Italia > SPID Login
Breadcrumbs::register('auth-do_verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Analytics Italia > SPID Login
Breadcrumbs::register('auth-register', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-register.title'), route('auth-register', [], false));
});

// Analytics Italia > 404
Breadcrumbs::register('errors.404', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.404.title'));
});
