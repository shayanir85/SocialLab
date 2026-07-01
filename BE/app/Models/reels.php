<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reels extends Model
{
    protected $guarded = ['id'];
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
    
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_reel', 'reel_id', 'user_id');
    }
        public function getVideoUrlAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
