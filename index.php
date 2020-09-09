<?php 

require 'lib/simple_html_dom.php';
require 'lib/Crawler3gorilla.php';

$crawler = new Crawler3gorilla('https://www.3gorillas.com/lifestyle.html');

$crawler->extract();

/**
 * @flow
 * 1. it will create a campaign and keep the campaign it on cache
 * 2. it will submit all videos from a category and keep track of the submitted urls
 */ 
