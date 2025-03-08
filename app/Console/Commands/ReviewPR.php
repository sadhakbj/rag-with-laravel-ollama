<?php

namespace App\Console\Commands;

use App\Services\GithubService;
use Illuminate\Console\Command;

class ReviewPR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:review-pr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = app(GithubService::class);

        $service->processPR('sadhakbj', 'rag-with-laravel-ollama', 2);
        $this->info('Processing PR, please wait couple of seconds.');
    }
}
