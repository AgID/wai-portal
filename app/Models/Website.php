<?php

namespace App\Models;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\Website\WebsiteDeleted;
use App\Events\Website\WebsiteRestored;
use App\Events\Website\WebsiteUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Website model.
 */
class Website extends Model
{
    use SoftDeletes;
    use CastsEnums;

    /**
     * Total active websites count cache key.
     *
     * @var string the key
     */
    public const WEBSITE_COUNT_KEY = 'websiteCount';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'type' => 'integer',
    ];

    /**
     * The attributes that should be cast to enums classes.
     *
     * @var array enum casted attributes
     */
    protected $enumCasts = [
        'status' => WebsiteStatus::class,
        'type' => WebsiteType::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array dispatched events list
     */
    protected $dispatchesEvents = [
        'updated' => WebsiteUpdated::class,
        'deleted' => WebsiteDeleted::class,
        'restored' => WebsiteRestored::class,
    ];

    /**
     * Get total active websites counter.
     *
     * @return int the count
     */
    public static function getCount(): int
    {
        return Cache::rememberForever(self::WEBSITE_COUNT_KEY, function () {
            return Website::where('status', WebsiteStatus::ACTIVE)->count();
        });
    }

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
     * Return name and slug of this website in printable format.
     *
     * @return string the printable website representation
     */
    public function getInfoAttribute(): string
    {
        return '"' . $this->name . '" [' . $this->slug . ']';
    }

    /**
     * Return the collection of non adminstrator users enabled for this website.
     *
     * @return Collection the non adminstrator users for whom this website is enabled
     */
    public function getEnabledNonAdministratorUsers(): Collection
    {
        return User::whereIsNot(UserRole::ADMIN)->where('status', '!=', UserStatus::SUSPENDED)->whereHas('abilities', function ($query) {
            $query->where('abilities.entity_id', $this->id);
            $query->where('abilities.name', '!=', UserPermission::NO_ACCESS);
        })->with('abilities')->get();
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
