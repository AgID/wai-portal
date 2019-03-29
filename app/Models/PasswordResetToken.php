<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetToken extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The primary key.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'created_at',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'token';
    }

    /**
     * The owner if this password reset token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
