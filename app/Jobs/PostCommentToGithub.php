<?php

namespace App\Jobs;

use App\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostCommentToGithub implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public string $owner,
        public string $repo,
        public int $prNumber,
        public string $filePath,
        public string $commitId,
        public string $diff,
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Executing job for file: {$this->filePath}");

        $prompt = <<<EOD
You are PHP Laravel expert developer who knows modern PHP, how to write clean code, and follow best practices.
Review the following Laravel code changes from the provided patch diff.
Focus only on the modified lines and their immediate context.
Ignore unchanged parts unless there's a critical issue.
Please start with following text in bold: AI Generated review, followed by empty line and your reviews.

Patch Diff:
$this->diff
EOD;

        $ollamaService = app(OllamaService::class);

        $feedback = $ollamaService->generate($prompt);

        $url = "https://api.github.com/repos/{$this->owner}/{$this->repo}/pulls/{$this->prNumber}/comments";
        $body = [
            'body' => $feedback,
            'path' => $this->filePath,
            'position' => 1, // Adjust the position as needed
            'commit_id' => $this->commitId,
        ];

        Log::info("Posting comment to GitHub for file: {$this->filePath}");

        Http::withToken(env('GITHUB_PERSONAL_ACCESS_TOKEN'))
            ->post($url, $body);
    }
}
