<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 010 10.10.18
 * Time: 5:14 AM
 */


$url = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/samsung-galaxy-note-9-n9600-8512gb-ocean-blue/";

// Get csrf-token token from usual session
// Prepare curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch))
    throw new Exception(curl_error($ch));

$dom = new DomDocument();
@$dom->loadHTML($response);
$xpath = new DOMXpath($dom);

$tokenQuery = $xpath->query("//meta[@name='csrf-token']/@content");
if ($tokenQuery->length != 0) {
    $token = $tokenQuery->item(0)->textContent;
} else {
    throw new Exception("Failed to parse x-csrf-token on " . $url);
}

// Make POST request to the shop API to get full images
curl_setopt($ch, CURLOPT_URL, $url . "get-product-gallery-content/");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-csrf-token: ' . $token));

$response = curl_exec($ch);

if (curl_errno($ch))
    throw new Exception(curl_error($ch));

// Parse response
@$dom->loadHTML(json_decode($response, true)["data"]);
$xpath = new DOMXpath($dom);

// Get image URLs
$imagesQuery = $xpath->query("//img/@data-gallery-image");
$images = [];

if ($imagesQuery->length != 0) {
    foreach ($imagesQuery as $image) {
        $images[] = $image->textContent;
    }
}

var_dump($images);