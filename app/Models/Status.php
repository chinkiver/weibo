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
    // 可批量编辑的字段
    protected $fillable = ['content'];

    /**
     * 所关联的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
