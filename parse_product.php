<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 003 03.10.18
 * Time: 7:29 PM
 */

include "Product.php";

function parseProduct($link)
{
    // Getting product page
    $contents = file_get_contents($link);
    $dom = new DOMDocument();
    @$dom->loadHTML($contents);
    $xpath = new DOMXpath($dom);

    // Parse name
    $name = $xpath
        ->query("//h1[@datatype='card-title']");
    if ($name->length != 0) {
        $name = trim($name->item(0)->textContent);
    } else {
        $name = null;
    }

    // Parse category
    $category = $xpath
        ->query("//ul[contains(@class, 'breadcrumbs')]//li/a");
    if ($category->length != 0) {
        $category = trim($category->item(1)->textContent);
    } else {
        $category = null;
    }

    // Parse image
    // //img[contains(@data-type, 'photo')]
    $imageUrl = $xpath
        ->query("//div[contains(@class, 'gallery-box')]/img[contains(@class, 'img-product')]/@src");
    if ($imageUrl->length != 0) {
        $imageUrl = trim($imageUrl->item(0)->textContent);
    } else {
        $imageUrl = null;
    }

    // Parse price
    $price = null;
    $priceRange = $xpath
        ->query("//span[contains(@class, 'price-lg')]");
    if ($priceRange->length == 0) {
        $singlePrice = $xpath
            ->query("//div[contains(@class, 'resume-price')]//span[contains(@class, 'price-format')]");
        if ($singlePrice->length != 0) {
            $price = trim($singlePrice->item(0)->textContent);
        }
    } else {
        $price = trim($priceRange->item(0)->textContent);
    }

    // Parse brand
    $brand = $xpath
        ->query("//div[contains(text(), 'Производитель')]/..//p");
    if ($brand->length != 0) {
        $brand = trim($brand->item(0)->textContent);
    } else {
        $brand = null;
    }

    // Parse description
    $description = $xpath
        ->query("//div[contains(@class, 'text')]/p[@data-specification-box]");
    if ($description->length != 0) {
        $description = trim($description->item(0)->textContent);
    } else {
        $description = null;
    }

    // Parse characteristics
    $characteristics = array();
    $charQuery = $xpath->query("//div[@class='clearfix active']/div");
    if ($charQuery->length != 0) {
        foreach ($charQuery as $table) {
            /* @var $table DOMElement */
            foreach ($xpath->evaluate(".//div[@class='table-row']", $table) as $tableRow) {
                $cell_4 = $xpath->evaluate("./div[contains(@class, 'cell-4')]", $tableRow);
                $cell_8 = $xpath->evaluate("./div[contains(@class, 'cell-8')]", $tableRow);

                $characName = null;
                if ($cell_4->length != 0) {
                    $cellText = $xpath->evaluate("./text()", $cell_4->item(0));
                    if ($cellText->length != 0) {
                        $characName = trim($cellText->item(0)->textContent);
                    }
                }

                $characVal = null;
                if ($cell_8->length != 0) {
                    $characVal = trim($cell_8->item(0)->textContent);
                }

                $characteristics[] = array("name" => $characName, "value" => $characVal);
            }
        }
    }

    // Debug
    /*var_dump($name);
    var_dump($category);
    var_dump($imageUrl);
    var_dump($price);
    var_dump($brand);
    var_dump($description);
    foreach ($characteristics as $c) {
        echo "\"" . $c["name"] . "\": \"" . $c["value"] . "\"\n";
    }*/

    return new Product($name, $category, $imageUrl, $price, $brand, $description, $characteristics);
}

// Some tests
/*var_dump(parseProduct("https://hotline.ua/auto-gps-navigatory/garmin-streetpilot-2610/"));
echo "\n";
var_dump(parseProduct("https://hotline.ua/computer-myshi-klaviatury/kingston-hyperx-pulsefire-surge-usb-black-hx-mc002b/"));
echo "\n";
var_dump(parseProduct("https://hotline.ua/auto-deflektory-okon-vetroviki/auto-clover-deflektory-okon-autoclover-a078/"));*/