<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects;

use YezzMedia\Foundation\Contracts\DefinesAuditEvents;
use YezzMedia\Foundation\Contracts\DefinesInstallSteps;
use YezzMedia\Foundation\Contracts\DefinesPermissions;
use YezzMedia\Foundation\Contracts\DefinesRateLimiters;
use YezzMedia\Foundation\Contracts\PlatformPackage;
use YezzMedia\Foundation\Contracts\ProvidesDoctorChecks;
use YezzMedia\Foundation\Contracts\RegistersFeatures;
use YezzMedia\Foundation\Data\AuditEventDefinition;
use YezzMedia\Foundation\Data\FeatureDefinition;
use YezzMedia\Foundation\Data\PackageMetadata;
use YezzMedia\Foundation\Data\PermissionDefinition;
use YezzMedia\Foundation\Data\RateLimitDefinition;
use YezzMedia\UserProjects\Doctor\ProjectConfigPublishedCheck;
use YezzMedia\UserProjects\Doctor\ProjectSchemaReadyCheck;
use YezzMedia\UserProjects\Install\EnsureProjectSchemaReadyInstallStep;
use YezzMedia\UserProjects\Install\PublishProjectConfigInstallStep;

final class UserProjectsPlatformPackage implements DefinesAuditEvents, DefinesInstallSteps, DefinesPermissions, DefinesRateLimiters, PlatformPackage, ProvidesDoctorChecks, RegistersFeatures
{
    public function metadata(): PackageMetadata
    {
        return new PackageMetadata(
            name: 'yezzmedia/laravel-user-projects',
            vendor: 'yezzmedia',
            description: 'Customer project ownership and SaaS workspace boundary for the Yezz Media Laravel website platform.',
            packageClass: self::class,
        );
    }

    public function permissionDefinitions(): array
    {
        return [
            new PermissionDefinition(
                name: 'user-projects.manage',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Manage user projects',
                description: 'Allows operator-facing oversight and management access to user projects.',
            ),
            new PermissionDefinition(
                name: 'user-projects.roles.manage',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Manage project roles',
                description: 'Allows creating, editing, and deleting project roles and their permissions.',
            ),
        ];
    }

    public function featureDefinitions(): array
    {
        return [
            new FeatureDefinition(
                name: 'user-projects.overview',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Project overview page',
                description: 'Provides the project grid, detail view, and quick actions.',
            ),
            new FeatureDefinition(
                name: 'user-projects.create',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Project creation page',
                description: 'Form to create a new user project.',
            ),
            new FeatureDefinition(
                name: 'user-projects.stats',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Project statistics page',
                description: 'Provides global analytics and insights across all projects.',
            ),
            new FeatureDefinition(
                name: 'user-projects.members',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Project members page',
                description: 'Provides member management for each project.',
            ),
            new FeatureDefinition(
                name: 'user-projects.settings',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Project settings page',
                description: 'Provides global project configuration and project-level settings.',
            ),
            new FeatureDefinition(
                name: 'user-projects.permissions',
                package: 'yezzmedia/laravel-user-projects',
                label: 'Role permissions page',
                description: 'Provides role CRUD and permission assignment.',
            ),
        ];
    }

    public function auditEventDefinitions(): array
    {
        return [
            new AuditEventDefinition(
                key: 'user-projects.project.created',
                package: 'yezzmedia/laravel-user-projects',
                action: 'created',
                subjectType: 'user_project',
                description: 'A user created a new project.',
                severity: 'info',
                contextKeys: ['project_id', 'project_name', 'owner_id'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.project.updated',
                package: 'yezzmedia/laravel-user-projects',
                action: 'updated',
                subjectType: 'user_project',
                description: 'A user updated a project.',
                severity: 'info',
                contextKeys: ['project_id', 'changed_fields'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.project.deleted',
                package: 'yezzmedia/laravel-user-projects',
                action: 'deleted',
                subjectType: 'user_project',
                description: 'A user deleted a project.',
                severity: 'warning',
                contextKeys: ['project_id', 'project_name'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.member.invited',
                package: 'yezzmedia/laravel-user-projects',
                action: 'invited',
                subjectType: 'user_project_member',
                description: 'A member was invited to a project.',
                severity: 'info',
                contextKeys: ['project_id', 'user_id', 'role'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.member.removed',
                package: 'yezzmedia/laravel-user-projects',
                action: 'removed',
                subjectType: 'user_project_member',
                description: 'A member was removed from a project.',
                severity: 'warning',
                contextKeys: ['project_id', 'user_id'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.member.role_updated',
                package: 'yezzmedia/laravel-user-projects',
                action: 'updated',
                subjectType: 'user_project_member',
                description: 'A member role was updated.',
                severity: 'info',
                contextKeys: ['project_id', 'user_id', 'new_role'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.role.created',
                package: 'yezzmedia/laravel-user-projects',
                action: 'created',
                subjectType: 'project_role',
                description: 'A new project role was created.',
                severity: 'info',
                contextKeys: ['role_name', 'role_label'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.role.updated',
                package: 'yezzmedia/laravel-user-projects',
                action: 'updated',
                subjectType: 'project_role',
                description: 'A project role was updated.',
                severity: 'info',
                contextKeys: ['role_name', 'changed_fields'],
            ),
            new AuditEventDefinition(
                key: 'user-projects.role.deleted',
                package: 'yezzmedia/laravel-user-projects',
                action: 'deleted',
                subjectType: 'project_role',
                description: 'A project role was deleted.',
                severity: 'warning',
                contextKeys: ['role_name'],
            ),
        ];
    }

    public function rateLimitDefinitions(): array
    {
        return [
            new RateLimitDefinition(
                key: 'user-projects.project.create',
                package: 'yezzmedia/laravel-user-projects',
                description: 'Rate limit for project creation.',
                maxAttempts: 10,
                decaySeconds: 3600,
                scope: 'user',
                keyStrategy: 'user_id',
            ),
            new RateLimitDefinition(
                key: 'user-projects.project.update',
                package: 'yezzmedia/laravel-user-projects',
                description: 'Rate limit for project updates.',
                maxAttempts: 30,
                decaySeconds: 3600,
                scope: 'user',
                keyStrategy: 'user_id',
            ),
            new RateLimitDefinition(
                key: 'user-projects.member.invite',
                package: 'yezzmedia/laravel-user-projects',
                description: 'Rate limit for member invitations.',
                maxAttempts: 20,
                decaySeconds: 3600,
                scope: 'user',
                keyStrategy: 'user_id',
            ),
        ];
    }

    public function installSteps(): array
    {
        return [
            app(PublishProjectConfigInstallStep::class),
            app(EnsureProjectSchemaReadyInstallStep::class),
        ];
    }

    public function doctorChecks(): array
    {
        return [
            app(ProjectConfigPublishedCheck::class),
            app(ProjectSchemaReadyCheck::class),
        ];
    }
}
