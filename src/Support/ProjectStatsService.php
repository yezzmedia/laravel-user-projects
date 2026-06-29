<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectMember;

final readonly class ProjectStatsService
{
    private const int DEFAULT_TTL = 3600;

    private function cacheKey(string $method, mixed ...$args): string
    {
        return 'user-projects.stats.'.$method.(! empty($args) ? '.'.md5(serialize($args)) : '');
    }

    private function ttl(): int
    {
        return (int) config('user-projects.stats.cache_ttl', self::DEFAULT_TTL);
    }

    public function totalProjects(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()->count());
    }

    public function totalMembers(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => ProjectMember::query()->count());
    }

    public function activeProjects(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()->where('status', 'active')->count());
    }

    public function archivedProjects(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()->where('status', 'archived')->count());
    }

    public function averageMembersPerProject(): float
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), function (): float {
            $projectCount = $this->totalProjects();

            if ($projectCount === 0) {
                return 0.0;
            }

            return round($this->totalMembers() / $projectCount, 1);
        });
    }

    public function projectsByStatus(): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): Collection => Project::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status => $row->count]));
    }

    public function projectsCreatedPerMonth(int $months = 12): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__, $months), $this->ttl(), function () use ($months): Collection {
            $connection = DB::connection()->getDriverName();

            $dateExpr = match ($connection) {
                'sqlite' => "strftime('%Y-%m', created_at)",
                'pgsql' => "to_char(created_at, 'YYYY-MM')",
                default => "DATE_FORMAT(created_at, '%Y-%m')",
            };

            return Project::query()
                ->selectRaw("{$dateExpr} as month, COUNT(*) as count")
                ->where('created_at', '>=', now()->subMonths($months))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(fn ($row) => [$row->month => $row->count]);
        });
    }

    public function memberRoleDistribution(): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): Collection => ProjectMember::query()
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('role')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->role => $row->count]));
    }

    public function topProjectsByMembers(int $limit = 5): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__, $limit), $this->ttl(), fn (): Collection => Project::query()
            ->withCount('members')
            ->orderByDesc('members_count')
            ->limit($limit)
            ->get(['id', 'name', 'status']));
    }

    public function projectsByOwner(): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): Collection => Project::query()
            ->selectRaw('owner_id, COUNT(*) as project_count')
            ->groupBy('owner_id')
            ->orderByDesc('project_count')
            ->with('owner')
            ->get()
            ->map(fn ($row) => [
                'user' => $row->owner?->name ?? $row->owner?->email ?? "User #{$row->owner_id}",
                'count' => $row->project_count,
            ]));
    }

    public function latestProjects(int $limit = 5): Collection
    {
        return Cache::remember($this->cacheKey(__FUNCTION__, $limit), $this->ttl(), fn (): Collection => Project::query()
            ->with('owner')
            ->withCount('members')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get());
    }

    public function newlyCreatedToday(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()
            ->whereDate('created_at', today())
            ->count());
    }

    public function newlyCreatedThisWeek(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()
            ->where('created_at', '>=', now()->startOfWeek())
            ->count());
    }

    public function newlyCreatedThisMonth(): int
    {
        return Cache::remember($this->cacheKey(__FUNCTION__), $this->ttl(), fn (): int => Project::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count());
    }

    public function dashboard(): array
    {
        return [
            'total_projects' => $this->totalProjects(),
            'active_projects' => $this->activeProjects(),
            'archived_projects' => $this->archivedProjects(),
            'total_members' => $this->totalMembers(),
            'avg_members_per_project' => $this->averageMembersPerProject(),
            'created_today' => $this->newlyCreatedToday(),
            'created_this_week' => $this->newlyCreatedThisWeek(),
            'created_this_month' => $this->newlyCreatedThisMonth(),
            'status_distribution' => $this->projectsByStatus(),
            'monthly_creation' => $this->projectsCreatedPerMonth(),
            'role_distribution' => $this->memberRoleDistribution(),
            'top_projects' => $this->topProjectsByMembers(),
            'top_owners' => $this->projectsByOwner(),
            'latest_projects' => $this->latestProjects(),
        ];
    }
}
