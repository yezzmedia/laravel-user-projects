<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

final class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'status',
        'photo_path',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        return Storage::disk('public')->url($this->photo_path);
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
