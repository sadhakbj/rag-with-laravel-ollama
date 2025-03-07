<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GithubService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function fetchPRDiff($owner, $repo, $prNumber)
    {
        $url = 'https://api.github.com/repos/sadhakbj/rag-with-laravel-ollama/pull/1';
        $response = Http::withToken(env('GITHUB_PERSONAL_ACCESS_TOKEN'))
            ->get($url);

        return $response;
    }
}
