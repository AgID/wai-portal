<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRolesAndAbilities;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spidCode',
        'name',
        'familyName',
        'fiscalNumber',
        'email',
        'password',
        'status',
        'partial_analytics_password',
        'password_changed_at',
    ];

    /**
     * The password reset token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function passwordResetToken()
    {
        return $this->hasOne(PasswordResetToken::class);
    }

    /**
     * Find a User instance by Fiscal Number.
     *
     * @param string fiscal Number
     *
     * @return User|null the User found or null if not found
     */
    public static function findByFiscalNumber(string $fiscalNumber)
    {
        return User::where('fiscalNumber', $fiscalNumber)->first();
    }

    /**
     * The Public Administration this User belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation|null
     */
    public function publicAdministrations()
    {
        return $this->belongsToMany(PublicAdministration::class);
    }

    /**
     * Return calculated password for this User's Analytics Service account.
     *
     * @return string
     */
    public function getAnalyticsPasswordAttribute()
    {
        return md5($this->partial_analytics_password . config('app.salt'));
    }

    /**
     * Return name, familyName and email of this user in printable format.
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->name . ' ' . $this->familyName . ' [' . $this->email . ']';
    }

    /**
     * Send the email verification notification.
     *
     * @return void
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
    public function isPasswordExpired()
    {
        return Carbon::now()->diffInDays($this->password_changed_at) >= config('auth.password_expiry');
    }
}
