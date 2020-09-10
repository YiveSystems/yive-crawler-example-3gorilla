<?php

class Crawler3gorilla
{
    public $parentUrl;
    public $products = [];

    function __construct($parentUrl)
    {
        $this->parentUrl = $parentUrl;
    }

    function extract()
    {
        $body = $this->get($this->parentUrl);
        $html = str_get_html($body);

        foreach ($html->find('.product-item-link') as $a) {

            $productUrl = trim($a->href);

            $product = [
                'images' => [],
                'link' => $productUrl,
                'title' => html_entity_decode(trim($a->plaintext)),
            ];

            $productBody = $this->get($productUrl);

            preg_match("/\"data\": (?<json>.+)}]/m", $productBody, $output_array);

            $imagesArray = json_decode($output_array['json'].'}]', true);

            $images = array_column($imagesArray, 'full');

            $description = str_get_html($productBody)->find('.description', 0)->plaintext;
            $description = str_replace("&nbsp;", ' ', $description);
            $description = html_entity_decode($description);

            $lines = array_values(array_filter(explode(PHP_EOL, $description)));

            $lines = array_map(function ($line) {
                return trim(preg_replace('/\s\s*/', ' ', $line));
            }, $lines);

            $lines = array_filter($lines);

            $product['images'] = $images;
            $product['lines'] = $lines;

            $this->products[] = $product;
        }
    }

    function getProducts()
    {
        return $this->products;
    }

    function get($url)
    {
        $cacheName = 'cache/'.basename($url);

        if (!file_exists($cacheName)) {
            $body = file_get_contents($url);
            file_put_contents($cacheName, $body);
        } else {
            $body = file_get_contents($cacheName);
        }

        return $body;
    }
}
