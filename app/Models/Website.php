<?php

namespace App\Models;

use App\Enums\WebsiteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
