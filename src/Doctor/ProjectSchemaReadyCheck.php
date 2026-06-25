<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Doctor;

use Throwable;
use YezzMedia\Foundation\Data\DoctorResult;
use YezzMedia\Foundation\Doctor\DoctorCheck;
use YezzMedia\UserProjects\Support\ProjectStoreSetup;

final readonly class ProjectSchemaReadyCheck implements DoctorCheck
{
    private const KEY = 'user_projects_schema_ready';

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
            $ready = $this->setup->storeReady();
        } catch (Throwable $exception) {
            return $this->result(
                status: 'failed',
                message: 'The user projects schema could not be inspected.',
                isBlocking: true,
                context: [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ],
            );
        }

        if ($ready) {
            return $this->result(
                status: 'passed',
                message: 'Project tables are present.',
                isBlocking: false,
            );
        }

        return $this->result(
            status: 'failed',
            message: 'User projects schema is not ready. Run `php artisan migrate` or `php artisan website:install --migrate`.',
            isBlocking: true,
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
