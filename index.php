<?php

require 'lib/simple_html_dom.php';
require 'lib/Crawler3gorilla.php';
require 'lib/YiveApiClient.php';

$yiveClient = new YiveApiClient('DWoZ5y5Xm5y3LF5npw7VsttiSv8h5rESyz3FGvaBlkXN55bREM1JKp76QOijVeQIWdCPRsr13pUZO7LH');

$user = $yiveClient->get('/api/v1/user');

if (isset($user['email'])) {
    echo "YIVE API authenticated using ".$user['email'].PHP_EOL;
} else {
    echo "YIVE API authentication falied. Make sure your API keys are correct.".PHP_EOL;
    print_r($user);
    die;
}

if (file_exists('cache/campaign.json')) {
    $campaign = json_decode(file_get_contents('cache/campaign.json'), true);
}

if (isset($campaign) && isset($campaign['id'])) {
    echo "Using campaign `".$campaign['name']."`. id: ".$campaign['id'].PHP_EOL;
} else {
    $campaign = $yiveClient->post('/api/v1/campaign', [
        'name' => '3gorilla example campaign',
        'type' => 'custom_content',
        'text_overlay' => 1,
        'should_render' => 1,
        'distribution_type' => 'none',
        'audio' => 'bg_music_only',
        'videos_per_day' => 1,
        'upload_interval' => 60,
        'is_active' => 0, // keep the campaign deactivated for test
    ]);

    if (isset($campaign['id'])) {
        echo "Campaign `".$campaign['name']."` created. id: ".$campaign['id'].PHP_EOL;
        file_put_contents('cache/campaign.json', json_encode($campaign, JSON_PRETTY_PRINT));
    } else {
        print_r($campaign);
        die;
    }

    print_r($campaign);
}

// submit content
$crawler = new Crawler3gorilla('https://www.3gorillas.com/lifestyle.html');
$crawler->extract();
$products = $crawler->getProducts();

foreach ($products as $product) {

    $slides = [];

    foreach ($product['lines'] as $key => $line) {

        $url = isset($product['images'][$key]) ? $product['images'][$key] : $product['images'][array_rand($product['images'])];

        $slides[] = [
            'type' => 'image',
            'url' => $url,
            'text' => $line,
        ];
    }

    $video = [
        'campaign_id' => $campaign['id'],
        'title' => $product['title'],
        'description' => $product['title'],
        'slides' => $slides,
        'tags' => '',
        'skip_duplicate' => true,
    ];

    $response = $yiveClient->post('/api/v1/video', $video);

    if (isset($response['id'])) {
        echo "video `".$response['title']."` submitted. video_id: ".$response['id'].PHP_EOL;
    } else {
        echo "Error while submitting video".PHP_EOL;
        print_r($response);
    }
}