<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Project extends Model
{
    use HasUuids;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function isOwner(mixed $user): bool
    {
        return $user !== null && (int) $user->getAuthIdentifier() === (int) $this->owner_id;
    }

    public function memberRole(mixed $user): ?string
    {
        if ($user === null) {
            return null;
        }

        $member = $this->members->firstWhere('user_id', (int) $user->getAuthIdentifier());

        return $member?->role;
    }
}
