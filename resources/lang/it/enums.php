<?php

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Enums\Logs\JobType;
use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;

return [

    PublicAdministrationStatus::class => [
        PublicAdministrationStatus::PENDING => 'in attesa',
        PublicAdministrationStatus::ACTIVE => 'attiva',
        PublicAdministrationStatus::SUSPENDED => 'sospesa',
    ],

    UserPermission::class => [
        UserPermission::ACCESS_ADMIN_AREA => "Accesso all'area amministrativa",
        UserPermission::MANAGE_USERS => 'Gestione utenti',
        UserPermission::MANAGE_WEBSITES => 'Gestione siti',
        UserPermission::VIEW_LOGS => 'Visualizzare i log',
        UserPermission::MANAGE_ANALYTICS => 'Gestione analytics',
        UserPermission::READ_ANALYTICS => 'Lettura analytics',
        UserPermission::DO_NOTHING => 'Nessun permesso',
    ],

    UserRole::class => [
        UserRole::SUPER_ADMIN => 'Super amministatore di ' . config('app.name'),
        UserRole::ADMIN => 'Amministarore della propria PA',
        UserRole::DELEGATED => 'Incaricato della propria PA',
        UserRole::REGISTERED => 'Utente registrato',
        UserRole::REMOVED => 'Utente sospeso',
    ],

    UserStatus::class => [
        UserStatus::INVITED => 'invitato',
        UserStatus::INACTIVE => 'inattivo',
        UserStatus::PENDING => 'in attesa',
        UserStatus::ACTIVE => 'attivo',
        UserStatus::SUSPENDED => 'sospeso',
    ],

    WebsiteStatus::class => [
        WebsiteStatus::PENDING => 'in attesa',
        WebsiteStatus::ACTIVE => 'attivo',
        WebsiteStatus::ARCHIVED => 'archiviato',
    ],

    WebsiteType::class => [
        WebsiteType::PRIMARY => 'sito istituzionale',
        WebsiteType::SECONDARY => 'informativo o tematico',
        WebsiteType::WEBAPP => 'interattivo o web application',
        WebsiteType::TESTING => 'testing o staging',
    ],

    WebsiteAccessType::class => [
        WebsiteAccessType::NO_ACCESS => 'nessun accesso',
        WebsiteAccessType::VIEW => 'sola lettura',
        WebsiteAccessType::WRITE => 'gestione analytics',
        WebsiteAccessType::ADMIN => 'amministratore',
    ],

    EventType::class => [
        EventType::EXCEPTION => 'Errore',
        EventType::ANALYTICS_LOGIN => 'Login Servizio Analytics',
        EventType::PENDING_WEBSITES_CHECK_COMPLETED => 'Verifica siti web in attesa completata',
        EventType::TRACKING_WEBSITES_CHECK_COMPLETED => 'Verifica tracciamento siti web completata',
        EventType::IPA_UPDATE_COMPLETED => 'Aggiornamento I.P.A. completato',
        EventType::PUBLIC_ADMINISTRATION_REGISTERED => 'Pubblica Amministrazione registrata',
        EventType::PUBLIC_ADMINISTRATION_ACTIVATED => 'Pubblica Amministrazione attivata',
        EventType::PUBLIC_ADMINISTRATION_ACTIVATION_FAILED => 'Attivazione Pubblica Amministrazione fallita',
        EventType::PUBLIC_ADMINISTRATION_UPDATED => 'Pubblica Amministrazione aggiornata',
        EventType::PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED => 'Sito primario modificato',
        EventType::PUBLIC_ADMINISTRATION_PURGED => 'Pubblica Amministrazione rimossa',
        EventType::USER_LOGIN => 'Utente autenticato',
        EventType::USER_LOGOUT => 'Utente sloggato',
        EventType::USER_REGISTERED => 'Utente registrato',
        EventType::USER_INVITED => 'Utente invitato',
        EventType::USER_VERIFIED => 'Email utente verificata',
        EventType::USER_ACTIVATED => 'Utente attivato',
        EventType::USER_EMAIL_CHANGED => 'Email utente modificato',
        EventType::USER_STATUS_CHANGED => 'Stato utente modificato',
        EventType::USER_DELETED => 'Utente eliminato',
        EventType::USER_RESTORED => 'Utente ripristinato',
        EventType::USER_WEBSITE_ACCESS_CHANGED => 'Livello di accesso a sito per utente modificato',
        EventType::WEBSITE_ADDED => 'Sito web aggiunto',
        EventType::WEBSITE_ACTIVATED => 'Sito web attivato',
        EventType::WEBSITE_ARCHIVING => 'Archiviazione sito web programmata',
        EventType::WEBSITE_ARCHIVED => 'Sito web archiviato',
        EventType::WEBSITE_PURGING => 'Rimozione sito web programmata',
        EventType::WEBSITE_PURGED => 'Sito web rimosso',
        EventType::USERS_INDEXING_COMPLETED => 'Aggiornamento indice utenti completato',
        EventType::WEBSITES_INDEXING_COMPLETED => 'Aggiornamento indice siti web completato',
    ],

    ExceptionType::class => [
        ExceptionType::GENERIC => 'Errore generico',
        ExceptionType::ANALYTICS_ACCOUNT => 'Errore autenticazione Servizio Analytics',
        ExceptionType::ANALYTICS_SERVICE => 'Errore Servizio Analytics',
        ExceptionType::ANALYTICS_COMMAND => 'Errore comando a Servizio Analytics',
        ExceptionType::HTTP_CLIENT_ERROR => 'Errore HTTP del client (4xx)',
        ExceptionType::SERVER_ERROR => 'Errore interno del server',
        ExceptionType::TENANT_SELECTION => 'Errore P.A. non selezionata',
        ExceptionType::IPA_INDEX_SEARCH => 'Errore ricerca indice I.P.A.',
        ExceptionType::WEBSITE_INDEX_SEARCH => 'Errore ricerca indice siti web',
        ExceptionType::USER_INDEX_SEARCH => 'Errore ricerca indice utenti',
        ExceptionType::INVALID_WEBSITE_STATUS => 'Errore stato sito web non valido',
        ExceptionType::INVALID_OPERATION => 'Errore comando non valido',
        ExceptionType::INVALID_USER_STATUS => 'Error stato utente non valido',
    ],

    JobType::class => [
        JobType::CLEAR_PASSWORD_TOKEN => 'Rimozione token reset password',
        JobType::UPDATE_IPA => 'Aggiornamento indice I.P.A.',
        JobType::SEND_RESET_PASSWORD_TOKEN => 'Invio token reset password',
        JobType::SEND_EMAIL_VERIFICATION_TOKEN => 'Invio token verifica email',
    ],

];
