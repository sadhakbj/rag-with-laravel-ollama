<?php

namespace App;

use Exception;
use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OllamaService
{
    public const string MODEL = 'llama3.2:latest';
    public const string EMBEDDING_MODEL = 'nomic-embed-text';

    public const string BASE_URL = 'http://localhost:11434/api';

    /**
     * @throws Exception
     */
    public function getEmbedding(string $text): array
    {
        $response = Http::post(self::BASE_URL . '/embeddings', [
            'model' => self::EMBEDDING_MODEL,
            'prompt' => $text,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to get embeddings: '.$response->body());
        }

        return $response->json('embedding');
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function streamGenerate(string $question, string $context): Generator
    {
        $response = Http::withOptions(['stream' => true])
            ->post( self::BASE_URL . '/generate', [
                'model' => self::MODEL,
                'prompt' => "Based on the provided context / documentation fed to you, can you please answer questions. Please keep it short, simple and concise. Question: $question \n Context: $context",
            ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch stream');
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
