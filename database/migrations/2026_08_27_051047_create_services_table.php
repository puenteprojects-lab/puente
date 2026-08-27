<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Stable identifier for a service, independent of any language.
            $table->string('slug')->unique();

            // Keyed by locale: {"en": {"title": "...", "text": "..."}, ...}.
            // Kept in one column because a service is always edited as a whole
            // and never queried by its translated text.
            $table->json('translations');

            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
