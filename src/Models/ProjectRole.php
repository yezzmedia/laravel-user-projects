<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectRole extends Model
{
    protected $table = 'project_roles';

    protected $fillable = [
        'name',
        'label',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }
}
