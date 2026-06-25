<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_roles', static function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        DB::table('project_roles')->insert([
            [
                'name' => 'owner',
                'label' => 'Owner',
                'permissions' => json_encode([
                    'create_project',
                    'edit_project',
                    'delete_project',
                    'invite_members',
                    'remove_members',
                    'manage_roles',
                    'view_stats',
                    'manage_settings',
                ]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'label' => 'Admin',
                'permissions' => json_encode([
                    'edit_project',
                    'invite_members',
                    'remove_members',
                    'view_stats',
                ]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'member',
                'label' => 'Member',
                'permissions' => json_encode([]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('project_roles');
    }
};
