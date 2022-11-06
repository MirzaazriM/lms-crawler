<?php

namespace App\Console\Commands;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Observers\CustomCrawlerObserver;
use Illuminate\Support\Facades\Log;
use Spatie\Crawler\Crawler;
use App\Http\Controllers\Controller;
use GuzzleHttp\RequestOptions;

class CrawlLMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl website by given URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $url = $this->ask('Enter URL starting point (with http/s): ');
        $username = $this->ask('Enter username for authentication: ');
        $password = $this->ask('Enter password for authentication: ');

        $client = new \GuzzleHttp\Client(['cookies' => true]);
        $client->request('POST', $url, [
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ],
            ]
        );

        $cookieJar = $client->getConfig('cookies');
        $cookieJar->toArray();

        Crawler::create([RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30, 'cookies' => $cookieJar])
           // ->acceptNofollowLinks()
            //->executeJavaScript()
            ->ignoreRobots()
            // ->setParseableMimeTypes(['text/html', 'text/plain'])
            ->setCrawlObserver(new CustomCrawlerObserver())
            ->setCrawlProfile(new \Spatie\Crawler\CrawlInternalUrls($url))
            ->setMaximumResponseSize(2048 * 1024 * 2) // 4 MB maximum
            ->setMaximumCrawlCount(10000)
            // ->setConcurrency(1) // all urls will be crawled one by one
            ->setDelayBetweenRequests(50)
            ->startCrawling($url);

        return true;
    }
}
