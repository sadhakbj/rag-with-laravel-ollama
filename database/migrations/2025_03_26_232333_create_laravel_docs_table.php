<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const string TABLE_NAME = 'laravel_docs';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });

        DB::statement(sprintf("ALTER TABLE %s ADD COLUMN embedding vector(768)", self::TABLE_NAME));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
