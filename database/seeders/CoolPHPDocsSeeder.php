<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CoolPHPDocsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        $ollamaService = app(\App\OllamaService::class);

        $directory = base_path('texts'); // Replace with your directory path

        // Read all files in the directory
        $files = File::files($directory);

        // Initialize an array to store file contents
        $texts = [];

        foreach ($files as $file) {
            // Read the content of each file and add it to the array
            $texts[] = File::get($file);
        }

        foreach ($texts as $text) {
            $chunks = $this->chunkText($text, 5000);

            foreach ($chunks as $chunk) {
                $embedding = $ollamaService->getEmbedding($chunk);

                DB::insert('
                INSERT INTO laravel_docs (content, embedding, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ', [$chunk, json_encode($embedding)]);
            }
        }
    }

    /**
     * Chunk the text into smaller pieces.
     */
    private function chunkText(string $text, int $chunkSize, int $overlap = 150): array
    {
        $chunks = [];
        $length = strlen($text);

        for ($i = 0; $i < $length; $i += ($chunkSize - $overlap)) {
            $chunks[] = substr($text, $i, $chunkSize);
        }

        return $chunks;
    }

}
