<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Website keyword model.
 */
class Keyword extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array mass assignable attributes
     */
    protected $fillable = [
        'id_vocabulary',
        'vocabulary',
        'name',
        'description',
    ];

    /**
     * The keywords connected to this website or null if none.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany the relation to the websites having this keyword
     *
     * @see \App\Models\Website
     */
    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class);
    }
}
