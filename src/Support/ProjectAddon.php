<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Support;

use YezzMedia\UserProjects\Models\Project;

final class ProjectAddon
{
    /**
     * @param  Closure(Project): string  $urlGenerator
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $icon,
        public readonly string $description,
        public readonly \Closure $urlGenerator,
        public readonly int $sort = 100,
    ) {}

    public function urlFor(Project $project): string
    {
        return ($this->urlGenerator)($project);
    }
}
