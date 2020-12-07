<?php

namespace App\Models;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use CastsEnums;

    protected $fillable = [
        'public_administration_id',
        'client_name',
        'consumer_id',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string the DB column name to use for route binding
     */
    public function getRouteKeyName(): string
    {
        return 'consumer_id';
    }

    public function getKeyFromConsumerId(string $consumerID): key
    {
        return static::where('consumer_id', $consumerID)->first();
    }

    public function publicAdministration()
    {
        return $this->belongsTo(PublicAdministration::class);
    }
}
