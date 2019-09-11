<?php

use Illuminate\Support\Facades\Route;

// Web Analytics Italia*
Breadcrumbs::for('home', function ($trail) {
    $trail->push(config('app.name'), route('home'));
});

// Web Analytics Italia > FAQ*
Breadcrumbs::for('faq', function ($trail) {
    $trail->parent('home');
    $trail->push(__('FAQ - Domande ricorrenti'), route('faq'));
});

// Web Analytics Italia > Open data*
Breadcrumbs::for('open-data', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Open data'), route('open-data'));
});

// Web Analytics Italia > Contacts*
Breadcrumbs::for('contacts', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Contattaci'), route('contacts'));
});

// Web Analytics Italia > Privacy*
Breadcrumbs::for('privacy', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Privacy'), route('privacy'));
});

// Web Analytics Italia > Legal Notes*
Breadcrumbs::for('legal-notes', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Note legali'), route('legal-notes'));
});

// Web Analytics Italia > Dashboard*
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Dashboard'), route('dashboard'));
});

// Web Analytics Italia > Dashboard > Logs view*
Breadcrumbs::for('logs.show', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(__('Visualizzazione log'), route('logs.show'));
});

// Web Analytics Italia > Websites*
Breadcrumbs::for('websites.index', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Siti web'), route('websites.index'));
});

// Web Analytics Italia > Websites > Add website*
Breadcrumbs::for('websites.create', function ($trail) {
    $trail->parent('websites.index');
    $trail->push(__('Aggiungi un sito web'), route('websites.create'));
});

// Web Analytics Italia > Websites > [website->name]*
Breadcrumbs::for('websites.show', function ($trail, $website) {
    $trail->parent('websites.index');
    $trail->push($website->name ?? '', route('websites.show', ['website' => $website]));
});

// Web Analytics Italia > Websites > [website->name] (edit)*
Breadcrumbs::for('websites.edit', function ($trail, $website) {
    $trail->parent('websites.index');
    $trail->push(implode(' ', [$website->name ?? '', __('(modifica)')]), route('websites.edit', ['website' => $website]));
});

// Web Analytics Italia > Users*
Breadcrumbs::for('users.index', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Utenti'), route('users.index'));
});

// Web Analytics Italia > Users > Invite a new user*
Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users.index');
    $trail->push(__('Invita un utente'), route('users.create'));
});

// Web Analytics Italia > Users > [user->full_name]*
Breadcrumbs::for('users.show', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push($user->full_name ?? '', route('users.show', ['user' => $user]));
});

// Web Analytics Italia > Users > [user->full_name] (edit)*
Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push(implode(' ', [$user->full_name ?? '', __('(modifica)')]), route('users.edit', ['user' => $user]));
});

// Web Analytics Italia > User profile*
Breadcrumbs::for('user.profile.edit', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Profilo utente'), route('user.profile.edit'));
});

// Web Analytics Italia > Super admin login*
Breadcrumbs::for('admin.login.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Accesso super amministratori'), route('admin.login.show'));
});

// Web Analytics Italia > Super admin user profile*
Breadcrumbs::for('admin.user.profile.edit', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Profilo utente'), route('admin.user.profile.edit'));
});

// Web Analytics Italia > Super admin dashboard*
Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Dashboard amministrativa'), route('admin.dashboard'));
});

// Web Analytics Italia > Super admin dashboard > Logs view*
Breadcrumbs::for('admin.logs.show', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(__('Visualizzazione log'), route('admin.logs.show'));
});

// Web Analytics Italia > Super admin dashboard > Super admin Users*
Breadcrumbs::for('admin.users.index', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Utenti super amministratori'), route('admin.users.index'));
});

// Web Analytics Italia > Super admin dashboard > Super admin Users > [user->full_name]*
Breadcrumbs::for('admin.users.show', function ($trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->full_name ?? '', route('admin.users.show', ['user' => $user]));
});

// Web Analytics Italia > Super admin dashboard > Super admin Users > Add super admin user*
Breadcrumbs::for('admin.users.create', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Aggiungi un utente super amministratore'), route('admin.users.create'));
});

// Web Analytics Italia > Super admin dashboard > Super admin Users > [user->full_name] (edit)*
Breadcrumbs::for('admin.users.edit', function ($trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push(implode(' ', [$user->full_name ?? '', __('(modifica)')]), route('admin.users.edit', ['user' => $user]));
});

// Web Analytics Italia > Forgot password?*
Breadcrumbs::for('admin.password.forgot.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Password dimenticata?'), route('admin.password.forgot.show'));
});

// Web Analytics Italia > Password change*
Breadcrumbs::for('admin.password.change.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Cambio della password'), route('admin.password.change.show'));
});

// Web Analytics Italia > Password reset*
Breadcrumbs::for('admin.password.reset.show', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Reset della password'), route('admin.password.reset.show'));
});

// Web Analytics Italia > Websites*
Breadcrumbs::for('admin.publicAdministration.websites.index', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Siti web'), route('admin.publicAdministration.websites.index'));
});

// Web Analytics Italia > Websites > Add website*
Breadcrumbs::for('admin.publicAdministration.websites.create', function ($trail) {
    $trail->parent('admin.publicAdministration.websites.index');
    $trail->push(__('Aggiungi un sito web'), route('admin.publicAdministration.websites.create'));
});

// Web Analytics Italia > Websites > [website->name]*
Breadcrumbs::for('admin.publicAdministration.websites.show', function ($trail, $website) {
    $trail->parent('admin.publicAdministration.websites.index');
    $trail->push($website->name ?? '', route('admin.publicAdministration.websites.show', ['website' => $website]));
});

// Web Analytics Italia > Websites > [website->name] (edit)*
Breadcrumbs::for('admin.publicAdministration.websites.edit', function ($trail, $website) {
    $trail->parent('admin.publicAdministration.websites.index');
    $trail->push(implode(' ', [$website->name ?? '', __('(modifica)')]), route('admin.publicAdministration.websites.edit', ['website' => $website]));
});

// Web Analytics Italia > Users*
Breadcrumbs::for('admin.publicAdministration.users.index', function ($trail) {
    $trail->parent('home');
    $trail->push(__('Utenti'), route('admin.publicAdministration.users.index'));
});

// Web Analytics Italia > Users > Invite a new user*
Breadcrumbs::for('admin.publicAdministration.create', function ($trail) {
    $trail->parent('admin.publicAdministration.users.index');
    $trail->push(__('Invita un utente'), route('admin.publicAdministration.users.create'));
});

// Web Analytics Italia > Users > [user->full_name]*
Breadcrumbs::for('admin.publicAdministration.users.show', function ($trail, $user) {
    $trail->parent('admin.publicAdministration.users.index');
    $trail->push($user->full_name ?? '', route('admin.publicAdministration.users.show', ['user' => $user]));
});

// Web Analytics Italia > Users > [user->full_name] (edit)*
Breadcrumbs::for('admin.publicAdministration.users.edit', function ($trail, $user) {
    $trail->parent('admin.publicAdministration.users.index');
    $trail->push(implode(' ', [$user->full_name ?? '', __('(modifica)')]), route('admin.publicAdministration.users.edit', ['user' => $user]));
});
