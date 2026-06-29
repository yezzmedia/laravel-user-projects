<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class ProjectInvitation extends Model
{
    protected $table = 'project_invitations';

    protected $fillable = [
        'project_id',
        'email',
        'role',
        'token',
        'invited_by_user_id',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'invited_by_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }

    public static function generateToken(): string
    {
        return Str::random(48);
    }
}
