<?php

use Muhammadmahedihasan\PhpScraper\Scrapers\Scrape;

require __DIR__ . '/vendor/autoload.php';

try {
    (new Scrape())->handle();
} catch (Exception $e) {
    echo $e->getMessage();
}