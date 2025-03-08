<?php

namespace App\Services;

use App\Jobs\PostCommentToGithub;
use App\OllamaService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $files = $this->fetchPRDiff(owner: $owner, repo: $repo, prNumber: $prNumber);

        Log::info('total files: '.count($files));

        foreach ($files as $file) {
            Log::info("Processing file: {$file['filename']}");
            $this->reviewFile($file, $owner, $repo, $prNumber);
        }
    }

    private function reviewFile(array $file, string $owner, string $repo, int $prNumber)
    {
        $filePath = $file['filename'];
        $diff = $file['patch'];

        $contentsUrl = $file['contents_url'];
        $commitId = explode('=', explode('?', $contentsUrl)[1])[1];

        Log::info("Dispatching job for file: {$filePath}");
        // Dispatch the job to post the comment to GitHub
        PostCommentToGithub::dispatch($owner, $repo, $prNumber, $filePath, $commitId, $diff);
    }

    public function sandbox($diff, $commitId)
    {

        $prompt = <<<EOD
Review the following Laravel code changes from the provided patch diff. 
Focus only on the modified lines and their immediate context. 
Ignore unchanged parts unless there's a critical issue. 
Provide concise, actionable feedback on security, performance, best practices, 
and Laravel conventions without unnecessary verbosity. 
Emergency issues in the surrounding context can be noted if absolutely necessary.

Patch Diff:
$diff
EOD;

        $feedback = $this->ollamaService->generate($prompt);
        dd($diff, $feedback);
    }
}
