<?php

namespace App;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getEmbedding(string $text): array
    {
        $response = Http::post('http://localhost:11434/api/embeddings', [
            'model' => 'nomic-embed-text',
            'prompt' => $text,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get embeddings: '.$response->body());
        }

        return $response->json('embedding'); // Returns an array of floats
    }

    public function generate(string $prompt): string
    {
        $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
            'model' => 'llama3.2',
            'prompt' => $prompt,
            'stream' => false,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to generate text: '.$response->body());
        }

        return $response->json('text');
    }

    public function streamGenerate(string $question, string $context)
    {
        $response = Http::withOptions(['stream' => true])
            ->post('http://localhost:11434/api/generate', [
                'model' => 'llama3.2',
                'prompt' => "Based on the provided context, please answer questions. Context: $context\nQuestion: $question",
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
