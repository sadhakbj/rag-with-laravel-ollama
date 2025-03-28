<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('laravel_docs', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE laravel_docs ADD COLUMN embedding vector(768)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laravel_docs');
    }
};
