<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'public_administration_id',
        'url',
        'type',
        'analytics_id',
        'slug',
        'status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
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
     * @return \Illuminate\Database\Eloquent\Relations\Relation.
     */
    public function publicAdministration()
    {
        return $this->belongsTo(PublicAdministration::class);
    }

    /**
     * Get total visits for this Website via AnalyticsService
     *
     * return int
     */
    public function getTotalVisits()
    {
        if (!$this->analytics_id) {
            return null;
        }
        return (int)app()->make('analytics-service')
                    ->getSiteTotalVisits($this->analytics_id, $this->created_at->format('Y-m-d'));
    }

    /**
     * Get last month visits for this Website via AnalyticsService
     *
     * return int
     */
    public function getLastMonthVisits()
    {
        if (!$this->analytics_id) {
            return null;
        }
        return (int)app()->make('analytics-service')
            ->getSiteLastMonthVisits($this->analytics_id);
    }
}