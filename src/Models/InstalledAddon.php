<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;

final class InstalledAddon extends Model
{
    protected $table = 'installed_addons';

    protected $fillable = [
        'addon_key',
        'name',
        'version',
        'description',
    ];
}
