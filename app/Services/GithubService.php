<?php

namespace App\Services;

use App\OllamaService;
use Illuminate\Support\Facades\Http;

class GithubService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private readonly OllamaService $ollamaService)
    {
        //
    }

    public function fetchPRDiff($owner, $repo, $prNumber)
    {
        $url = "https://api.github.com/repos/sadhakbj/rag-with-laravel-ollama/pulls/$prNumber/files";
        $response = Http::withToken(env('GITHUB_PERSONAL_ACCESS_TOKEN'))
            ->get($url);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function processPR(string $owner, string $repo, int $prNumber)
    {
        $prData = $this->fetchPRDiff(owner: $owner, repo: $repo, prNumber: $prNumber);

        $patches = array_column($prData, 'patch');
        $patchesContent = implode("\n", $patches);

        $guideline = "You are expert PHP developer, you should know better than this. So review this PR contents and give me feedback. Content: \n\n $patchesContent";

        return $this->ollamaService->streamGenerate($guideline, $patchesContent);
    }
}
