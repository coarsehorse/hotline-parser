<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 5:55 AM
 */

include_once "../parser/HotlineParser.php";

$parser = new HotlineParser();

/*$cat = $parser->getCategories(1);

var_dump($cat);*/

// range price
//$product = $parser->getProduct("https://hotline.ua/fashion-gorodskie-ryukzaki/xd-design-bobby-anti-theft-backpack-156-black-p705541/");
// single price
//$product = $parser->getProduct("https://hotline.ua/dacha_sad-shiny-pilnye/bosch-f016125689/");
//
//var_dump($product);

// Images test
//$productFew = $parser->getProduct("https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/samsung-galaxy-note-9-n9600-8512gb-ocean-blue/");
//var_dump($productFew);
//$productSingle = $parser->getProduct("https://hotline.ua/auto-nasosy-i-kompressory/dorozhnaya-karta-4905826218/");
//var_dump($productSingle);

$productSingle = $parser->getProduct("https://hotline.ua/mobile-umnye-chasy-smartwatch/amazfit-stratos-2s-black/");
var_dump($productSingle);
