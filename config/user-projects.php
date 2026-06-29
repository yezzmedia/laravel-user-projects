<?php

declare(strict_types=1);

return [

    'panel' => [
        'id' => 'projects',
        'path' => 'projects',
        'guard' => 'web',
        'theme' => 'resources/css/filament/user/theme.css',
        'back_url' => null,
    ],

    'projects' => [
        'display_limit' => 25,
        'statuses' => [
            'active' => 'Active',
            'archived' => 'Archived',
        ],
    ],

    'members' => [
        'roles' => [
            'owner' => 'Owner',
            'admin' => 'Admin',
            'member' => 'Member',
        ],
        'role_weights' => [
            'owner' => 100,
            'admin' => 50,
            'member' => 10,
        ],
    ],

    'permissions' => [
        'create_project',
        'edit_project',
        'delete_project',
        'invite_members',
        'remove_members',
        'manage_roles',
        'view_stats',
        'manage_settings',
    ],

    'default_role_permissions' => [
        'owner' => [
            'create_project',
            'edit_project',
            'delete_project',
            'invite_members',
            'remove_members',
            'manage_roles',
            'view_stats',
            'manage_settings',
        ],
        'admin' => [
            'edit_project',
            'invite_members',
            'remove_members',
            'view_stats',
        ],
        'member' => [],
    ],

];
