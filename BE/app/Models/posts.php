<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Posts extends Model
{
    protected $guarded = ['id'];
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
