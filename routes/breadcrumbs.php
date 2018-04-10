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

// Web Analytics Italia > Dashboard
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.dashboard.title'), route('dashboard', [], false));
});

// Web Analytics Italia > Dashboard > Websites
Breadcrumbs::register('websites-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.websites.index.title'), route('websites-index', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Add primary website
Breadcrumbs::register('websites-add-primary', function ($breadcrumbs) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.websites.add-primary.title'), route('websites-add-primary', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Add website
Breadcrumbs::register('websites-add', function ($breadcrumbs) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.websites.add.title'), route('websites-add', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Edit website
Breadcrumbs::register('websites-edit', function ($breadcrumbs, $website) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.websites.edit.title'), route('websites-edit', ['website' => $website], false));
});

// Web Analytics Italia > Dashboard > Websites > Javascript snippet
Breadcrumbs::register('website-javascript-snippet', function ($breadcrumbs, $website) {
    $breadcrumbs->parent('websites-index');
    $breadcrumbs->push(__('ui.pages.websites.javascript-snippet.title'), route('website-javascript-snippet', ['website' => $website], false));
});

// Web Analytics Italia > Dashboard > Users
Breadcrumbs::register('users-index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('ui.pages.users.title'), route('users-index', [], false));
});

// Web Analytics Italia > Dashboard > Users > Add user
Breadcrumbs::register('users-create', function ($breadcrumbs) {
    $breadcrumbs->parent('users-index');
    $breadcrumbs->push(__('ui.pages.add-user.title'), route('users-create', [], false));
});

// Web Analytics Italia > Privacy
Breadcrumbs::register('privacy', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.privacy.title'), route('privacy', [], false));
});

// Web Analytics Italia > User profile
Breadcrumbs::register('user_profile', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.profile.title'), route('user_profile', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::register('spid-auth_login', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.spid-auth_login.title'), route('spid-auth_login', [], false));
});

// Web Analytics Italia > Email verification
Breadcrumbs::register('auth-verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Web Analytics Italia > Register
Breadcrumbs::register('auth-register', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.auth-register.title'), route('auth-register', [], false));
});

// Web Analytics Italia > Admin login
Breadcrumbs::register('admin-login', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-login.title'), route('admin-login', [], false));
});

// Web Analytics Italia > Admin User profile
Breadcrumbs::register('admin-user_show', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-user_show.title'), route('admin-user_show', ['user' => $user], false));
});

// Web Analytics Italia > Admin User profile edit
Breadcrumbs::register('admin-user_edit', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-user_edit.title'), route('admin-user_edit', ['user' => $user], false));
});

// Web Analytics Italia > Admin forgot password
Breadcrumbs::register('admin-password_forgot', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_forgot.title'), route('admin-password_forgot', [], false));
});

// Web Analytics Italia > Admin password change
Breadcrumbs::register('admin-password_change', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_change.title'), route('admin-password_change', [], false));
});

// Web Analytics Italia > Admin password reset
Breadcrumbs::register('admin-password_reset', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-password_reset.title'), route('admin-password_reset', [], false));
});

// Web Analytics Italia > Admin dashboard
Breadcrumbs::register('admin-dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-dashboard.title'), route('admin-dashboard', [], false));
});

// Web Analytics Italia > Admin dashboard > Add user
Breadcrumbs::register('admin-user_add', function ($breadcrumbs) {
    $breadcrumbs->parent('admin-dashboard');
    $breadcrumbs->push(__('ui.pages.admin-user_add.title'), route('admin-user_add', [], false));
});

// Web Analytics Italia > Admin email verification
Breadcrumbs::register('admin-verify', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-verify.title'), route('admin-verify', [], false));
});

// Web Analytics Italia > Admin email verification resend
Breadcrumbs::register('admin-verify_resend', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.admin-verify_resend.title'), route('admin-verify_resend', [], false));
});

// Web Analytics Italia > Error 404
Breadcrumbs::register('errors.404', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('ui.pages.404.title'));
});
