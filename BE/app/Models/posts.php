<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Posts extends Model
{
    protected $guarded = ['id'];
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_post', 'post_id', 'user_id')
                    ->withTimestamps();
    }
    public function likes(): BelongsToMany
    {
        return $this->likedByUsers();
    }
}
