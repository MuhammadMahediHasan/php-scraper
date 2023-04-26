<?php

namespace Muhammadmahedihasan\PhpScraper\Scrapers;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Scrape
{
    /**
     * @throws GuzzleException
     */
    public function handle(): string
    {
        $data = $this->scrapeData();
        $this->saveToJson($data);

        return 'Saved successful';
    }

    /**
     * @throws GuzzleException
     */
    public function scrapeData(): array
    {
        $url = 'https://www.wafilife.com/cat/books/';
        $httpClient = new Client();
        $response = $httpClient->get($url);

        $htmlString = (string)$response->getBody();

        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);

        $xpath = new DOMXPath($doc);

        $titles = $xpath->evaluate('//div[@class="product_item_wrapper"]//div[@class="product-meta-wrapper"]//h3/a');
        $author = $xpath->evaluate('//div[@class="product_item_wrapper"]//div[@class="product-meta-wrapper"]//div[@class="wd_product_categories"]');
        $prices = $xpath->evaluate('//div[@class="product_item_wrapper"]//div[@class="product-meta-wrapper"]//span[@class="price"]');
        $images = $xpath->evaluate('//div[@class="product_item_wrapper"]//div[@class="product_thumbnail_wrapper"]//div[@class="product-image-front"]//img');

        $books = array();
        foreach ($titles as $key => $title) {
            $books[$key]['name'] = $title->textContent;
            $books[$key]['author'] = ($author[$key])->textContent ?? '';

            //price generate
            $price = ($prices[$key])->textContent ?? '';
            $explode = explode("\n", $price);
            $hasDiscount = count($explode) > 0 ? array_values(array_filter($explode, 'strlen')) : false;

            $books[$key]['price'] = $hasDiscount && isset($hasDiscount[1]) ? $hasDiscount[1] : $price;
            $books[$key]['image'] = ($images[$key])->getAttribute('src');
        }

        return $books;
    }

    public function saveToJson($data)
    {
        $data = [
            'saved_search' => [
                'keys' => $data,
            ]
        ];

        $json = json_encode($data);
        dump(file_exists(__DIR__ . '/../../raw_saved_search.json'));
        file_put_contents(__DIR__ . '/../../raw_saved_search.json', $json);
    }
}