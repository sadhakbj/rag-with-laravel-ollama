<?php

namespace App;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    public const string MODEL = 'llama3.2:latest';
    public const string EMBEDDING_MODEL = 'nomic-embed-text';

    /**¢
     * Create a new class instance.
     */
    public function __construct() {}

    public function getEmbedding(string $text): array
    {
        $response = Http::post('http://localhost:11434/api/embeddings', [
            'model' => self::EMBEDDING_MODEL,
            'prompt' => $text,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get embeddings: '.$response->body());
        }

        return $response->json('embedding');
    }

    public function streamGenerate(string $question, string $context)
    {
        $response = Http::withOptions(['stream' => true])
            ->post('http://localhost:11434/api/generate', [
                'model' => self::MODEL,
                'prompt' => "Based on the provided context, please answer questions. Question: $question \n Context: $context",
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch stream');
        }

        $buffer = '';
        $body = $response->getBody();
        while (! $body->eof()) {
            $buffer .= $body->read(1024); // Buffer chunks of response
            while (($pos = strpos($buffer, "\n")) !== false) { // Process complete lines
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                if (trim($line)) {
                    yield $line;
                }
            }
        }
    }
}
