<?php

namespace App\Models;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Notifications\WebsiteActivatedPAEmail;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Public Administration model.
 */
class PublicAdministration extends Model
{
    use CastsEnums;
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array Mass assignable attributes
     */
    protected $fillable = [
        'ipa_code',
        'name',
        'pec_address',
        'city',
        'county',
        'region',
        'type',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * The attributes that should be cast to enums classes.
     *
     * @var array enum casted attributes
     */
    protected $enumCasts = [
        'status' => PublicAdministrationStatus::class,
    ];

    /**
     * Find a Public Administration instance by IPA code.
     *
     * @param string IPA code
     *
     * @return PublicAdministration|null The Public Administration found or null if not found
     */
    public static function findByIpaCode(string $ipa_code): ?PublicAdministration
    {
        return PublicAdministration::where('ipa_code', $ipa_code)->first();
    }

    /**
     * Find a deleted Public Administration instance by IPA code.
     *
     * @param string IPA code
     *
     * @return PublicAdministration|null The Public Administration found or null if not found
     */
    public static function findTrashedByIpaCode(string $ipa_code): ?PublicAdministration
    {
        return PublicAdministration::onlyTrashed()->where('ipa_code', $ipa_code)->first();
    }

    /**
     * Get the route key for the model.
     *
     * @return string The DB column name to use for route binding
     */
    public function getRouteKeyName(): string
    {
        return 'ipa_code';
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
        return empty(trim($this->name)) ? $this->pec_address : [$this->pec_address, $this->name];
    }

    /**
     * The users belonging to this Public Administration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany the relation to the users belonging to this Public Administration
     *
     * @see \App\Models\User
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The websites of this Public Administration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany the relation to the websites of this Public Administration
     *
     * @see \App\Models\Website
     */
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    /**
     * Return name and IPA code of this public administration in printable format.
     *
     * @return string the printable public administration representation
     */
    public function getInfo(): string
    {
        return '"' . $this->name . '" [' . $this->ipa_code . ']';
    }

    /**
     * Get the administrators users of this public administration.
     *
     * @return Collection the users list
     */
    public function getAdministrators(): Collection
    {
        if ($this->status->is(PublicAdministrationStatus::PENDING)) {
            return $this->users()->where('status', UserStatus::PENDING)->get();
        }

        Bouncer::scope()->to($this->id);
        $administrators = User::whereIs(UserRole::ADMIN)->get();
        Bouncer::scope()->to(session('tenant_id'));

        return $administrators;
    }

    /**
     * Get the active administrators users of this public administration.
     *
     * @return Collection the users list
     */
    public function getActiveAdministrators(): Collection
    {
        return $this->getAdministrators()->filter(function ($administrator) {
            return $administrator->status->is(UserStatus::ACTIVE);
        });
    }

    /**
     * Get all the non administrators users of this public administration.
     *
     * @return Collection the users list
     */
    public function getNonAdministrators(): Collection
    {
        if ($this->status->is(PublicAdministrationStatus::PENDING)) {
            return collect([]);
        }

        Bouncer::scope()->to($this->id);
        $nonAdministrators = User::whereIs(UserRole::DELEGATED)->get();
        Bouncer::scope()->to(session('tenant_id'));

        return $nonAdministrators;
    }

    /**
     * Notify website activated.
     *
     * @param Website $website the website
     */
    public function sendWebsiteActivatedNotification(Website $website): void
    {
        $this->notify(new WebsiteActivatedPAEmail($website));
    }
}
