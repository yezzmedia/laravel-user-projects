<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProjectAddonActivation extends Model
{
    protected $table = 'project_addon_activations';

    protected $fillable = [
        'project_id',
        'addon_key',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
