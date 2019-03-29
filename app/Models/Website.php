<?php

namespace App\Models;

use App\Enums\WebsiteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The Public Administration this Website belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function publicAdministration()
    {
        return $this->belongsTo(PublicAdministration::class);
    }

    /**
     * Get total visits for this Website via AnalyticsService.
     *
     * return int
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }

    /**
     * Get last month visits for this Website via AnalyticsService.
     *
     * return int
     */
    public function markActive(): bool
    {
        return $this->fill([
            'status' => WebsiteStatus::ACTIVE,
        ])->save();
    }

    public function markArchived(): bool
    {
        return $this->fill([
            'status' => WebsiteStatus::ARCHIVED,
        ])->save();
    }
}
