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
$product = $parser->getProduct("https://hotline.ua/dacha_sad-shiny-pilnye/bosch-f016125689/");

var_dump($product);