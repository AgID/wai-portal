<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

/**
 * User model.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRolesAndAbilities;
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
        'email_verified_at' => 'datetime',
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
     * User status accessor.
     *
     * @param int $value the database value
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException if status is not valid
     *
     * @return UserStatus the status
     *
     * @see \App\Enums\UserStatus
     */
    public function getStatusAttribute($value): UserStatus
    {
        return new UserStatus((int) $value);
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
     * @return mixed the user email address or an array containing email and user name/surname
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
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
}
