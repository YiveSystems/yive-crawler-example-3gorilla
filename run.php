<?php
/**
 * Crawler example for 3gorillas.com to submit content at YIVE
 * @author NasirNobin <nasir@yivesystems.com>
 * @version 0.1
 */

// we are going to use simple_html_dom to parse HTML */
require 'lib/simple_html_dom.php';

// 3gorilla Crawler */
require 'lib/Crawler3gorilla.php';

// Basic YiveApiClient
require 'lib/YiveApiClient.php';

// initialize YIVE API CLient using API key
$yiveClient = new YiveApiClient('DWoZ5y5Xm5y3LF5npw7VsttiSv8h5rESyz3FGvaBlkXN55bREM1JKp76QOijVeQIWdCPRsr13pUZO7LH');

// hit `/api/v1/user` to make sure API token is valid
$user = $yiveClient->get('/api/v1/user');

// if response has email, then token is valid, exit otherwise
if (isset($user['email'])) {
    echo "YIVE API authenticated using ".$user['email'].PHP_EOL;
} else {
    echo "YIVE API authentication falied. Make sure your API keys are correct.".PHP_EOL;
    print_r($user);
    die;
}

// we'll cache the campaign data at `cache/campaign.json` to avoid creating campaig on each run
if (file_exists('cache/campaign.json')) {
    $campaign = json_decode(file_get_contents('cache/campaign.json'), true);
}

if (isset($campaign) && isset($campaign['id'])) {
    echo "Using campaign `".$campaign['name']."`. id: ".$campaign['id'].PHP_EOL;
} else {

    // create a new campaign
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

    // make sure campaign was created successfully, exit otherwise
    if (isset($campaign['id'])) {
        echo "Campaign `".$campaign['name']."` created. id: ".$campaign['id'].PHP_EOL;
        file_put_contents('cache/campaign.json', json_encode($campaign, JSON_PRETTY_PRINT));
    } else {
        print_r($campaign);
        die;
    }
}

// crawl content from `https://www.3gorillas.com/lifestyle.html`
$crawler = new Crawler3gorilla('https://www.3gorillas.com/lifestyle.html');
$crawler->extract();
$products = $crawler->getProducts();

foreach ($products as $product) {

    $slides = [];

    // loop over product lines and insert each line as a slide
    foreach ($product['lines'] as $key => $line) {

        // set image url for each slide
        // set first try to get images on default sequence,
        // but if we have less image, pick random image for that slide
        $url = isset($product['images'][$key]) ? $product['images'][$key] : $product['images'][array_rand($product['images'])];

        $slides[] = [
            'type' => 'image',
            'url' => $url,
            'text' => $line,
        ];
    }

    // video data for submission
    $video = [
        'campaign_id' => $campaign['id'],
        'title' => $product['title'],
        'description' => $product['title'],
        'slides' => $slides,
        'tags' => '',
        'skip_duplicate' => true,
    ];

    // send to YIVE
    $response = $yiveClient->post('/api/v1/video', $video);

    // check if submission was successful
    if (isset($response['id'])) {
        echo "video `".$response['title']."` submitted. video_id: ".$response['id'].PHP_EOL;
    } else {
        echo "Error while submitting video".PHP_EOL;
        print_r($response);
    }
}