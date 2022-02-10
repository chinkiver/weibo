<?php

namespace App\Models;

/**
 * 微博
 * Class Status
 *
 * @package App\Models
 */
class Status extends Model
{
    /**
     * 所关联的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
