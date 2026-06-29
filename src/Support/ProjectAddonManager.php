<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectAddonActivation;

final class ProjectAddonManager
{
    /** @var array<string, ProjectAddon> */
    private array $addons = [];

    public function register(ProjectAddon $addon): void
    {
        $this->addons[$addon->key] = $addon;
    }

    /**
     * @return array<string, ProjectAddon>
     */
    public function all(): array
    {
        $installedKeys = app(InstalledAddonRegistry::class)->keys();

        if ($installedKeys === []) {
            return [];
        }

        $sorted = $this->addons;

        uasort($sorted, fn (ProjectAddon $a, ProjectAddon $b): int => $a->sort <=> $b->sort);

        $result = [];

        foreach ($sorted as $key => $addon) {
            if (in_array($key, $installedKeys, true)) {
                $result[$key] = $addon;
            }
        }

        return $result;
    }

    public function find(string $key): ?ProjectAddon
    {
        return $this->addons[$key] ?? null;
    }

    /**
     * @return array<string, ProjectAddon>
     */
    public function activeForProject(Project $project): array
    {
        $activeKeys = ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->pluck('addon_key')
            ->toArray();

        if ($activeKeys === []) {
            return [];
        }

        $result = [];

        foreach ($this->all() as $key => $addon) {
            if (in_array($key, $activeKeys, true)) {
                $result[$key] = $addon;
            }
        }

        return $result;
    }

    /**
     * @return array<string, bool>
     */
    public function activationStatus(Project $project): array
    {
        $activeKeys = ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->pluck('addon_key')
            ->toArray();

        $status = [];

        foreach ($this->all() as $key => $addon) {
            $status[$key] = in_array($key, $activeKeys, true);
        }

        return $status;
    }

    public function activate(Project $project, string $addonKey, array $settings = []): ProjectAddonActivation
    {
        if ($this->find($addonKey) === null) {
            throw new \InvalidArgumentException("Unknown addon key: {$addonKey}");
        }

        return ProjectAddonActivation::query()->firstOrCreate(
            [
                'project_id' => $project->id,
                'addon_key' => $addonKey,
            ],
            [
                'settings' => $settings,
            ],
        );
    }

    public function deactivate(Project $project, string $addonKey): void
    {
        ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->where('addon_key', $addonKey)
            ->delete();
    }

    public function toggle(Project $project, string $addonKey, array $settings = []): bool
    {
        if ($this->find($addonKey) === null) {
            throw new \InvalidArgumentException("Unknown addon key: {$addonKey}");
        }

        $activation = ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->where('addon_key', $addonKey)
            ->first();

        if ($activation !== null) {
            $activation->delete();

            return false;
        }

        ProjectAddonActivation::query()->create([
            'project_id' => $project->id,
            'addon_key' => $addonKey,
            'settings' => $settings,
        ]);

        return true;
    }

    public function settings(Project $project, string $addonKey): ?Collection
    {
        $activation = ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->where('addon_key', $addonKey)
            ->first();

        return $activation?->settings !== null ? collect($activation->settings) : null;
    }

    public function setSettings(Project $project, string $addonKey, array $settings): void
    {
        ProjectAddonActivation::query()
            ->where('project_id', $project->id)
            ->where('addon_key', $addonKey)
            ->update(['settings' => $settings]);
    }
}
