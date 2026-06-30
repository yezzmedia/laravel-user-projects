# Changelog

All notable changes to `yezzmedia/laravel-user-projects` will be documented in this file.

The format is based on Keep a Changelog and this package follows Semantic Versioning.

## [Unreleased]

## [0.2.0] - 2026-06-30

### Added

- `ProjectsDashboardWidget` for the dashboard hub page with section card design and hover rows
- Permission guard integration for project access control
- Activity filter for project listing
- Pagination support on project overview
- Stats caching with `ProjectStatsService`
- Loading states for project pages
- Archive filter for project overview

### Fixed

- `ProjectStatsService` now caches plain arrays instead of Collection objects, fixing `__PHP_Incomplete_Class` errors under Laravel 13 cache serialization defaults
- Corrected migration publish tag from `laravel-user-projects-migrations` to `user-projects-migrations`
- Restored `activities()` relationship on Project model after merge

### Changed

- Redesigned `projects-dashboard.blade.php` widget to match account overview design language
- Removed `HasUuids` from Project model, aligned migrations with auto-increment IDs
- Simplified `detail.blade.php` from 578 to 176 lines with tabbed interface redesign
- Fixed translation key `no_projects_yet` to `no_projects`
- Bumped minimum `yezzmedia/laravel-foundation` dependency from `*@dev` to `^0.2`
- Bumped minimum `yezzmedia/laravel-access` dependency from `*@dev` to `^0.2`
- Bumped minimum `yezzmedia/laravel-dashboard` dependency from `*@dev` to `^0.2`

## [0.1.0] - 2026-03-31

### Added

- Project hub with multi-tenancy project management
- Member management with role-based permissions
- Activity log tracking per project
- Foundation-aligned install steps
- Dashboard navigation integration
