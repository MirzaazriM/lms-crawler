## How to use?

1. In LMS project disable VerifyCsrfToken middleware at app/Http/Kernel.php
2. In LMS disable "Accept cookie required"
2. In this project run command: php artisan crawl
3. Enter URL starting point for crawling, and username and password to authenticate with the service and receive session token which will be used by crawler

Successfully crawled URLs will be stored at /storage/logs/laravel.log.
Unsuccessfully crawled URLs will be stored at /storage/logs/crawler-fails.log.
