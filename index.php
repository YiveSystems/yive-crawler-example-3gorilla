<?php 

require 'lib/simple_html_dom.php';

if(! file_exists('cache/lifestyle.html')){
    $body = file_get_contents('https://www.3gorillas.com/lifestyle.html');
    file_put_contents('cache/lifestyle.html', $html);
}else{
    $body = file_get_contents('cache/lifestyle.html');
}

$html = str_get_html($body);

$products = [];

foreach($html->find('.product-item-link') as $a){

    $productUrl = trim($a->href);
    $cacheName = 'cache/' . basename($productUrl);

    $product = [
        'images' => [],
        'link' => $productUrl,
        'title' => trim($a->plaintext),
    ];

    if(! file_exists($cacheName)){
        $productBody = file_get_contents($productUrl);
        file_put_contents($cacheName, $productBody);
    }else{
        $productBody = file_get_contents($cacheName);
    }

    preg_match("/\"data\": (?<json>.+)}]/m", $productBody, $output_array);

    $images = json_decode($output_array['json'] . '}]', true);

    foreach($images as $image){
        $product['images'][] = [
            'image' => $image['full'],
            'caption' => $image['caption'],
        ];
    }

    $product['description'] = html_entity_decode(str_get_html($productBody)->find('.description', 0)->plaintext);

    $products[] = $product;
}

print_r($products);