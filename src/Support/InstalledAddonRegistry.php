<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Collection;
use YezzMedia\UserProjects\Models\InstalledAddon;

final class InstalledAddonRegistry
{
    public function register(string $addonKey, string $name, ?string $version = null, ?string $description = null): InstalledAddon
    {
        return InstalledAddon::query()->firstOrCreate(
            ['addon_key' => $addonKey],
            [
                'name' => $name,
                'version' => $version,
                'description' => $description,
            ],
        );
    }

    public function unregister(string $addonKey): void
    {
        InstalledAddon::query()->where('addon_key', $addonKey)->delete();
    }

    public function isInstalled(string $addonKey): bool
    {
        return InstalledAddon::query()->where('addon_key', $addonKey)->exists();
    }

    public function all(): Collection
    {
        return InstalledAddon::query()->orderBy('name')->get();
    }

    public function keys(): array
    {
        return InstalledAddon::query()->pluck('addon_key')->toArray();
    }
}
