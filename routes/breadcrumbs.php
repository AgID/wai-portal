<?php

// Web Analytics Italia
Breadcrumbs::for('home', function ($trail) {
    $trail->push(__('ui.site_title'), route('home', [], false));
});

// Web Analytics Italia > FAQs
Breadcrumbs::for('faq', function ($trail) {
    $trail->parent('home');
    $trail->push(__('FAQ - Domande ricorrenti'), route('faq', [], false));
});

// Web Analytics Italia > Contattaci
Breadcrumbs::for('contacts', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Contattaci'), route('contacts', [], false));
});

// Web Analytics Italia > Privacy
Breadcrumbs::for('privacy', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.privacy.title'), route('privacy', [], false));
});

// Web Analytics Italia > Legal Notes
Breadcrumbs::for('legal-notes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.legal-notes.title'), route('legal-notes', [], false));
});

// Web Analytics Italia > Dashboard
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.dashboard.title'), route('dashboard', [], false));
});

// Web Analytics Italia > Dashboard > Websites
Breadcrumbs::for('websites.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(__('ui.pages.websites.index.title'), route('websites.index', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Add primary website
Breadcrumbs::for('websites.create.primary', function ($trail) {
    $trail->parent('websites.index');
    $trail->push(__('ui.pages.websites.add-primary.title'), route('websites.create.primary', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Add website
Breadcrumbs::for('websites.create', function ($trail) {
    $trail->parent('websites.index');
    $trail->push(__('ui.pages.websites.add.title'), route('websites.create', [], false));
});

// Web Analytics Italia > Dashboard > Websites > Show website
Breadcrumbs::for('websites.show', function ($trail, $website) {
    $trail->parent('websites.index');
    $trail->push(__('ui.pages.websites.show.title'), route('websites.show', ['website' => $website], false));
});

// Web Analytics Italia > Dashboard > Websites > Edit website
Breadcrumbs::for('websites.edit', function ($trail, $website) {
    $trail->parent('websites.index');
    $trail->push(__('ui.pages.websites.edit.title'), route('websites.edit', ['website' => $website], false));
});

// Web Analytics Italia > Dashboard > Websites > Javascript snippet
Breadcrumbs::for('websites.snippet.javascript', function ($trail, $website) {
    $trail->parent('websites.index');
    $trail->push(__('ui.pages.websites.javascript-snippet.title'), route('websites.snippet.javascript', ['website' => $website], false));
});

// Web Analytics Italia > Dashboard > Users
Breadcrumbs::for('users.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(__('ui.pages.users.index.title'), route('users.index', [], false));
});

// Web Analytics Italia > Dashboard > Users > Add user
Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users.index');
    $trail->push(__('ui.pages.users.add.title'), route('users.create', [], false));
});

// Web Analytics Italia > Dashboard > Users > Show user
Breadcrumbs::for('users.show', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push(__('ui.pages.users.show.title'), route('users.show', ['user' => $user], false));
});

// Web Analytics Italia > Dashboard > Users > Edit user
Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push(__('ui.pages.users.edit.title'), route('users.edit', ['user' => $user], false));
});

// Web Analytics Italia > User profile
Breadcrumbs::for('user.profile', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.profile.title'), route('user.profile', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::for('spid-auth_login', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.spid-auth_login.title'), route('spid-auth_login', [], false));
});

// Web Analytics Italia > SPID Login
Breadcrumbs::for('spid-auth_acs', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.spid-auth_login.title'), route('spid-auth_login', [], false));
});

// Web Analytics Italia > Email verification
Breadcrumbs::for('auth-verify', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.auth-verify.title'), route('auth-verify', [], false));
});

// Web Analytics Italia > Register
Breadcrumbs::for('auth.register.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.auth.register.title'), route('auth.register.show', [], false));
});

// Web Analytics Italia > Admin login
Breadcrumbs::for('admin.login.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-login.title'), route('admin.login.show', [], false));
});

// Web Analytics Italia > Admin Users
Breadcrumbs::for('admin.users.index', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('ui.pages.admin.users.index.title'), route('admin.users.index', [], false));
});

// Web Analytics Italia > Admin Users > Show user
Breadcrumbs::for('admin.users.show', function ($trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push(__('ui.pages.admin.users.show.title'), route('admin.users.show', ['user' => $user], false));
});

// Web Analytics Italia > Admin Users > Add user
Breadcrumbs::for('admin.users.create', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('ui.pages.admin.users.add.title'), route('admin.users.create', [], false));
});

// Web Analytics Italia > Admin Users > Edit user
Breadcrumbs::for('admin.users.edit', function ($trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push(__('ui.pages.admin.users.edit.title'), route('admin.users.edit', ['user' => $user], false));
});

// Web Analytics Italia > Admin forgot password
Breadcrumbs::for('admin.password.forgot.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-password_forgot.title'), route('admin.password.forgot.show', [], false));
});

// Web Analytics Italia > Admin password change
Breadcrumbs::for('admin.password.change.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-password_change.title'), route('admin.password.change.show', [], false));
});

// Web Analytics Italia > Admin password reset
Breadcrumbs::for('admin.password.reset.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-password_reset.title'), route('admin.password.reset.show', [], false));
});

// Web Analytics Italia > Admin dashboard
Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin.dashboard.title'), route('admin.dashboard', [], false));
});

// Web Analytics Italia > Admin email verification
Breadcrumbs::for('admin-verify', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-verify.title'), route('admin-verify', [], false));
});

// Web Analytics Italia > Admin email verification resend
Breadcrumbs::for('admin-verify_resend', function ($trail) {
    $trail->parent('home');
    $trail->push(__('ui.pages.admin-verify_resend.title'), route('admin-verify_resend', [], false));
});
