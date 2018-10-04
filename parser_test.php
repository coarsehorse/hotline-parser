<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 002 02.10.18
 * Time: 3:50 PM
 */

include "ShopParser.php";

$categoryNum = 5;
$subCategoryNum = 20;
$productsNum = 10;

/* Parse category links */
$rootPage = "https://hotline.ua";
$contents = file_get_contents($rootPage);
$dom = new DOMDocument();
@$dom->loadHTML($contents);
$xpath = new DOMXpath($dom);
$categoryLinks = $xpath->query(
    "//li[contains(concat(' ', normalize-space(@class), ' '), ' level-1 ' )]/a/@href");

/* Convert results to array of links */
$categories = array();
foreach ($categoryLinks as $link) {
    /* @var $link DOMElement */
    // skip links that is not subcategory
    $linkPart = $link->textContent;
    if (!stristr($linkPart, 'https')) {
        $categories[] = $rootPage . $linkPart;
    }
}
unset($link);

/* Take first 5 categories*/
$categories5 = array_slice($categories, 0, 1);

/* Parse subcategory pages */
$catSubcategories = array();

foreach ($categories5 as $cat) {
    $contents1 = file_get_contents($cat);
    $dom1 = new DOMDocument(); // can be removed?
    @$dom1->loadHTML($contents1);
    $xpath1 = new DOMXpath($dom1); // can be removed?
    $subCatLinks = $xpath1->query(
        "//ul[contains(concat(' ', normalize-space(@class), ' '), ' cell-navigation ' )]//li/a/@href");

    var_dump($contents);

//    foreach ($subCatLinks as $v) {
//        /* @var $v DOMElement */
//        echo "\"" . $v->textContent . "\"\n";
//    }


    /* Convert results to array of links */
//    $subCategories = array();
//    foreach ($subCatLinks as $link) {
//        /* @var $link DOMElement */
//        // skip links that is not subcategory
//        $linkPart = $link->textContent;
//        if (!stristr($linkPart, 'https')) {
//            $subCategories[] = $rootPage . $linkPart;
//        }
//    }
//    unset($link);
//
//    $catSubcategories[] = array("category" => $cat, "subcategories" => $subCategories);
}

//var_dump($catSubcategories);