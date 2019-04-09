<?php

namespace App\Models;

use App\Enums\PublicAdministrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Public Administration model.
 */
class PublicAdministration extends Model
{
    use SoftDeletes;

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
     * Find a Public Administration instance by IPA code.
     *
     * @param string IPA code
     *
     * @return PublicAdministration|null The Public Administration found or null if not found
     */
    public static function findByIPACode(string $ipa_code): ?PublicAdministration
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
    public static function findTrashedByIPACode(string $ipa_code): ?PublicAdministration
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
     * Public administration status accessor.
     *
     * @param int $value the database value
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException if status is not valid
     *
     * @return PublicAdministrationStatus the status
     *
     * @see \App\Enums\PublicAdministrationStatus
     */
    public function getStatusAttribute($value): PublicAdministrationStatus
    {
        return new PublicAdministrationStatus((int) $value);
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
}
