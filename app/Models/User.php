<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserRestored;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
use App\Notifications\ExpiredInvitationLinkVisitedEmail;
use App\Notifications\PrimaryWebsiteNotTrackingUserEmail;
use App\Notifications\VerifyEmail;
use App\Notifications\WebsiteActivatedUserEmail;
use App\Notifications\WebsiteArchivedUserEmail;
use App\Notifications\WebsiteArchivingUserEmail;
use App\Notifications\WebsitePurgingUserEmail;
use App\Traits\HasAnalyticsServiceAccount;
use App\Traits\HasWebsitePermissions;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Silber\Bouncer\Database\HasRolesAndAbilities;

/**
 * User model.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use CastsEnums;
    use Notifiable;
    use HasRolesAndAbilities;
    use HasWebsitePermissions;
    use HasAnalyticsServiceAccount;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array mass assignable attributes
     */
    protected $fillable = [
        'spid_code',
        'name',
        'uuid',
        'family_name',
        'fiscal_number',
        'email',
        'password',
        'status',
        'partial_analytics_password',
        'password_changed_at',
        'last_access_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array serialization hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
        'partial_analytics_password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array attributes to cast
     */
    protected $casts = [
        'last_access_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * The attributes that should be cast to enums classes.
     *
     * @var array enum casted attributes
     */
    protected $enumCasts = [
        'status' => UserStatus::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array dispatched events list
     */
    protected $dispatchesEvents = [
        'updating' => UserUpdating::class,
        'updated' => UserUpdated::class,
        'restored' => UserRestored::class,
    ];

    /**
     * Find a User instance by fiscal number.
     *
     * @param string $fiscalNumber fiscal number
     *
     * @return User|null the User or null if not found
     */
    public static function findByFiscalNumber(string $fiscalNumber): ?User
    {
        return User::where('fiscal_number', $fiscalNumber)->first();
    }

    /**
     * Find a deleted User instance by fiscal number.
     *
     * @param string $fiscalNumber fiscal number
     *
     * @return User|null the User or null if not found
     */
    public static function findTrashedByFiscalNumber(string $fiscalNumber): ?User
    {
        return User::onlyTrashed()->where('fiscal_number', $fiscalNumber)->first();
    }

    /**
     * Set the fiscal number.
     *
     * @param string $fiscalNumber the fiscal number
     */
    public function setFiscalNumberAttribute($fiscalNumber)
    {
        $this->attributes['fiscal_number'] = strtoupper($fiscalNumber);
    }

    /**
     * Get the route key for the model.
     *
     * @return string the route key name
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get recipient for mail notifications.
     *
     * @param Notification $notification the notification
     *
     * @return array|string the recipient
     */
    public function routeNotificationForMail($notification)
    {
        return empty($this->full_name) ? $this->email : [$this->email, $this->full_name];
    }

    /**
     * The password reset token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne the relation with password reset token
     */
    public function passwordResetToken(): HasOne
    {
        return $this->hasOne(PasswordResetToken::class);
    }

    /**
     * The Public Administration this User belongs to, as Eloquent relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany the relation with public administrations
     *
     * @see \App\Models\PublicAdministration
     */
    public function publicAdministrations(): BelongsToMany
    {
        return $this->belongsToMany(PublicAdministration::class);
    }

    /**
     * Return calculated password for this User's Analytics Service account.
     *
     * @return string The transformed value
     */
    public function getAnalyticsPasswordAttribute(): string
    {
        return md5($this->partial_analytics_password . config('app.salt'));
    }

    /**
     * User full name accessor.
     *
     * @return string the user full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name ? implode(' ', [trim($this->name), trim($this->family_name)]) : $this->fiscal_number;
    }

    /**
     * Return name, family_name and email of this user in printable format.
     *
     * @return string the printable user representation
     */
    public function getInfoAttribute(): string
    {
        return $this->full_name . ' [' . $this->email . ']';
    }

    /**
     * Return all user roles printable format.
     *
     * @return string the printable user roles representation
     */
    public function getAllRoleNamesAttribute(): string
    {
        return $this->roles->map(function ($role) {
            return UserRole::getDescription($role->name);
        })->join(' ');
    }

    /**
     * Return a collection of all the user roles.
     *
     * @return Collection the collection of all the user roles
     */
    public function getAllRoles(): Collection
    {
        return $this->roles->map(function ($role) {
            return [
                'name' => $role->name,
                'description' => UserRole::getDescription($role->name),
                'longDescription' => UserRole::getLongDescription($role->name),
            ];
        });
    }

    /**
     * Configure information for notifications over mail channel.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to or null if user is registering a new Public Administration
     */
    public function sendEmailVerificationNotification(?PublicAdministration $publicAdministration = null): void
    {
        $this->notify(new VerifyEmail($publicAdministration));
    }

    /**
     * Check if the current password is expired.
     *
     * @return bool
     */
    public function isPasswordExpired(): bool
    {
        return Carbon::now()->diffInDays($this->password_changed_at) >= config('auth.password_expiry');
    }

    /**
     * Check whether this user is the last active administrator of the specified public administration.
     *
     * @param PublicAdministration $publicAdministration the public administration
     *
     * @return bool true if the specified user is the last active administrator
     */
    public function isTheLastActiveAdministratorOf(PublicAdministration $publicAdministration): bool
    {
        $activeAdministrators = $publicAdministration->getActiveAdministrators();

        return 1 === $activeAdministrators->count() && $this->is($activeAdministrators->first());
    }

    /**
     * Check whether this user is the last active super administrator.
     *
     * @return bool true if the specified user is the last active super administrator
     */
    public function isTheLastActiveSuperAdministrator(): bool
    {
        $activeSuperAdministrators = static::whereIs(UserRole::SUPER_ADMIN)->get()->filter(function ($administrator) {
            return $administrator->status->is(UserStatus::ACTIVE);
        });

        return 1 === $activeSuperAdministrators->count() && $this->is($activeSuperAdministrators->first());
    }

    /**
     * Notify website activated.
     *
     * @param Website $website the website
     */
    public function sendWebsiteActivatedNotification(Website $website): void
    {
        $this->notify(new WebsiteActivatedUserEmail($website));
    }

    /**
     * Notify website scheduled for purging.
     *
     * @param Website $website the website
     */
    public function sendWebsitePurgingNotification(Website $website): void
    {
        $this->notify(new WebsitePurgingUserEmail($website));
    }

    /**
     * Notify website scheduled for archiving.
     *
     * @param Website $website the website
     * @param int $daysLeft the number of days left before automatic archiving
     */
    public function sendWebsiteArchivingNotification(Website $website, int $daysLeft): void
    {
        $this->notify(new WebsiteArchivingUserEmail($website, $daysLeft));
    }

    /**
     * Notify website archived.
     *
     * @param Website $website the website
     * @param bool $manual whether the website was archived manually
     */
    public function sendWebsiteArchivedNotification(Website $website, bool $manual): void
    {
        $this->notify(new WebsiteArchivedUserEmail($website, $manual));
    }

    /**
     * Notify primary website not tracking.
     */
    public function sendPrimaryWebsiteNotTrackingNotification(): void
    {
        $this->notify(new PrimaryWebsiteNotTrackingUserEmail());
    }

    /**
     * Notify an expired verification URL was used.
     *
     * @param User $user the user the expired URL refers to
     */
    public function sendExpiredInvitationLinkVisited(User $user): void
    {
        $this->notify(new ExpiredInvitationLinkVisitedEmail($user));
    }
}
