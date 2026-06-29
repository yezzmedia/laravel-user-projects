<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_addon_activations', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('addon_key');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'addon_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_addon_activations');
    }
};
