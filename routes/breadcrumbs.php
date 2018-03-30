<?php

// Web Analytics Italia
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push(__('ui.site_title'), route('home', [], false));
});

// Web Analytics Italia > FAQs
Breadcrumbs::register('faq', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.faq.title'), route('faq', [], false));
});

// Web Analytics Italia > Accesso amministratori
Breadcrumbs::register('admin-login', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-login.title'), route('admin-login', [], false));
});

// Web Analytics Italia > Password dimenticata
Breadcrumbs::register('admin-password_forgot', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_forgot.title'), route('admin-password_forgot', [], false));
});

// Web Analytics Italia > Cambio della password
Breadcrumbs::register('admin-password_change', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_change.title'), route('admin-password_change', [], false));
});

// Web Analytics Italia > Reset della password
Breadcrumbs::register('admin-password_reset', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_reset.title'), route('admin-password_reset', [], false));
});

// Web Analytics Italia > Dashboard amministrativa
Breadcrumbs::register('admin-dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-dashboard.title'), route('admin-dashboard', [], false));
});

// Web Analytics Italia > Dashboard
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.dashboard.title'), route('dashboard', [], false));
});

// Web Analytics Italia > Dashboard > Websites
Breadcrumbs::register('websites-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.websites.title'), route('websites-index', [], false));
});

// Web Analytics Italia > Dashboard > Add primary website
Breadcrumbs::register('add-primary-website', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.add-primary-website.title'), route('add-primary-website', [], false));
});

// Web Analytics Italia > Dashboard > Add website
Breadcrumbs::register('websites-create', function ($breadcrumbs) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.add-website.title'), route('websites-create', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Javascript snippet
Breadcrumbs::register('website-javascript-snippet', function ($breadcrumbs, $website) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.website-javascript-snippet.title'), route('website-javascript-snippet', $website, false));
});

// Web Analytics Italia > Dashboard > Users
Breadcrumbs::register('users-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.users.title'), route('users-index', [], false));
});

// Web Analytics Italia > Dashboard > Add user
Breadcrumbs::register('users-create', function ($breadcrumbs) {
    $breadcrumbs->parent('users-index');
    $breadcrumbs->push(__('ui.pages.add-user.title'), route('users-create', [], false));
});

// Web Analytics Italia > Privacy
Breadcrumbs::register('privacy', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.privacy.title'), route('privacy', [], false));
});

// Web Analytics Italia > Register
Breadcrumbs::register('register', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.register.title'), route('register', [], false));
});

// Web Analytics Italia > User profile
Breadcrumbs::register('user-profile', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.profile.title'), route('user-profile', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::register('spid-auth_login', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.spid-auth_login.title'), route('spid-auth_login', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::register('auth-verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::register('auth-do_verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::register('auth-register', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-register.title'), route('auth-register', [], false));
});

// Web Analytics Italia > Error 404
Breadcrumbs::register('errors.404', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.404.title'));
});
