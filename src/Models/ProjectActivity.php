<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProjectActivity extends Model
{
    public $timestamps = false;

    protected $table = 'project_activities';

    protected $fillable = [
        'project_id',
        'user_id',
        'type',
        'description',
        'properties',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public static function log(int $projectId, string $type, string $description, ?int $userId = null, ?array $properties = null): self
    {
        return self::query()->create([
            'project_id' => $projectId,
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
            'created_at' => now(),
        ]);
    }
}
