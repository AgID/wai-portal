<?php

namespace App\Models;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Website model.
 */
class Website extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array mass assignable attributes
     */
    protected $fillable = [
        'name',
        'url',
        'type',
        'public_administration_id',
        'analytics_id',
        'slug',
        'status',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string the DB column name to use for route binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Website status accessor.
     *
     * @param int $value the database value
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException if status is not valid
     *
     * @return WebsiteStatus the status
     *
     * @see \App\Enums\WebsiteStatus
     */
    public function getStatusAttribute($value): WebsiteStatus
    {
        return new WebsiteStatus((int) $value);
    }

    /**
     * Website type accessor.
     *
     * @param int $value the database value
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException if type is not valid
     *
     * @return WebsiteType the type
     *
     * @see \App\Enums\WebsiteType
     */
    public function getTypeAttribute($value)
    {
        return new WebsiteType((int) $value);
    }

    /**
     * The Public Administration this Website belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo the relation to the public administration this website belongs to
     *
     * @see \App\Models\PublicAdministration
     */
    public function publicAdministration(): BelongsTo
    {
        return $this->belongsTo(PublicAdministration::class);
    }

    /**
     * The keywords connected to this website or null if none.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany the relation to the keywords connected to this website
     *
     * @see \App\Models\Keyword
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }

    /**
     * Return name and slug of this website in printable format.
     *
     * @return string the printable website representation
     */
    public function getInfo(): string
    {
        return '"' . $this->name . '" [' . $this->slug . ']';
    }

    /**
     * Change website status to active.
     *
     * @return bool true if operation is successful, false otherwise
     */
    public function markActive(): bool
    {
        return $this->fill([
            'status' => WebsiteStatus::ACTIVE,
        ])->save();
    }

    /**
     * Change website status to archived.
     *
     * @return bool true if operation is successful, false otherwise
     */
    public function markArchived(): bool
    {
        return $this->fill([
            'status' => WebsiteStatus::ARCHIVED,
        ])->save();
    }

    /**
     * Get the website administrators.
     *
     * @return array the users list
     */
    public function getAdministrators(): array
    {
        if ($this->publicAdministration->status->is(PublicAdministrationStatus::PENDING)) {
            return $this->publicAdministration->users()->where('status', UserStatus::PENDING)->get();
        }

        Bouncer::scope()->to($this->publicAdministration->id);

        return Bouncer::whereIs('admin', $this)->get();
    }
}
