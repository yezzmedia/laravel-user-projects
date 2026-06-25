<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use Illuminate\Support\Str;
use ReflectionClass;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Pages\ProjectsCreatePage;
use YezzMedia\UserProjects\Pages\ProjectsOverviewPage;
use YezzMedia\UserProjects\Pages\ProjectsPermissionsPage;
use YezzMedia\UserProjects\Pages\ProjectsSettingsPage;
use YezzMedia\UserProjects\Pages\ProjectsStatsPage;

final class ProjectNavigationManager
{
    /**
     * @return list<class-string>
     */
    private function resolvePanelPages(): array
    {
        $panel = null;

        try {
            $panel = filament()->getPanel('projects');
        } catch (\Throwable) {
            // Panel might not exist in test environments
        }

        if ($panel === null) {
            try {
                $panel = filament()->getCurrentOrDefaultPanel();
            } catch (\Throwable) {
                $panel = null;
            }
        }

        if ($panel === null) {
            return [
                ProjectsOverviewPage::class,
                ProjectsCreatePage::class,
                ProjectsStatsPage::class,
                ProjectsSettingsPage::class,
                ProjectsPermissionsPage::class,
            ];
        }

        return $panel->getPages();
    }

    /**
     * @return array<string, array{label: string, items: list<array{key: string, label: string, icon: string, route: string, sort: int}>}>
     */
    public function indexNavigation(): array
    {
        $prefix = '/'.trim((string) config('user-projects.panel.path', 'hub'), '/').'/projects';
        $pageClasses = $this->resolvePanelPages();
        $groups = [];

        foreach ($pageClasses as $pageClass) {
            $ref = new ReflectionClass($pageClass);

            if (! $ref->getStaticPropertyValue('shouldRegisterNavigation', true)) {
                continue;
            }

            $group = (string) ($ref->getStaticPropertyValue('navigationGroup', 'Projects') ?? 'Projects');
            $label = (string) ($ref->getStaticPropertyValue('navigationLabel', '') ?? '');
            $icon = (string) ($ref->getStaticPropertyValue('navigationIcon', 'grid') ?? 'grid');
            $sort = (int) ($ref->getStaticPropertyValue('navigationSort', 100) ?? 100);
            $slug = $ref->getStaticPropertyValue('slug');

            if ($slug === null) {
                $route = $prefix.'/'.Str::kebab(class_basename($pageClass));
            } else {
                $slug = (string) $slug;
                $route = $slug === '' ? $prefix : $prefix.'/'.ltrim($slug, '/');
            }

            $groups[$group][] = [
                'key' => $slug,
                'label' => $label ?: Str::headline(class_basename($pageClass)),
                'icon' => $icon,
                'route' => $route,
                'sort' => $sort,
            ];
        }

        foreach ($groups as &$items) {
            usort($items, static fn (array $a, array $b): int => $a['sort'] <=> $b['sort']);
        }

        $order = ['Projects', 'Other'];
        $sorted = [];
        foreach ($order as $key) {
            if (isset($groups[$key])) {
                $sorted[$key] = [
                    'label' => $key,
                    'items' => $groups[$key],
                ];
            }
        }

        return $sorted;
    }

    /**
     * @return array<string, array{label: string, items: list<array{key: string, label: string, icon: string, route: string, sort: int}>}>
     */
    public function projectNavigation(Project $project): array
    {
        $prefix = '/'.trim((string) config('user-projects.panel.path', 'hub'), '/').'/projects/'.$project->getRouteKey();
        $pageClasses = $this->resolvePanelPages();
        $groups = [];

        foreach ($pageClasses as $pageClass) {
            $ref = new ReflectionClass($pageClass);

            if (! $ref->getStaticPropertyValue('shouldRegisterNavigation', true)) {
                continue;
            }

            $slug = $ref->getStaticPropertyValue('slug');

            if ($slug === null || $slug === '' || ! str_contains((string) $slug, '{')) {
                continue;
            }

            $group = (string) ($ref->getStaticPropertyValue('navigationGroup', 'Projects') ?? 'Projects');
            $label = (string) ($ref->getStaticPropertyValue('navigationLabel', '') ?? '');
            $icon = (string) ($ref->getStaticPropertyValue('navigationIcon', 'grid') ?? 'grid');
            $sort = (int) ($ref->getStaticPropertyValue('navigationSort', 100) ?? 100);

            $routeSlug = str_replace('{project}', (string) $project->getRouteKey(), (string) $slug);
            $route = $prefix.'/'.ltrim($routeSlug, '/');

            $groups[$group][] = [
                'key' => $slug,
                'label' => $label ?: Str::headline(class_basename($pageClass)),
                'icon' => $icon,
                'route' => $route,
                'sort' => $sort,
            ];
        }

        foreach ($groups as &$items) {
            usort($items, static fn (array $a, array $b): int => $a['sort'] <=> $b['sort']);
        }

        $order = ['Project', 'Other'];
        $sorted = [];
        foreach ($order as $key) {
            if (isset($groups[$key])) {
                $sorted[$key] = [
                    'label' => $key,
                    'items' => $groups[$key],
                ];
            }
        }

        return $sorted;
    }
}
