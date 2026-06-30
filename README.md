<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/yezzmedia/.github/main/profile/yezzmedia-dark.svg">
    <img src="https://raw.githubusercontent.com/yezzmedia/.github/main/profile/yezzmedia-light.svg" alt="Yezz Media" height="40">
  </picture>
</p>

<p align="center">
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-projects"><img src="https://img.shields.io/packagist/v/yezzmedia/laravel-user-projects?style=flat-square" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-projects"><img src="https://img.shields.io/packagist/php-v/yezzmedia/laravel-user-projects?style=flat-square" alt="PHP Version"></a>
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-projects"><img src="https://img.shields.io/packagist/l/yezzmedia/laravel-user-projects?style=flat-square" alt="License"></a>
</p>

---

# Laravel User &middot; Projects

`yezzmedia/laravel-user-projects` provides a customer-facing project hub with member management, role-based permissions, activity logging, and project statistics for the Yezz Media platform.

It integrates with the dashboard navigation, hub extensions, and `laravel-access` for authorization.

## Version

Current release: `0.2.0`

## Requirements

- PHP `^8.5`
- Laravel `^13.0` components
- `spatie/laravel-package-tools ^1.93`
- `yezzmedia/laravel-foundation ^0.2`
- `yezzmedia/laravel-access ^0.2`
- `yezzmedia/laravel-dashboard ^0.2`

## Installation

```bash
composer require yezzmedia/laravel-user-projects
```

## What The Package Provides

### Project Hub

A multi-tenancy project overview page (`/projects`) with:

- Project listing with status indicators and member counts
- Archive filter for inactive projects
- Responsive card layout

### Project Detail

Per-project detail pages with:

- Tabbed interface (Overview, Members, Settings)
- Member role management with role-based access control
- Activity log tracking
- Project status and metadata management

### Dashboard Widget

`ProjectsDashboardWidget` surfaces recent projects on the dashboard hub page with:

- Section card with left-border color indicator
- Hover-row project items
- Empty state when no projects exist

### Project Statistics

`ProjectStatsService` provides cached project statistics with:

- Total project count per user
- Active, archived, and member-based breakdowns
- Cache-safe array serialization

### Activity Logging

Per-project activity tracking via `ProjectActivity` model with typed events and timestamps.

### Install Steps

Foundation-aligned install steps publish project migrations and ensure the project schema is ready.

## Development

```bash
composer test
composer analyse
composer format
```

## License

MIT
