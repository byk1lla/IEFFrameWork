<?php
/**
 * PostCategory — blog kategorisi.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class PostCategory extends Model
{
    protected static string $table = 'post_categories';

    protected static array $fillable = ['name', 'slug', 'description'];
}
