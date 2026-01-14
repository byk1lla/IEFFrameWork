<?php

namespace App\Models;

use App\Core\Model;

class Task extends Model
{
    protected static string $table = 'tasks';
    protected static array $fillable = ['title', 'is_completed'];
    protected static bool $useUuid = true;
}
