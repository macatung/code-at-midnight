<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'playlist')) {
                $table->string('playlist', 100)->nullable()->index()->after('youtube_url');
            }
            if (!Schema::hasColumn('articles', 'episode_number')) {
                $table->unsignedInteger('episode_number')->nullable()->index()->after('playlist');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'episode_number')) {
                $table->dropColumn('episode_number');
            }
            if (Schema::hasColumn('articles', 'playlist')) {
                $table->dropColumn('playlist');
            }
        });
    }
};
