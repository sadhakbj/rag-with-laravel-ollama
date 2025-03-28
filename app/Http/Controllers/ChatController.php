<?php

namespace App\Http\Controllers;

use App\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(private readonly OllamaService $ollamaService) {}

    public function index(): Response
    {
        return Inertia::render('chat/Index');
    }

    /**
     * @throws \Exception
     */
    public function chat(Request $request)
    {
        $text = $request->input('query', '...');
        $embedding = $this->ollamaService->getEmbedding($text);
        $embeddingVector = implode(',', $embedding); // Convert array to a string

        $query = "
        SELECT content
        FROM laravel_docs
        ORDER BY embedding <=> '[$embeddingVector]'
        LIMIT 2
        ";

        $results = DB::select($query);
        $content = implode(' ', array_map(fn ($result) => $result->content, $results)) ?? "I don't have information on that."; // Aggregate content

        return response()->stream(function () use ($text, $content) {
            $generator = $this->ollamaService->streamGenerate($text, $content);
            foreach ($generator as $chunk) {
                echo "data: {$chunk}\n\n";
                ob_flush();
                flush();
            }
            echo "data: </stream>\n\n";
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
