<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    
    protected $fillable = [
        'title',
        'content',
        'author',
        'email',
        'email_verified',
        'verification_code',
        'code_expires_at'
    ];
    
    protected $casts = [
        'email_verified' => 'boolean',
        'code_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $hidden = [
        'verification_code'
    ];
}
