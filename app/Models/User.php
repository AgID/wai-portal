<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
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
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array mass assignable attributes
     */
    protected $fillable = [
        'spidCode',
        'name',
        'uuid',
        'familyName',
        'fiscalNumber',
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
    ];

    /**
     * Find a User instance by Fiscal Number.
     *
     * @param string $fiscalNumber fiscal Number
     *
     * @return User|null the User or null if not found
     */
    public static function findByFiscalNumber(string $fiscalNumber): ?User
    {
        return User::where('fiscalNumber', $fiscalNumber)->first();
    }

    /**
     * Find a deleted User instance by Fiscal Number.
     *
     * @param string $fiscalNumber fiscal Number
     *
     * @return User|null the User or null if not found
     */
    public static function findTrashedByFiscalNumber(string $fiscalNumber): ?User
    {
        return User::onlyTrashed()->where('fiscalNumber', $fiscalNumber)->first();
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
        return trim((null === $this->name ? '' : $this->name . ' ') . ($this->familyName ?? ''));
    }

    /**
     * Return name, familyName and email of this user in printable format.
     *
     * @return string the printable user representation
     */
    public function getInfo(): string
    {
        return (null === $this->name ? '' : $this->name . ' ') . (null === $this->familyName ? '' : $this->familyName . ' ') . '[' . $this->email . ']';
    }

    /**
     * Configure information for notifications over mail channel.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to
     *                                                        or null if user is registering a new P.A.
     *
     * @return mixed the user email address or an array containing email and user name/surname
     */
    public function sendEmailVerificationNotification(PublicAdministration $publicAdministration = null)
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
     */
    public function sendWebsiteArchivedNotification(Website $website): void
    {
        $this->notify(new WebsiteArchivedUserEmail($website));
    }
}
