<?php

namespace App\Models;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getKeyFromConsumerId(string $consumerID): ?Key
    {
        return Key::where('consumer_id', $consumerID)->first();
    }

    public function publicAdministration(): BelongsTo
    {
        return $this->belongsTo(PublicAdministration::class);
    }
}
