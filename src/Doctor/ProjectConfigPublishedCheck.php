<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Doctor;

use Throwable;
use YezzMedia\Foundation\Data\DoctorResult;
use YezzMedia\Foundation\Doctor\DoctorCheck;
use YezzMedia\UserProjects\Support\ProjectStoreSetup;

final readonly class ProjectConfigPublishedCheck implements DoctorCheck
{
    private const KEY = 'user_projects_config_published';

    private const PACKAGE = 'yezzmedia/laravel-user-projects';

    public function __construct(private ProjectStoreSetup $setup) {}

    public function key(): string
    {
        return self::KEY;
    }

    public function package(): string
    {
        return self::PACKAGE;
    }

    public function run(): DoctorResult
    {
        try {
            $published = $this->setup->configPublished();
        } catch (Throwable $exception) {
            return $this->result(
                status: 'failed',
                message: 'The user projects config could not be inspected.',
                isBlocking: true,
                context: [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ],
            );
        }

        if ($published) {
            return $this->result(
                status: 'passed',
                message: 'User projects config is published at config/user-projects.php.',
                isBlocking: false,
                context: ['config_path' => config_path('user-projects.php')],
            );
        }

        return $this->result(
            status: 'warning',
            message: 'User projects config is not published. Run `php artisan website:install --refresh-publish` to publish it.',
            isBlocking: false,
            context: ['config_path' => config_path('user-projects.php')],
        );
    }

    private function result(string $status, string $message, bool $isBlocking, ?array $context = null): DoctorResult
    {
        return new DoctorResult(
            key: $this->key(),
            package: $this->package(),
            status: $status,
            message: $message,
            isBlocking: $isBlocking,
            context: $context,
        );
    }
}
