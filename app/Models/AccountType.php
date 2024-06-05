<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'delete' => 'boolean',
        'minimum_deposit' => 'decimal:2',
    ];

    public function metaGroup()
    {
        return $this->belongsTo(Group::class, 'group', 'id');
    }
}
