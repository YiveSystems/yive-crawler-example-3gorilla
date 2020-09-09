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
     
        foreach($html->find('.product-item-link') as $a){

            $productUrl = trim($a->href);

            $product = [
                'images' => [],
                'link' => $productUrl,
                'title' => trim($a->plaintext),
            ];

            $productBody = $this->get($productUrl);

            preg_match("/\"data\": (?<json>.+)}]/m", $productBody, $output_array);

            $images = json_decode($output_array['json'] . '}]', true);

            foreach($images as $image){
                $product['images'][] = [
                    'image' => $image['full'],
                    'caption' => $image['caption'],
                ];
            }

            $product['description'] = html_entity_decode(str_get_html($productBody)->find('.description', 0)->plaintext);

            $this->products[] = $product;
        }

        print_r($this->products);
    }

    function get($url)
    {
        $cacheName = 'cache/' . basename($url);

        if(! file_exists($cacheName)){
            $body = file_get_contents($url);
            file_put_contents($cacheName, $body);
        }else{
            $body = file_get_contents($cacheName);
        }

        return $body;
    }
}
