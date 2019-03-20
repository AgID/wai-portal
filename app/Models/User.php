<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable
{
    use Notifiable;
    use HasRolesAndAbilities;
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
        'status',
        'partial_analytics_password',
    ];

    /**
     * The verification token used in email verification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function verificationToken()
    {
        return $this->hasOne(VerificationToken::class);
    }

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
    public function publicAdministration()
    {
        return $this->belongsTo(PublicAdministration::class);
    }

    /**
     * The Websites of the Public Administration this User belongs to.
     *
     * @return \App\Models\Website|null
     */
    public function getWebsites()
    {
        if (isset($this->publicAdministration)) {
            return $this->publicAdministration->websites;
        } else {
            return null;
        }
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
}
